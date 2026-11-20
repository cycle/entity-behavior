<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Unit\Schema;

use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\EventListener;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\ArgumentlessModifier;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\AuditService;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\PostService;
use Cycle\ORM\SchemaInterface;
use Testo\Assert;
use Testo\Test;

#[Test]
final class BaseModifierTest
{
    public function testWithRoleReturnsNewInstance(): void
    {
        $modifier = new CreatedAt();

        Assert::notSame($modifier->withRole('post'), $modifier);
    }

    public function testListenerWithoutArgumentsIsDeclaredByClassName(): void
    {
        $schema = [];

        (new ArgumentlessModifier())->modifySchema($schema);

        Assert::same($schema, [SchemaInterface::LISTENERS => [AuditService::class]]);
    }

    public function testEventListenerWithRoleReturnsNewInstance(): void
    {
        $modifier = new EventListener(PostService::class);

        Assert::notSame($modifier->withRole('post'), $modifier);
    }
}
