<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\SQLite\Hook;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Hook\HookTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class HookTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
