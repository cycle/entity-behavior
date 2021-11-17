<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures;

use DateTimeImmutable;

class Post
{
    public int $id;
    public ?DateTimeImmutable $createdAt = null;
    public ?DateTimeImmutable $customCreatedAt = null;
    public ?DateTimeImmutable $updatedAt = null;
    public ?DateTimeImmutable $customUpdatedAt = null;
    public ?string $content = null;
}