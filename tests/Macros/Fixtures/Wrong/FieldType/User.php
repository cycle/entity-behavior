<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\Wrong\FieldType;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\Timestamped\CreatedAtMacro;

#[Entity]
#[CreatedAtMacro(field: 'wrongCreatedAt', column: 'wrong_created_at')]
class User
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string', nullable: true)]
    public ?string $wrongCreatedAt;
}
