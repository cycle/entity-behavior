<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Listener;

use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\QueueCommand;

final class Hook
{
    /** @var callable */
    private $callable;

    public function __construct(
        callable $callable,
        private array $events
    ) {
        $this->callable = $callable;
    }

    #[Listen(QueueCommand::class)]
    public function __invoke(QueueCommand $event): void
    {
        if (!\in_array($event::class, $this->events, true)) {
            return;
        }

        \call_user_func($this->callable, $event);
    }
}
