<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures;

use Cycle\ORM\Parser\TypecastInterface;

class CustomTypecast implements TypecastInterface
{
    public function setRules(array $rules): array
    {
        return [];
    }

    public function cast(array $values): array
    {
        return [];
    }
}
