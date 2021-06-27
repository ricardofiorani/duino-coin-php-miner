<?php declare(strict_types=1);

namespace RicardoFiorani\DuinoMiner\Miner;

interface MinerInterface
{
    public function work(string $baseHash, string $digest, int $difficulty): int;

    public function getName(): string;
}
