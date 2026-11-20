<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\CreatedAt;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\CreatedAt;

#[Entity]
#[CreatedAt(field: 'addedAt')]
class Comment
{
    #[Column(type: 'primary')]
    public int $id;
}
