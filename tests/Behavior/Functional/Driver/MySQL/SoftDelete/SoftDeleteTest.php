<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\MySQL\SoftDelete;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\SoftDelete\SoftDeleteTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class SoftDeleteTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
