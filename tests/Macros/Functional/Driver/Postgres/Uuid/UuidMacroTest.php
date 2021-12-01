<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Postgres\Uuid;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Uuid\UuidMacroTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class UuidMacroTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
