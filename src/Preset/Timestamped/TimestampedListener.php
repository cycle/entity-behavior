<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Preset\Timestamped;

use Cycle\ORM\Command\StoreCommandInterface;
use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnUpdate;

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
