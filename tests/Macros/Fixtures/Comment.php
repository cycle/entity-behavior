<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\Subscriber\ClassSubscriberMacro;

#[Entity]
#[ClassSubscriberMacro(CommentService::class)]
class Comment
{
    #[Column(type: 'primary')]
    public int $id;
}
