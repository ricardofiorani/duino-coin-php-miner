<?php declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RicardoFiorani\DuinoMiner\ConfigurationFactory;
use RicardoFiorani\DuinoMiner\Logic\DuinoMinerLogic;
use RicardoFiorani\DuinoMiner\Miner\DucoS1Miner;

/**
 * Configuration
 */


$username = 'ricardofiorani';

$configurationFactory = new ConfigurationFactory('https://server.duinocoin.com/getPool');
$configuration = $configurationFactory->createConfiguration($username);
/**
 * Logging
 */
$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('php://stdout'));
$logger->alert('STARTING PHP MINER', ['user' => $configuration->getUsername()]);

/**
 * Miner logic
 */
$loop = React\EventLoop\Factory::create();
$connector = new React\Socket\Connector($loop, ['timeout' => 200]);
$algorithm = new DucoS1Miner();
$miner = new DuinoMinerLogic();

while (true) {
    $miner->run($logger, $loop, $connector, $algorithm, $configuration);
}
