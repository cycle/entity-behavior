<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\SoftDelete;

use Cycle\ORM\Entity\Behavior\Listener\SoftDelete as Listener;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\SoftDelete\Comment;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\SoftDelete\Post;
use Cycle\ORM\Entity\Behavior\Tests\Support\SchemaTestCase;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class SoftDeleteTest extends SchemaTestCase
{
    #[Test]
    public function testExistenceColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('deletedAt'));
        Assert::true($fields->hasColumn('deleted_at'));
        Assert::same($fields->get('deletedAt')->getType(), 'datetime');
        Assert::same($fields->get('deletedAt')->getGenerated(), GeneratedField::BEFORE_UPDATE);

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
        Assert::same($fields->get('newField')->getGenerated(), GeneratedField::BEFORE_UPDATE);
    }

    #[Test]
    public function testColumnNamedAfterField(): void
    {
        $entity = $this->registry->getEntity(Comment::class);

        Assert::same($entity->getFields()->get('removedAt')->getColumn(), 'removedAt');
        Assert::true($this->registry->getTableSchema($entity)->column('removedAt')->isNullable());
    }

    #[Test]
    public function testListenerReceivesField(): void
    {
        Assert::same($this->schema->define(Post::class, SchemaInterface::LISTENERS), [
            [Listener::class, ['field' => 'deletedAt']],
            [Listener::class, ['field' => 'newField']],
        ]);
    }

    #[Test]
    public function testComputeConfiguresDeclaredField(): void
    {
        $this->computeWithTokenizer(self::tokenizer());

        $table = $this->registry->getTableSchema($this->registry->getEntity(Post::class));

        Assert::true($table->hasColumn('deleted_at'));
        Assert::true($table->column('deleted_at')->isNullable());
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(self::tokenizer());
    }

    private static function tokenizer(): Tokenizer
    {
        return new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/SoftDelete'],
            'exclude' => [],
        ]));
    }
}
