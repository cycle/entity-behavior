<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Cycle\Database\DatabaseInterface;

interface UuidTypecastInterface
{
    public static function cast(string $value, DatabaseInterface $database): UuidInterface;
}
