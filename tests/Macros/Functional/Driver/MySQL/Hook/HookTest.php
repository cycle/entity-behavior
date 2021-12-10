<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\MySQL\Hook;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Hook\HookTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class HookTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
