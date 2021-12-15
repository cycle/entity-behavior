<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Attribute;

use Attribute;
use Cycle\ORM\Entity\Behavior\Event\MapperEvent;

#[Attribute(flags: Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Listen
{
    /**
     * @param class-string<MapperEvent> $event
     */
    public function __construct(
        public string $event
    ) {
    }
}
