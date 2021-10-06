<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Dispatcher;

/**
 * Provides events list that should be listened by the behavior Listener
 */
interface EventListProviderInterface
{
    /**
     * @return array<int, array{string, string}> List of tuples [event, class method]
     */
    public function getEventsList(): array;
}
