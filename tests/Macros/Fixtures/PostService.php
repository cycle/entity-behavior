<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures;

use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnUpdate;

class PostService
{
    public static function update(OnUpdate $event): void
    {
        $event->state->register('content', 'modified by service');
    }
}
