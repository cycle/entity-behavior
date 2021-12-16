<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\SQLServer\Schema;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Schema\ErrorsTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class ErrorsTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
