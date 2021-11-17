<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Postgres\Timestamped;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Timestamped\UpdatedAtTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class UpdatedAtTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
