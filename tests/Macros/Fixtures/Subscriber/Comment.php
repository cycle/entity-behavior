<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\Subscriber;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\Subscriber\ClassSubscriberMacro;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\CommentService;

#[Entity]
#[ClassSubscriberMacro(CommentService::class)]
class Comment
{
    #[Column(type: 'primary')]
    public int $id;
}
