<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures;

use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnUpdate;

class PostService
{
    public function __invoke(OnUpdate $event, string $content): void
    {
        $event->state->register('content', $content);
    }
}
