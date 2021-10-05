<?php

declare(strict_types=1);

namespace Cycle\SmartMapper\Behavior\Timestamped;

use Cycle\ORM\Command\StoreCommandInterface;
use Cycle\SmartMapper\Attribute\Listen;
use Cycle\SmartMapper\Event\Mapper\Command\OnCreate;
use Cycle\SmartMapper\Event\Mapper\Command\OnUpdate;

final class TimestampedListener
{
    public function __construct(
        private string $fieldCreatedAt = 'createdAt',
        private string $fieldUpdatedAt = 'updatedAt',
    ) {
    }

    #[Listen(OnCreate::class)]
    #[Listen(OnUpdate::class)]
    public function __invoke(OnUpdate|OnCreate $event): void
    {
        $time = new \DateTimeImmutable();
        $event->state->register($this->fieldCreatedAt, $time);
        if ($event instanceof OnCreate) {
            $event->state->register($this->fieldUpdatedAt, $time);
        } elseif ($event->command instanceof StoreCommandInterface) {
            $event->command->registerAppendix('updated_at', $time);
        }
    }
}
