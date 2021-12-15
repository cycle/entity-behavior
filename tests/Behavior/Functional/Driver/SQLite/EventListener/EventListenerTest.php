<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\SQLite\EventListener;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\EventListener\EventListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class EventListenerTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
