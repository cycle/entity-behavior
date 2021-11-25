<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\SQLServer\Timestamped;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Timestamped\DeletedAtTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class DeletedAtTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
