<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Event\Mapper;

use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Entity\Macros\Event\MapperEvent;

abstract class QueueCommand extends MapperEvent
{
    public ?CommandInterface $command;
}
