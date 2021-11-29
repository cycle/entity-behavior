<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Common\Event\Mapper;

use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Entity\Macros\Common\Event\MapperEvent;

abstract class QueueCommand extends MapperEvent
{
    public ?CommandInterface $command;
}
