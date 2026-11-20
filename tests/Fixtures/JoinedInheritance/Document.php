<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\JoinedInheritance;

class Document
{
    public ?int $id = null;
    public ?\DateTimeImmutable $createdAt = null;
    public ?\DateTimeImmutable $updatedAt = null;
    public string $title = '';
}
