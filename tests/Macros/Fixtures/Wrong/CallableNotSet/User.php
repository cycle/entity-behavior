<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Fixtures\Wrong\CallableNotSet;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Subscriber\CallableSubscriberMacro;

#[Entity]
#[CallableSubscriberMacro(callable: [], events: [OnCreate::class])]
class User
{
    #[Column(type: 'primary')]
    public int $id;
}
