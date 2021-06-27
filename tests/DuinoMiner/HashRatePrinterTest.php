<?php declare(strict_types=1);

namespace RicardoFiorani\Tests\DuinoMiner;

use PHPUnit\Framework\TestCase;
use RicardoFiorani\DuinoMiner\Miner\DucoS1Miner;
use RicardoFiorani\DuinoMiner\Printer\HashRatePrinter;

class HashRatePrinterTest extends TestCase
{
    /**
     * @dataProvider hashRateProvider
     */
    public function testPrint($input, $expected): void
    {
        TestCase::assertEquals($expected, HashRatePrinter::print($input));
    }

    public function hashRateProvider(): array
    {
        return [
            [999, '999 h/s'],
            [1000, '1 Kh/s'],
            [10000, '1 Mh/s'],
            [100000, '1 Gh/s'],
            [1000000, '10 Gh/s'],
            [251.3, '251.3 h/s'],
            [999.9999, '1 Kh/s'],
        ];
    }
}
