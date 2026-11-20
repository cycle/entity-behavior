<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures;

use Cycle\ORM\Entity\Behavior\Schema\BaseModifier;

/**
 * A custom behavior whose listener takes no arguments.
 */
final class ArgumentlessModifier extends BaseModifier
{
    protected function getListenerClass(): string
    {
        return AuditService::class;
    }

    protected function getListenerArgs(): array
    {
        return [];
    }
}
