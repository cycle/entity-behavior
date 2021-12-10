<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Postgres\OptimisticLock;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\OptimisticLock\OptimisticLockTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class OptimisticLockTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
