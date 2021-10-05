<?php

declare(strict_types=1);

namespace Cycle\SmartMapper\Attribute;

use Attribute;
use Cycle\SmartMapper\Event\MapperEvent;

#[Attribute(flags: Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
final class Listen
{
    /**
     * @param class-string<MapperEvent> $event
     */
    public function __construct(
        public string $event
    ) { }
}
