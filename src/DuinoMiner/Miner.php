<?php declare(strict_types=1);

namespace RicardoFiorani\DuinoMiner;

class Miner
{
    public function work(string $baseHash, string $digest, int $difficulty): int
    {
        for ($i = 0; $i < 100 * $difficulty + 1; $i++) {
            $sha = sha1($baseHash . $i);

            if ($sha == $digest) {
                return $i;
            }
        }

        return -1;
    }
}
