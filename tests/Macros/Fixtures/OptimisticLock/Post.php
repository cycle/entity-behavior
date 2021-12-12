<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\OptimisticLock;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\OptimisticLock;

#[Entity]
#[OptimisticLock(rule: OptimisticLock::RULE_INCREMENT)]
class Post
{
    #[Column(type: 'primary')]
    public int $id;
}
