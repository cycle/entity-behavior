<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\SQLServer\Subscriber;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Subscriber\CallableSubscriberTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class CallableSubscriberTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
