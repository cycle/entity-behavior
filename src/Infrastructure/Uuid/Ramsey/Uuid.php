<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey;

use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * @psalm-immutable
 */
class Uuid extends RamseyUuid implements UuidInterface
{
    private static ?UuidFactory $factory = null;

    public static function getFactory(): UuidFactory
    {
        if (self::$factory === null) {
            self::$factory = new UuidFactory();
        }

        return self::$factory;
    }

    /**
     * @psalm-pure
     * @psalm-suppress ImpureMethodCall, LessSpecificReturnStatement, MoreSpecificReturnType
     */
    public static function fromString(string $uuid): UuidInterface
    {
        return self::getFactory()->fromString($uuid);
    }
}
