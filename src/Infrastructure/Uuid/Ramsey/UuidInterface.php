<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey;

use Cycle\ORM\Entity\Macros\Uuid\UuidInterface as EntityMacrosUuidInterface;
use Ramsey\Uuid\UuidInterface as RamseyUuidInterface;

interface UuidInterface extends EntityMacrosUuidInterface, RamseyUuidInterface
{
}
