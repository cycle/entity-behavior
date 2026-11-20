<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Dispatcher\EventListProviderInterface;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnUpdate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\QueueCommand;

/**
 * Listens through attributes and through {@see getEventsList()} at once.
 */
class AuditService implements EventListProviderInterface
{
    public function getEventsList(): array
    {
        return [
            [OnUpdate::class, 'updateTitle'],
            [OnUpdate::class, 'updateContent'],
        ];
    }

    #[Listen(OnCreate::class)]
    public function createTitle(OnCreate $event): void
    {
        $event->state->register('title', 'created');
    }

    #[Listen(OnCreate::class)]
    public function createContent(OnCreate $event): void
    {
        $event->state->register('content', 'created');
    }

    public function updateTitle(OnUpdate $event): void
    {
        $event->state->register('title', 'updated');
    }

    public function updateContent(OnUpdate $event): void
    {
        $event->state->register('content', 'updated');
    }

    #[Listen(QueueCommand::class)]
    public function queued(QueueCommand $event): void {}
}
