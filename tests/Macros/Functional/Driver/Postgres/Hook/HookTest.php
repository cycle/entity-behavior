<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Postgres\Hook;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Hook\HookTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class HookTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
