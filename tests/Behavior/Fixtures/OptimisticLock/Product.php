<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\OptimisticLock;

#[Entity]
#[OptimisticLock(field: 'revision')]
class Product
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'integer', name: 'revision_field')]
    public int $revision;
}
