<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Integration\CaseTemplate\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\SoftDelete;
use Cycle\ORM\Entity\Behavior\UpdatedAt;

#[Entity(role: Comment::ROLE, table: 'comment')]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
#[SoftDelete(field: 'deletedAt', column: 'deleted_at')]
class Comment
{
    public const ROLE = 'comment';

    #[Column(type: 'bigPrimary')]
    public ?int $id = null;

    #[Column(type: 'boolean')]
    public bool $public = false;

    #[Column(type: 'text')]
    public string $content;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTimeImmutable $published_at = null;

    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
    public ?\DateTimeImmutable $deletedAt = null;

    #[BelongsTo(target: User::class, innerKey: 'userId', fkCreate: false)]
    public User $user;

    #[BelongsTo(target: Post::class, innerKey: 'postId', fkCreate: false)]
    public ?Post $post = null;

    #[Column(type: 'bigInteger', name: 'user_id')]
    public ?int $userId = null;

    #[Column(type: 'bigInteger', name: 'post_id')]
    public ?int $postId = null;

    private function __construct() {}
}
