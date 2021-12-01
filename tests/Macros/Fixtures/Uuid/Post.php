<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\Uuid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\Uuid\UuidV4Macro;

#[Entity]
#[UuidV4Macro(field: 'customUuid', column: 'custom_uuid')]
class Post
{
    #[Column(type: 'primary')]
    public int $id;
}
