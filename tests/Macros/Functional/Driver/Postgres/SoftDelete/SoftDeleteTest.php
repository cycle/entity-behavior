<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Postgres\SoftDelete;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\SoftDelete\SoftDeleteTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class SoftDeleteTest extends CommonClass
{
    public const DRIVER = 'postgres';
}