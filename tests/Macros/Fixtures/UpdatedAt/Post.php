<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\UpdatedAt;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\UpdatedAt;

#[Entity]
#[UpdatedAt]
#[UpdatedAt(field: 'newField', column: 'new_field')]
class Post
{
    #[Column(type: 'primary')]
    public int $id;
    public ?\DateTimeImmutable $updatedAt = null;
    public ?\DateTimeImmutable $customUpdatedAt = null;
    public ?string $content = null;
}
