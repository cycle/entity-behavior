<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\MySQL\Subscriber;

// phpcs:ignore
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Subscriber\CallableSubscriberTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class CallableSubscriberTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
