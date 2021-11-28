<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

interface UuidInterface extends \Stringable
{
    public static function fromBytes(string $bytes);

    public function getBytes(): string;
}
