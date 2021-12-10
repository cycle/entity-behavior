<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\CreatedAt;

use Cycle\ORM\Entity\Macros\Tests\Fixtures\CreatedAt\Post;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseSchemaTest;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class CreatedAtTest extends BaseSchemaTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 4) . '/Fixtures/CreatedAt'],
            'exclude' => [],
        ])));
    }

    public function testExistenceColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('createdAt'));
        $this->assertTrue($fields->hasColumn('created_at'));
        $this->assertSame('datetime', $fields->get('createdAt')->getType());
    }

    public function testAddedColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('newField'));
        $this->assertTrue($fields->hasColumn('new_field'));
        $this->assertSame('datetime', $fields->get('newField')->getType());
    }
}
