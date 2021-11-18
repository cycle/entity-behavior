<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Postgres\Listener;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Listener\ListenerClassTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class ListenerClassTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
