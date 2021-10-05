<?php

declare(strict_types=1);

namespace Cycle\SmartMapper\Event;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\MapperInterface;

abstract class MapperEvent
{
    public function __construct(
        public string $role,
        public MapperInterface $mapper,
        public object $entity,
        public Node $node,
        public State $state,
    ) {
    }
}
