<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Utils;

use Closure;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

final class SimpleContainer implements ContainerInterface
{
    private array $definitions;
    private Closure $factory;

    /**
     * @param array $definitions
     * @param Closure|null $factory Should be closure that works like ContainerInterface::get(string $id): mixed
     */
    public function __construct(array $definitions = [], Closure $factory = null)
    {
        $this->definitions = $definitions;
        $this->factory = $factory ?? static function (string $id): void {
                throw new class ("No definition or class found for \"$id\".")
                    extends Exception
                    implements NotFoundExceptionInterface {
                };
            };
    }

    public function get($id): mixed
    {
        if (!\array_key_exists($id, $this->definitions)) {
            $this->definitions[$id] = ($this->factory)($id);
        }
        return $this->definitions[$id];
    }

    public function has($id): bool
    {
        if (\array_key_exists($id, $this->definitions)) {
            return true;
        }
        try {
            $this->get($id);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
}
