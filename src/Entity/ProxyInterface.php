<?php

namespace Retrowaver\ProxyChecker\Entity;

interface ProxyInterface
{
    /**
     * @return string
     */
    public function getIp(): string;

    /**
     * @param string $ip
     * @return ProxyInterface
     */
    public function setIp(string $ip): ProxyInterface;

    /**
     * @return int
     */
    public function getPort(): int;

    /**
     * @param int $port
     * @return ProxyInterface
     */
    public function setPort(int $port): ProxyInterface;

    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @param string $username
     * @return ProxyInterface
     */
    public function setUsername(string $username): ProxyInterface;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @param string $password
     * @return ProxyInterface
     */
    public function setPassword(string $password): ProxyInterface;

    /**
     * @return string
     */
    public function getProtocol(): string;

    /**
     * @param string $protocol
     * @return ProxyInterface
     */
    public function setProtocol(string $protocol): ProxyInterface;
}
