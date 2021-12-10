<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Postgres\EventListener;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\EventListener\EventListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class EventListenerTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
