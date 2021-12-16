<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\MySQL\UpdatedAt;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\UpdatedAt\ListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class ListenerTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
