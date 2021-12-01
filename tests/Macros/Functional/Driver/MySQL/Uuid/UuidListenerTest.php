<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\MySQL\Uuid;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Uuid\UuidListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class UuidListenerTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
