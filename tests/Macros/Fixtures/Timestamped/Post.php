<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\Timestamped;

use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnUpdate;

class Post
{
    public int $id;
    public ?\DateTimeImmutable $createdAt = null;
    public ?\DateTimeImmutable $customCreatedAt = null;
    public ?\DateTimeImmutable $updatedAt = null;
    public ?\DateTimeImmutable $customUpdatedAt = null;

    public ?string $title = null;
    public ?string $content = null;
    public ?string $slug = null;
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
