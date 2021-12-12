<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\Wrong\ColumnName;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\UpdatedAt;

#[Entity]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
class User
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'datetime', name: 'custom_updated_at')]
    public ?\DateTimeImmutable $updatedAt;
}
