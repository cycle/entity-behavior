<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Postgres\OptimisticLock;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\OptimisticLock\InquiredRelationTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class InquiredRelationTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
