<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\OptimisticLock;

use Cycle\Database\ColumnInterface;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Comment;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Item;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\News;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Page;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\WithAllParameters;
use Cycle\ORM\Entity\Behavior\Tests\Support\SchemaTestCase;
use Cycle\ORM\Schema\GeneratedField;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class OptimisticLockTest extends SchemaTestCase
{
    #[Test]
    public function testAddIntColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('version'));
        Assert::true($fields->hasColumn('version'));
        Assert::same($fields->get('version')->getType(), 'integer');
        Assert::same($fields->get('version')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);
    }

    #[Test]
    public function testAddStringColumn(): void
    {
        $fields = $this->registry->getEntity(Comment::class)->getFields();

        Assert::true($fields->has('version'));
        Assert::true($fields->hasColumn('version'));
        Assert::same($fields->get('version')->getType(), ColumnInterface::STRING);
        Assert::same($fields->get('version')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);
    }

    #[Test]
    public function testAddDatetimeColumn(): void
    {
        $fields = $this->registry->getEntity(Page::class)->getFields();

        Assert::true($fields->has('version'));
        Assert::true($fields->hasColumn('version'));
        Assert::same($fields->get('version')->getType(), 'datetime');
        Assert::same($fields->get('version')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);
    }

    #[Test]
    public function testExistColumn(): void
    {
        $fields = $this->registry->getEntity(Item::class)->getFields();

        Assert::true($fields->has('revision'));
        Assert::true($fields->hasColumn('revision_field'));
        Assert::same($fields->get('revision')->getType(), 'integer');
        Assert::same($fields->get('revision')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);

        // not added new columns
        Assert::same($fields->count(), 2);
    }

    #[Test]
    public function testExistColumnAndAllOptimisticLockParameters(): void
    {
        $fields = $this->registry->getEntity(WithAllParameters::class)->getFields();

        Assert::true($fields->has('revision'));
        Assert::true($fields->hasColumn('revision_field'));
        Assert::same($fields->get('revision')->getType(), 'integer');
        Assert::same($fields->get('revision')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);

        // not added new columns
        Assert::same($fields->count(), 2);
    }

    #[Test]
    public function testAddDefaultColumAndRule(): void
    {
        $fields = $this->registry->getEntity(News::class)->getFields();

        Assert::true($fields->has('version'));
        Assert::true($fields->hasColumn('version'));
        Assert::same($fields->get('version')->getType(), 'integer');
        Assert::same($fields->get('version')->getGenerated(), GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE);

        Assert::same($fields->count(), 2);
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/OptimisticLock'],
            'exclude' => [],
        ])));
    }
}
