<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\UpdatedAt;

use Cycle\ORM\Entity\Behavior\Tests\Fixtures\UpdatedAt\Post;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseSchemaTest;
use Cycle\ORM\Schema\GeneratedField;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class UpdatedAtTest extends BaseSchemaTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 4) . '/Fixtures/UpdatedAt'],
            'exclude' => [],
        ])));
    }

    public function testExistenceColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('updatedAt'));
        $this->assertTrue($fields->hasColumn('updated_at'));
        $this->assertSame('datetime', $fields->get('updatedAt')->getType());
        $this->assertSame(
            GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE,
            $fields->get('updatedAt')->getGenerated()
        );

        // No new fields added
        $this->assertSame(3, $fields->count());
    }

    public function testAddedColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('newField'));
        $this->assertTrue($fields->hasColumn('new_field'));
        $this->assertSame('datetime', $fields->get('newField')->getType());
        $this->assertSame(
            GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE,
            $fields->get('newField')->getGenerated()
        );
    }
}
