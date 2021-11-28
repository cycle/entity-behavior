<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures;

use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\UuidInterface;

class User
{
    public UuidInterface $uuid;
}
