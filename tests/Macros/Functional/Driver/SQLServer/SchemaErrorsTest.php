<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\SQLServer;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\SchemaErrorsTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class SchemaErrorsTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
