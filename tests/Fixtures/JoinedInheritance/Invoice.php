<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\JoinedInheritance;

class Invoice extends Document
{
    public int $amount = 0;
}
