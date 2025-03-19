<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Integration\CaseTemplate\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\OptimisticLock;
use Cycle\ORM\Entity\Behavior\SoftDelete;
use Cycle\ORM\Entity\Behavior\UpdatedAt;

#[Entity(role: Post::ROLE, table: 'post')]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
#[SoftDelete(field: 'deletedAt', column: 'deleted_at')]
#[OptimisticLock(field: 'version', rule: OptimisticLock::RULE_INCREMENT)]
class Post
{
    public const ROLE = 'post';

    #[Column(type: 'bigPrimary')]
    public ?int $id = null;

    #[Column(type: 'string')]
    public string $slug;

    #[Column(type: 'string')]
    public string $title = '';

    #[Column(type: 'boolean')]
    public bool $public = false;

    #[Column(type: 'text')]
    public string $content = '';

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTimeImmutable $published_at = null;

    public int $version = 0;
    public \DateTimeImmutable $created_at;
    public \DateTimeImmutable $updated_at;
    public ?\DateTimeImmutable $deleted_at = null;

    #[BelongsTo(target: User::class, innerKey: 'userId', fkAction: 'NO ACTION')]
    public User $user;

    #[Column(type: 'bigInteger', name: 'user_id')]
    public ?int $userId = null;

    /** @var iterable<Tag> */
    #[ManyToMany(target: Tag::class, innerKey: 'id', outerKey: 'id', throughInnerKey: 'postId', throughOuterKey: 'tagId', through: PostTag::class)]
    public iterable $tags = [];

    /** @var iterable<Comment> */
    #[HasMany(target: Comment::class, innerKey: 'id', outerKey: 'postId', fkCreate: false)]
    public iterable $comments = [];

    private function __construct() {}
}
