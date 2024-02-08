<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\SoftDelete;

use Cycle\ORM\Entity\Behavior\Tests\Fixtures\SoftDelete\Post;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseSchemaTest;
use Cycle\ORM\Schema\GeneratedField;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class SoftDeleteTest extends BaseSchemaTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 4) . '/Fixtures/SoftDelete'],
            'exclude' => [],
        ])));
    }

    public function testExistenceColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('deletedAt'));
        $this->assertTrue($fields->hasColumn('deleted_at'));
        $this->assertSame('datetime', $fields->get('deletedAt')->getType());
        $this->assertSame(GeneratedField::BEFORE_UPDATE, $fields->get('deletedAt')->getGenerated());

        // No new fields added
        $this->assertSame(3, $fields->count());
    }

    public function testAddedColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('newField'));
        $this->assertTrue($fields->hasColumn('new_field'));
        $this->assertSame('datetime', $fields->get('newField')->getType());
        $this->assertSame(GeneratedField::BEFORE_UPDATE, $fields->get('newField')->getGenerated());
    }
}
