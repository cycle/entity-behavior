<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\OptimisticLock;

#[Entity]
#[OptimisticLock(rule: OptimisticLock::RULE_RAND_STR)]
class Comment
{
    #[Column(type: 'primary')]
    public int $id;

    public ?string $content = null;
    public ?int $versionInt = null;
    public ?string $versionStr = null;
    public ?\DateTimeImmutable $versionDatetime = null;
    public ?string $versionMicrotime = null;
    public ?int $versionCustom = null;
}
