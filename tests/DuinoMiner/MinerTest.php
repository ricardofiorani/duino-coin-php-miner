<?php declare(strict_types=1);

namespace RicardoFiorani\Tests\DuinoMiner;

use PHPUnit\Framework\TestCase;
use RicardoFiorani\DuinoMiner\Miner\DucoS1Miner;

class MinerTest extends TestCase
{
    public function testMiner(): void
    {
        //Setup
        $baseHash = 'b60e5f87fdd9be241d006a69125c32be3d60e4b4';
        $digest = '3ec9fff925c1326d4577b21f7ca4fdcabb57488d';
        $difficulty = 50000;
        $expectedSolution = 2464655;

        $miner = new DucoS1Miner();

        TestCase::assertEquals($expectedSolution, $miner->work($baseHash, $digest, $difficulty));
    }
}
