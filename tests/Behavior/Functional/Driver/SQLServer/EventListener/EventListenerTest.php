<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\SQLServer\EventListener;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\EventListener\EventListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class EventListenerTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
