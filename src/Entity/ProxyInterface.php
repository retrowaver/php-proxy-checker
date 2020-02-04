<?php

namespace Retrowaver\ProxyChecker\Entity;

interface ProxyInterface
{
    public function getIp(): string;
    public function setIp(string $ip): ProxyInterface;

    public function getPort(): int;
    public function setPort(int $port): ProxyInterface;

    public function getUsername(): string;
    public function setUsername(string $username): ProxyInterface;

    public function getPassword(): string;
    public function setPassword(string $password): ProxyInterface;

    public function getProtocol(): string;
    public function setProtocol(string $protocol): ProxyInterface;
}
