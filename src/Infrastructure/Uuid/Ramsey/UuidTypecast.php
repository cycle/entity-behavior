<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey;

use Cycle\Database\DatabaseInterface;
use Cycle\ORM\Entity\Macros\Uuid\UuidTypecastInterface;
use Cycle\ORM\Entity\Macros\Uuid\UuidInterface;

class UuidTypecast implements UuidTypecastInterface
{
    public static function cast(string $value, DatabaseInterface $database): UuidInterface
    {
        return Uuid::fromString($value);
    }
}
