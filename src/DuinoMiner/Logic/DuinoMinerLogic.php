<?php declare(strict_types=1);

namespace RicardoFiorani\DuinoMiner\Logic;

use Exception;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\ConnectorInterface;
use RicardoFiorani\DuinoMiner\Configuration;
use RicardoFiorani\DuinoMiner\Miner\MinerInterface;
use RicardoFiorani\DuinoMiner\Printer\HashRatePrinter;

class DuinoMinerLogic
{
    public function run(LoggerInterface $logger, LoopInterface $loop, ConnectorInterface $connector, MinerInterface $algorithm, Configuration $configuration)
    {
        $logger->alert(sprintf('Connecting to %s...', $configuration->getPool()->getDescriptiveName()));

        $connector->connect($configuration->getPool()->getAddress())->then(
            function (ConnectionInterface $connection) use ($logger, $algorithm, $configuration) {
                // connection successfully established
                $connection->on('data', function ($data) use ($connection, $logger) {
                    $logger->info('POOL SENT DATA', [$data]);

                    $arguments = explode(',', $data);
                    $event = array_shift($arguments);
                    $event = str_replace(array("\r", "\n"), '', $event);

                    if (in_array($event, ['OK', 'GOOD', 'BAD'])) {
                        $connection->emit($event, $arguments);
                        $logger->info('EMITTING: ' . $event);
                        return;
                    }

                    //We need to explode again because array_shift changes the original $arguments
                    $miningArguments = explode(',', $data);

                    if (count($miningArguments) < 2) {
                        $logger->info('Connected to pool version: ' . floatval($event));

                        return;
                    }

                    //Then we know its a job
                    $connection->emit('JOB_INCOMING', $miningArguments);
                });

                $connection->on('close', function () use ($logger) {
                    $logger->warning('[SOCKET CLOSED]');
                });

                //Based on pool response
                $connection->on('OK', function () use ($connection) {
                    $connection->emit('TIME_FOR_NEW_JOB');
                });

                $connection->on('TIME_FOR_NEW_JOB', function () use ($connection, $configuration, $logger) {
                    $logger->info('Asking pool for a new job...');
                    $connection->write(sprintf("JOB,%s,MEDIUM", $configuration->getUsername()));
                });

                $connection->on('GOOD', function () use ($connection, $logger) {
                    $logger->info('Share Accepted!');
                    $connection->emit('TIME_FOR_NEW_JOB');
                });

                $connection->on('BLOCK', function () use ($connection, $logger) {
                    $logger->info('Block FOUND');
                    $connection->emit('TIME_FOR_NEW_JOB');
                });

                $connection->on('BAD', function () use ($connection, $logger) {
                    $logger->warning('Share NOT ACCEPTED!');
                    $connection->emit('TIME_FOR_NEW_JOB');
                });

                $connection->on('JOB_INCOMING', function ($baseHash, $digest, $difficulty) use ($connection, $logger, $algorithm) {
                    $logger->info("Starting to work", ['baseHash' => $baseHash, 'digest' => $digest, 'difficulty' => $difficulty]);

                    $start = microtime(true);
                    $result = $algorithm->work((string)$baseHash, (string)$digest, (int)$difficulty);
                    $timeDifference = microtime(true) - $start;

                    $logger->debug('Time difference', [$timeDifference]);

                    if ($result < 0) {
                        $logger->alert('Hash Not Found :(');
                        $connection->emit('TIME_FOR_NEW_JOB');
                        return;
                    }

                    $logger->info('FOUND!', ['result' => $result]);

                    $hashRate = $result / ($timeDifference);
                    $logger->info('Current HashRate', ['hashRate' => HashRatePrinter::print($hashRate)]);
                    $connection->write("$result,$hashRate,{$algorithm->getName()}");
                });

                //The knot that ties everything together
                $connection->emit('TIME_FOR_NEW_JOB');
            },
            function (Exception $error) use ($logger) {
                $logger->error($error->getMessage());
            }
        );

        $loop->run();

        $logger->alert('Sleeping 10 seconds before trying again...');

        foreach (range(10, 0) as $i) {
            sleep(1);
            $logger->info(sprintf('%s seconds before trying again.', $i));
        }
    }
}
