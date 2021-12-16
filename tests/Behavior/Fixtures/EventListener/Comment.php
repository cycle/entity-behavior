<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\EventListener;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\EventListener;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CommentService;

#[Entity]
#[EventListener(CommentService::class)]
class Comment
{
    #[Column(type: 'primary')]
    public int $id;
}
