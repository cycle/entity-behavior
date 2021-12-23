<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Event;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\MapperInterface;
use Cycle\ORM\Select\Source;

/**
 * @internal
 *
 * Don't listen to this event
 */
abstract class MapperEvent
{
    public function __construct(
        public string $role,
        public MapperInterface $mapper,
        public object $entity,
        public Node $node,
        public State $state,
        public Source $source,
        public \DateTimeImmutable $timestamp
    ) {
    }
}
