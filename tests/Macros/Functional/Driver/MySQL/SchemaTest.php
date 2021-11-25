<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\MySQL;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\SchemaTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class SchemaTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
