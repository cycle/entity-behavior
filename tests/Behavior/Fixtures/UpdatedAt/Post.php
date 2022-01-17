<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\UpdatedAt;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\UpdatedAt;

#[Entity]
#[UpdatedAt]
#[UpdatedAt(field: 'newField', column: 'new_field')]
class Post
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTimeImmutable $updatedAt = null;
    public ?\DateTimeImmutable $customUpdatedAt = null;
    public \DateTimeImmutable $notNullableUpdatedAt;
    public ?string $content = null;
}
