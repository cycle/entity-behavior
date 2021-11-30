<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\Subscriber;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnUpdate;
use Cycle\ORM\Entity\Macros\Subscriber\ClassSubscriberMacro;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\PostService;

#[Entity]
#[ClassSubscriberMacro(PostService::class, ['foo' => 'modified by ClassSubscriberMacro', 'bar' => ['baz']])]
class Post
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string', nullable: true)]
    public ?string $title = null;

    #[Column(type: 'string', nullable: true)]
    public ?string $content = null;

    public ?string $slug = null;
    public ?\DateTimeImmutable $updatedAt = null;
    public ?\DateTimeImmutable $createdAt = null;
    public ?string $lastEvent = null;

    public static function onCreate(OnCreate $event): void
    {
        $event->state->register('createdAt', new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        $event->state->register('slug', strtolower($event->entity->title));
    }

    public static function touch(OnCreate|OnUpdate $event): void
    {
        $event->state->register('lastEvent', $event::class);
        $event->state->register('updatedAt', new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
    }
}
