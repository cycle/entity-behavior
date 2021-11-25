<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\Wrong\CallableInvalid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Subscriber\CallableSubscriberMacro;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\PostService;

#[Entity]
#[CallableSubscriberMacro(callable: [PostService::class, 'undefinedMethod'], events: [OnCreate::class])]
class User
{
    #[Column(type: 'primary')]
    public int $id;
}
