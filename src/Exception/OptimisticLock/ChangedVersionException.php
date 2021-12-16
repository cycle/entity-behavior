<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Exception\OptimisticLock;

class ChangedVersionException extends OptimisticLockException
{
    public function __construct(mixed $old, mixed $new)
    {
        parent::__construct(sprintf('Record version change detected. Old value `%s`, a new value `%s`.', $old, $new));
    }
}
