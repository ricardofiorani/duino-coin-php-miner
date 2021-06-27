<?php declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RicardoFiorani\DuinoMiner\Configuration;
use RicardoFiorani\DuinoMiner\Logic\DuinoMinerLogic;
use RicardoFiorani\DuinoMiner\Miner\DucoS1Miner;

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
