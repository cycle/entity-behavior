<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures;

use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnCreate;

class CommentService
{
    #[Listen(OnCreate::class)]
    public function eventListener(OnCreate $event): void
    {
    }
}
