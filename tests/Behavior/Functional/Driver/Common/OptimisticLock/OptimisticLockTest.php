<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\OptimisticLock;

use Cycle\Database\ColumnInterface;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Comment;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Item;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\News;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Page;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\WithAllParameters;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseSchemaTest;
use Cycle\ORM\Schema\GeneratedField;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class OptimisticLockTest extends BaseSchemaTest
{
    #[\Testo\Test]
    public function testAddIntColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        \Testo\Assert::true($fields->has('version'));
        \Testo\Assert::true($fields->hasColumn('version'));
        \Testo\Assert::same($fields->get('version')->getType(), 'integer');
        \Testo\Assert::same($fields->get('version')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);
    }

    #[\Testo\Test]
    public function testAddStringColumn(): void
    {
        $fields = $this->registry->getEntity(Comment::class)->getFields();

        \Testo\Assert::true($fields->has('version'));
        \Testo\Assert::true($fields->hasColumn('version'));
        \Testo\Assert::same($fields->get('version')->getType(), ColumnInterface::STRING);
        \Testo\Assert::same($fields->get('version')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);
    }

    #[\Testo\Test]
    public function testAddDatetimeColumn(): void
    {
        $fields = $this->registry->getEntity(Page::class)->getFields();

        \Testo\Assert::true($fields->has('version'));
        \Testo\Assert::true($fields->hasColumn('version'));
        \Testo\Assert::same($fields->get('version')->getType(), 'datetime');
        \Testo\Assert::same($fields->get('version')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);
    }

    #[\Testo\Test]
    public function testExistColumn(): void
    {
        $fields = $this->registry->getEntity(Item::class)->getFields();

        \Testo\Assert::true($fields->has('revision'));
        \Testo\Assert::true($fields->hasColumn('revision_field'));
        \Testo\Assert::same($fields->get('revision')->getType(), 'integer');
        \Testo\Assert::same($fields->get('revision')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);

        // not added new columns
        \Testo\Assert::same($fields->count(), 2);
    }

    #[\Testo\Test]
    public function testExistColumnAndAllOptimisticLockParameters(): void
    {
        $fields = $this->registry->getEntity(WithAllParameters::class)->getFields();

        \Testo\Assert::true($fields->has('revision'));
        \Testo\Assert::true($fields->hasColumn('revision_field'));
        \Testo\Assert::same($fields->get('revision')->getType(), 'integer');
        \Testo\Assert::same($fields->get('revision')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);

        // not added new columns
        \Testo\Assert::same($fields->count(), 2);
    }

    #[\Testo\Test]
    public function testAddDefaultColumAndRule(): void
    {
        $fields = $this->registry->getEntity(News::class)->getFields();

        \Testo\Assert::true($fields->has('version'));
        \Testo\Assert::true($fields->hasColumn('version'));
        \Testo\Assert::same($fields->get('version')->getType(), 'integer');
        \Testo\Assert::same($fields->get('version')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);

        \Testo\Assert::same($fields->count(), 2);
    }

    #[\Testo\Lifecycle\BeforeTest]
    public function setUp(): void
    {
        parent::setUp();

        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 4) . '/Fixtures/OptimisticLock'],
            'exclude' => [],
        ])));
    }
}
