<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Listener;

use Cycle\ORM\Command\StoreCommandInterface;
use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnUpdate;

final class UpdatedAt
{
    public function __construct(
        private string $field = 'updatedAt',
        private bool $nullable = false
    ) {
    }

    #[Listen(OnUpdate::class)]
    public function __invoke(OnUpdate $event): void
    {
        if ($event->command instanceof StoreCommandInterface) {
            $event->command->registerAppendix($this->field, $event->timestamp);
        }
    }

    #[Listen(OnCreate::class)]
    public function onCreate(OnCreate $event): void
    {
        if ($this->nullable === false && !isset($event->state->getData()[$this->field])) {
            $event->state->register($this->field, $event->timestamp);
        }
    }
}
