<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Integration\CaseTemplate\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(role: PostTag::ROLE, table: 'post_tag')]
class PostTag
{
    public const ROLE = 'post_tag';

    #[Column(type: 'bigInteger', name: 'post_id', primary: true)]
    private ?int $postId = null;

    #[Column(type: 'bigInteger', name: 'tag_id', primary: true)]
    private ?int $tagId = null;
}
