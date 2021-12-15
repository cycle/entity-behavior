<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Event\Mapper;

use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Entity\Behavior\Event\MapperEvent;

abstract class QueueCommand extends MapperEvent
{
    public ?CommandInterface $command;
}
