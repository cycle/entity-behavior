<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Exception\OptimisticLock;

use Cycle\ORM\Heap\Node;

class RecordIsLockedException extends OptimisticLockException
{
    public function __construct(Node $node)
    {
        $message = sprintf('The `%s` record is locked.', $node->getRole());

        parent::__construct($message);
    }
}
