<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\SQLServer\Schema;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Schema\RegistryModifierTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class RegistryModifierTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
