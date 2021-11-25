<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Subscriber;

use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Event\Mapper\QueueCommand;

final class CallableSubscriberListener
{
    public function __construct(
        private array $callable,
        private array $events
    ) {
    }

    #[Listen(QueueCommand::class)]
    public function __invoke(QueueCommand $event): void
    {
        if (!\in_array($event::class, $this->events, true)) {
            return;
        }

        \call_user_func(count($this->callable) === 1 ? $this->callable[0] : $this->callable, $event);
    }
}
