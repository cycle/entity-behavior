<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\OptimisticLock;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\OptimisticLock\OptimisticLockListener;
use Cycle\ORM\Entity\Macros\OptimisticLock\OptimisticLockMacro;

#[Entity]
#[OptimisticLockMacro(rule: OptimisticLockListener::RULE_DATETIME)]
class Page
{
    #[Column(type: 'primary')]
    public int $id;
}
