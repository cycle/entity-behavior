<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Postgres\CreatedAt;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\CreatedAt\CreatedAtTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class CreatedAtTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
