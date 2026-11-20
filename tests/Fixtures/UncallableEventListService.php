<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures;

use Cycle\ORM\Entity\Behavior\Dispatcher\EventListProviderInterface;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;

/**
 * Names a method it does not have in {@see getEventsList()}.
 */
class UncallableEventListService implements EventListProviderInterface
{
    public function getEventsList(): array
    {
        return [
            [OnCreate::class, 'missing'],
        ];
    }
}
