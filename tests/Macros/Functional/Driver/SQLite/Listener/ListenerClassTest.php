<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\SQLite\Listener;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Listener\ListenerClassTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class ListenerClassTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
