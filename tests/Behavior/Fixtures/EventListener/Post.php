<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\EventListener;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\EventListener;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\PostService;

#[Entity]
#[EventListener(PostService::class, ['foo' => 'modified by EventListener', 'bar' => ['baz']])]
class Post
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string', nullable: true)]
    public ?string $title = null;

    #[Column(type: 'string', nullable: true)]
    public ?string $content = null;
}
