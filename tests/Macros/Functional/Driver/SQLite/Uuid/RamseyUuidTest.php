<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\SQLite\Uuid;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Uuid\RamseyUuidTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class RamseyUuidTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
