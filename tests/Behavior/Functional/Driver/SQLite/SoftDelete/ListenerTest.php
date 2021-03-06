<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\SQLite\SoftDelete;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\SoftDelete\ListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class ListenerTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
