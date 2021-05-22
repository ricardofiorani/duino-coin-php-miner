<?php declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RicardoFiorani\DuinoMiner\Configuration;
use RicardoFiorani\DuinoMiner\DucoS1Miner;

/**
 * Configuration
 */
$poolIp = '51.15.127.80'; //https://raw.githubusercontent.com/revoxhere/duino-coin/gh-pages/serverip.txt
$poolPort = 2811; //https://raw.githubusercontent.com/revoxhere/duino-coin/gh-pages/serverip.txt
$username = 'ricardofiorani';

$configuration = new Configuration("$poolIp:$poolPort", $username);

/**
 * Logging
 */
$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('php://stdout'));
$logger->alert('STARTING PHP MINER FOR USER');

/**
 * Miner logic
 */
$loop = React\EventLoop\Factory::create();
$connector = new React\Socket\Connector($loop);
$miner = new DucoS1Miner();

while (true) {
    $logger->alert(sprintf('Connecting to %s...', $configuration->getPoolAddress()));
    $connector->connect($configuration->getPoolAddress())->then(
        function (React\Socket\ConnectionInterface $connection) use ($logger, $miner, $configuration) {
            // connection successfully established
            $connection->on('data', function ($data) use ($connection, $logger) {
                $logger->info('SERVER SENT: ' . $data);

                $arguments = explode(',', $data);
                $event = array_shift($arguments);
                $event = str_replace(array("\r", "\n"), '', $event);

                if (in_array($event, ['OK', '2.4', 'GOOD', 'BAD'])) {
                    $connection->emit($event, $arguments);
                    $logger->info('EMITTING: ' . $event);
                    return;
                }

                //Then we know its a job
                $connection->emit('JOB_INCOMING', explode(',', $data));
            });

            $connection->on('close', function () use ($logger) {
                $logger->warning('[SOCKET CLOSED]');
            });

            //Based on server response
            $connection->on('OK', function () use ($connection) {
                $connection->emit('TIME_FOR_NEW_JOB');
            });

            $connection->on('TIME_FOR_NEW_JOB', function () use ($connection, $configuration) {
                $connection->write(sprintf("JOB,%s,MEDIUM", $configuration->getUsername()));
            });

            $connection->on('GOOD', function () use ($connection, $logger) {
                $logger->info('Share Accepted!');
                $connection->emit('TIME_FOR_NEW_JOB');
            });

            $connection->on('BAD', function () use ($connection, $logger) {
                $logger->warning('Share NOT ACCEPTED!');
                $connection->emit('TIME_FOR_NEW_JOB');
            });

            $connection->on('JOB_INCOMING', function ($baseHash, $digest, $difficulty) use ($connection, $logger, $miner) {
                $logger->info("Starting to work on {$baseHash}:{$digest}:{$difficulty}");

                $start = microtime(true);
                $result = $miner->work((string)$baseHash, (string)$digest, (int)$difficulty);
                $timeDifference = microtime(true) - $start;

                $logger->debug('Time difference', [$timeDifference]);

                if ($result < 0) {
                    $logger->alert('Hash Not Found :(');
                    $connection->emit('TIME_FOR_NEW_JOB');
                    return;
                }

                $logger->info('FOUND!', ['result' => $result]);

                $hashRate = $result / ($timeDifference);
                $logger->info('Current HashRate: ' . round($hashRate / 1000, 2) . ' KH/s');
                $connection->write("$result,$hashRate,{$miner->getName()}");
            });

            //The knot that ties everything together
            $connection->emit('TIME_FOR_NEW_JOB');
        },
        function (Exception $error) use ($logger) {
            $logger->error($error->getMessage());
        }
    );

    $loop->run();
}
