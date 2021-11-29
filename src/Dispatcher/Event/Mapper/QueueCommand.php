<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Dispatcher\Event\Mapper;

use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Entity\Macros\Dispatcher\Event\MapperEvent;

abstract class QueueCommand extends MapperEvent
{
    public ?CommandInterface $command;
}
