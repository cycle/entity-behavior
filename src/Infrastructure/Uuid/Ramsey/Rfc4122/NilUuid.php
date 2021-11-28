<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Rfc4122;

use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Uuid;

/**
 * The nil UUID is special form of UUID that is specified to have all 128 bits
 * set to zero
 *
 * @psalm-immutable
 */
final class NilUuid extends Uuid
{
}
