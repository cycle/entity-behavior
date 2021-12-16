<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\SoftDelete;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\SoftDelete;

#[Entity]
#[SoftDelete]
#[SoftDelete(field: 'newField', column: 'new_field')]
class Post
{
    #[Column(type: 'primary')]
    public int $id;
    public ?\DateTimeImmutable $deletedAt = null;
    public ?\DateTimeImmutable $customDeletedAt = null;
    public ?string $content = null;
}
