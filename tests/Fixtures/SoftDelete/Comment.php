<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\SoftDelete;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\SoftDelete;

#[Entity]
#[SoftDelete(field: 'removedAt')]
class Comment
{
    #[Column(type: 'primary')]
    public int $id;
}
