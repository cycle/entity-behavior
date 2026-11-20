<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\UpdatedAt;

use Cycle\ORM\Entity\Behavior\Listener\UpdatedAt as Listener;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\UpdatedAt\Post;
use Cycle\ORM\Entity\Behavior\Tests\Support\SchemaTestCase;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class UpdatedAtTest extends SchemaTestCase
{
    #[Test]
    public function testExistenceColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('updatedAt'));
        Assert::true($fields->hasColumn('updated_at'));
        Assert::same($fields->get('updatedAt')->getType(), 'datetime');
        Assert::same($fields->get('updatedAt')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);

        // No new fields added
        Assert::same($fields->count(), 3);
    }

    #[Test]
    public function testAddedColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('newField'));
        Assert::true($fields->hasColumn('new_field'));
        Assert::same($fields->get('newField')->getType(), 'datetime');
        Assert::same($fields->get('newField')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);
    }

    #[Test]
    public function testListenerReceivesFieldAndNullable(): void
    {
        Assert::same($this->schema->define(Post::class, SchemaInterface::LISTENERS), [
            [Listener::class, ['field' => 'updatedAt', 'nullable' => false]],
            [Listener::class, ['field' => 'newField', 'nullable' => false]],
        ]);
    }

    #[Test]
    public function testComputeAddsFieldWithKnownColumn(): void
    {
        $this->computeWithTokenizer(self::tokenizer());

        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('newField'));
        Assert::same($fields->get('newField')->getColumn(), 'new_field');
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(self::tokenizer());
    }

    private static function tokenizer(): Tokenizer
    {
        return new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/UpdatedAt'],
            'exclude' => [],
        ]));
    }
}
