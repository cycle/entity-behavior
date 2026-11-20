<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures;

use Cycle\ORM\Entity\Behavior\Dispatcher\EventListProviderInterface;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;

/**
 * Returns an event without a method name from {@see getEventsList()}.
 */
class BrokenEventListService implements EventListProviderInterface
{
    public function getEventsList(): array
    {
        return [
            [OnCreate::class],
        ];
    }
}
