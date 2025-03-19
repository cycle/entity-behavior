<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Integration\CaseTemplate\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\ORM\Entity\Behavior\CreatedAt;

#[Entity(role: Tag::ROLE, table: 'tag')]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
class Tag
{
    public const ROLE = 'tag';

    #[Column(type: 'bigPrimary', name: 'id')]
    public ?int $id = null;

    #[Column(type: 'string')]
    public string $label;

    public \DateTimeImmutable $createdAt;

    /** @var iterable<Post> */
    #[ManyToMany(target: Post::class, innerKey: 'id', outerKey: 'id', throughInnerKey: 'tagId', throughOuterKey: 'postId', through: PostTag::class)]
    public iterable $posts = [];

    private function __construct() {}
}
