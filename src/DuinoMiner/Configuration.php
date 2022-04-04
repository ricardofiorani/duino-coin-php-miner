<?php

namespace RicardoFiorani\DuinoMiner;

use RicardoFiorani\DuinoMiner\Connectivity\Pool;

class Configuration
{
    private Pool $pool;
    private string $username;

    public function __construct(Pool $pool, string $username)
    {
        $this->pool = $pool;
        $this->username = $username;
    }

    public function getPool(): Pool
    {
        return $this->pool;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
