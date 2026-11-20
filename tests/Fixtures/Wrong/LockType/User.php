<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\Wrong\LockType;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\OptimisticLock;

#[Entity]
#[OptimisticLock]
class User
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'boolean')]
    public bool $version;
}
