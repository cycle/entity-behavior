<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\MySQL\OptimisticLock;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\OptimisticLock\OptimisticLockTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class OptimisticLockTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
