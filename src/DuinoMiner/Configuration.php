<?php

namespace RicardoFiorani\DuinoMiner;

class Configuration
{
    private string $poolAddress;
    private string $username;

    public function __construct(string $poolAddress, string $username)
    {
        $this->poolAddress = $poolAddress;
        $this->username = $username;
    }

    public function getPoolAddress(): string
    {
        return $this->poolAddress;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
