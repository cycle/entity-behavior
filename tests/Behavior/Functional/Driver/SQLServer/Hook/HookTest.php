<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\SQLServer\Hook;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Hook\HookTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class HookTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
