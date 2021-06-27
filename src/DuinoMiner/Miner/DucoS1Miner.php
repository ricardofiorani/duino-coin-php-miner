<?php declare(strict_types=1);

/**
 * Main Duino-Coin algorithm (DUCO-S1 (-S1A)) is based on SHA1.
 * For more info please see the white paper
 * @link https://github.com/revoxhere/duino-coin/blob/gh-pages/assets/whitepaper.pdf
 */

namespace RicardoFiorani\DuinoMiner\Miner;

class DucoS1Miner implements MinerInterface
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

    public function getName() : string
    {
        return 'DUCO-S1-PHP-Miner';
    }
}
