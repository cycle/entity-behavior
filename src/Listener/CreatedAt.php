<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;

final class CreatedAt
{
    public function __construct(
        private string $field = 'createdAt'
    ) {
    }

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        $event->state->register($this->field, $event->timestamp);
    }
}
