<?php

declare(strict_types=1);

namespace Cycle\SmartMapper\Event\Mapper;

use Cycle\ORM\Command\CommandInterface;
use Cycle\SmartMapper\Event\MapperEvent;

abstract class QueueCommand extends MapperEvent
{
    public ?CommandInterface $command;
}
