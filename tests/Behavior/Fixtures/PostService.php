<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnUpdate;

class PostService
{
    public function __construct(
        private string $foo,
        private array $bar
    ) {
    }

    public static function update(OnUpdate $event): void
    {
        $event->state->register('content', 'modified by service');
    }

    #[Listen(OnCreate::class)]
    public function eventListener(OnCreate $event): void
    {
        $event->state->register('title', $this->foo);
        $event->state->register('content', $this->bar[0]);
    }
}
