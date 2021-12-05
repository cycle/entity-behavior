<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnCreate;
use Ramsey\Uuid\Uuid;

final class Uuid4Listener
{
    public function __construct(
        private string $field = 'uuid'
    ) {
    }

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if (!isset($event->state->getData()[$this->field])) {
            $event->state->register($this->field, Uuid::uuid4());
        }
    }
}
