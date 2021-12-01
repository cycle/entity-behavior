<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\OptimisticLock;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\OptimisticLock\OptimisticLockMacro;

#[Entity]
#[OptimisticLockMacro(field: 'revision')]
class Product
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'integer', name: 'revision_field')]
    public int $revision;
}
