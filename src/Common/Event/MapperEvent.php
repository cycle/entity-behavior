<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Common\Event;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\MapperInterface;

abstract class MapperEvent
{
    public \DateTimeImmutable $dispatchedAt;

    public function __construct(
        public string $role,
        public MapperInterface $mapper,
        public object $entity,
        public Node $node,
        public State $state,
    ) {
        $this->dispatchedAt = new \DateTimeImmutable();
    }
}
