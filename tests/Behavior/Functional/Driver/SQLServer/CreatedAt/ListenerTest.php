<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\SQLServer\CreatedAt;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\CreatedAt\ListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class ListenerTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
