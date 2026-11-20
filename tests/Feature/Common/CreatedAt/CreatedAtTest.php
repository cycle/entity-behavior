<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\CreatedAt;

use Cycle\ORM\Entity\Behavior\Listener\CreatedAt as Listener;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CreatedAt\Comment;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CreatedAt\Post;
use Cycle\ORM\Entity\Behavior\Tests\Support\SchemaTestCase;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class CreatedAtTest extends SchemaTestCase
{
    #[Test]
    public function testExistenceColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('createdAt'));
        Assert::true($fields->hasColumn('created_at'));
        Assert::same($fields->get('createdAt')->getType(), 'datetime');
        Assert::same($fields->get('createdAt')->getGenerated(), GeneratedField::BEFORE_INSERT);

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
        Assert::same($fields->get('newField')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[Test]
    public function testColumnNamedAfterField(): void
    {
        $entity = $this->registry->getEntity(Comment::class);
        $field = $entity->getFields()->get('addedAt');

        Assert::same($field->getColumn(), 'addedAt');
        Assert::same($field->getGenerated(), GeneratedField::BEFORE_INSERT);
        Assert::false($this->registry->getTableSchema($entity)->column('addedAt')->isNullable());
    }

    #[Test]
    public function testListenerReceivesField(): void
    {
        Assert::same($this->schema->define(Post::class, SchemaInterface::LISTENERS), [
            [Listener::class, ['field' => 'createdAt']],
            [Listener::class, ['field' => 'newField']],
        ]);
    }

    #[Test]
    public function testComputeConfiguresDeclaredField(): void
    {
        $this->computeWithTokenizer(self::tokenizer());

        $entity = $this->registry->getEntity(Post::class);

        Assert::same($entity->getFields()->get('createdAt')->getGenerated(), GeneratedField::BEFORE_INSERT);
        Assert::false($this->registry->getTableSchema($entity)->column('created_at')->isNullable());
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(self::tokenizer());
    }

    private static function tokenizer(): Tokenizer
    {
        return new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/CreatedAt'],
            'exclude' => [],
        ]));
    }
}
