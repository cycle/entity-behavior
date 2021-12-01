<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Cycle\Database\DatabaseInterface;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

class UuidTypecast
{
    public static function cast(string $value, DatabaseInterface $database): UuidInterface
    {
        return Uuid::fromString($value);
    }
}
