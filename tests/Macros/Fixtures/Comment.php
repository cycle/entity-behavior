<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures;

class Comment
{
    public int $id;
    public ?string $content = null;
    public ?int $versionInt = null;
    public ?string $versionStr = null;
    public ?\DateTimeImmutable $versionDatetime = null;
    public ?string $versionMicrotime = null;
}
