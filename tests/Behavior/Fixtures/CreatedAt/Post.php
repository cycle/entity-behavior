<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\CreatedAt;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\CreatedAt;

#[Entity]
#[CreatedAt]
#[CreatedAt(field: 'newField', column: 'new_field')]
class Post
{
    #[Column(type: 'primary')]
    public int $id;
    public ?\DateTimeImmutable $createdAt = null;
    public ?\DateTimeImmutable $customCreatedAt = null;
    public ?string $content = null;
}
