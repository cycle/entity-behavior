<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Utils;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

class TestLogger implements LoggerInterface
{
    use LoggerTrait;

    private $enabled;

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function log(mixed $level, mixed $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }

        if ($level === LogLevel::ERROR) {
            echo " \n! \033[31m" . $message . "\033[0m";
        } elseif ($level === LogLevel::ALERT) {
            echo " \n! \033[35m" . $message . "\033[0m";
        } elseif (strpos($message, 'SHOW') === 0) {
            echo " \n> \033[34m" . $message . "\033[0m";
        } elseif (strpos($message, 'SELECT') === 0) {
            echo " \n> \033[32m" . $message . "\033[0m";
        } else {
            echo " \n> \033[33m" . $message . "\033[0m";
        }
    }
}
