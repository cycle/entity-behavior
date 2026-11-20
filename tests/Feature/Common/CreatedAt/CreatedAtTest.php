<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\CreatedAt;

use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CreatedAt\Post;
use Cycle\ORM\Entity\Behavior\Tests\Support\SchemaTestCase;
use Cycle\ORM\Schema\GeneratedField;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

final class CreatedAtTest extends SchemaTestCase
{
    #[\Testo\Test]
    public function testExistenceColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        \Testo\Assert::true($fields->has('createdAt'));
        \Testo\Assert::true($fields->hasColumn('created_at'));
        \Testo\Assert::same($fields->get('createdAt')->getType(), 'datetime');
        \Testo\Assert::same($fields->get('createdAt')->getGenerated(), GeneratedField::BEFORE_INSERT);

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
        \Testo\Assert::same($fields->get('newField')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/CreatedAt'],
            'exclude' => [],
        ])));
    }
}
