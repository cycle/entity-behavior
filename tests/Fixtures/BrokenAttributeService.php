<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;

/**
 * Declares {@see Listen} without the event, so the attribute cannot be instantiated.
 */
class BrokenAttributeService
{
    #[Listen]
    public function onCreate(OnCreate $event): void {}
}
