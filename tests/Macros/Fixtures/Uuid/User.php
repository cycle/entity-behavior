<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\Uuid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\Uuid\UuidV1Macro;
use Ramsey\Uuid\UuidInterface;

#[Entity]
#[UuidV1Macro]
class User
{
    #[Column(type: 'uuid', primary: true)]
    public UuidInterface $uuid;
}
