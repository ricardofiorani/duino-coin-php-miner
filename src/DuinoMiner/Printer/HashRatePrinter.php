<?php declare(strict_types=1);

namespace RicardoFiorani\DuinoMiner\Printer;

class HashRatePrinter
{
    public static function print(float $hashRate): string
    {
        return match (true) {
            $hashRate > 99999 => round($hashRate / 100000, 2) . ' Gh/s',
            $hashRate > 9999 => round($hashRate / 10000, 2) . ' Mh/s',
            $hashRate > 999 => round($hashRate / 1000, 2) . ' Kh/s',
            default => $hashRate . ' h/s',
        };
    }
}
