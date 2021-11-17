<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\SQLServer\Timestamped;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Timestamped\CreatedAtTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class CreatedAtTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
