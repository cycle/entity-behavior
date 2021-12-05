<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnCreate;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

final class Uuid6Listener
{
    public function __construct(
        private string $field = 'uuid',
        private Hexadecimal|string|null $node = null,
        private ?int $clockSeq = null
    ) {
    }

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if (\is_string($this->node)) {
            $this->node = new Hexadecimal($this->node);
        }

        if (!isset($event->state->getData()[$this->field])) {
            $event->state->register($this->field, Uuid::uuid6($this->node, $this->clockSeq));
        }
    }
}
