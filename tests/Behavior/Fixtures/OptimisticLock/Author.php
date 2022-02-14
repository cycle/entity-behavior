<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;

#[Embeddable]
class Author
{
    public function __construct(
        #[Column(type: 'string', name: 'author_first_name')]
        public string $firstName,

        #[Column(type: 'string', name: 'author_last_name')]
        public string $lastName,
    )
    {
        
    }
}
