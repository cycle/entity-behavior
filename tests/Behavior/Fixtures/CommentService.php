<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;

class CommentService
{
    #[Listen(OnCreate::class)]
    public function eventListener(OnCreate $event): void
    {
    }
}
