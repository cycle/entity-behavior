<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\SQLite\CreatedAt;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\CreatedAt\CreatedAtTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class CreatedAtTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}