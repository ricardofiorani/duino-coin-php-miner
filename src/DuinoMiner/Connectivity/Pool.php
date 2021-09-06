<?php declare(strict_types=1);

namespace RicardoFiorani\DuinoMiner\Connectivity;

class Pool
{
    private string $name;
    private string $ip;
    private int $port;

    public function __construct(string $name, string $ip, int $port)
    {
        $this->name = $name;
        $this->ip = $ip;
        $this->port = $port;
    }

    public function getAddress(): string
    {
        return sprintf('%s:%s', $this->getIp(), $this->getPort());
    }

    public function getDescriptiveName(): string
    {
        return sprintf('%s - %s', $this->getName(), $this->getAddress());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getPort(): int
    {
        return $this->port;
    }
}
