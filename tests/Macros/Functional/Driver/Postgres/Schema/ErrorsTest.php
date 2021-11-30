<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Postgres\Schema;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Schema\ErrorsTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class ErrorsTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
