<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Timestamped;

use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnCreate;

final class CreatedAtListener
{
    public function __construct(
        private string $field = 'createdAt'
    ) {
    }

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        $event->state->register($this->field, new \DateTimeImmutable());
    }
}
