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
    #[\Testo\Test]
    public function testExistenceColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        \Testo\Assert::true($fields->has('deletedAt'));
        \Testo\Assert::true($fields->hasColumn('deleted_at'));
        \Testo\Assert::same($fields->get('deletedAt')->getType(), 'datetime');
        \Testo\Assert::same($fields->get('deletedAt')->getGenerated(), GeneratedField::BEFORE_UPDATE);

        // No new fields added
        \Testo\Assert::same($fields->count(), 3);
    }

    #[\Testo\Test]
    public function testAddedColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        \Testo\Assert::true($fields->has('newField'));
        \Testo\Assert::true($fields->hasColumn('new_field'));
        \Testo\Assert::same($fields->get('newField')->getType(), 'datetime');
        \Testo\Assert::same($fields->get('newField')->getGenerated(), GeneratedField::BEFORE_UPDATE);
    }

    #[\Testo\Lifecycle\BeforeTest]
    public function setUp(): void
    {
        parent::setUp();

        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 4) . '/Fixtures/SoftDelete'],
            'exclude' => [],
        ])));
    }
}
