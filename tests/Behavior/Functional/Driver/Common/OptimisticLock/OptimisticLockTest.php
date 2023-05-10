<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\OptimisticLock;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Comment;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\News;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Page;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Product;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\WithAllParameters;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseSchemaTest;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class OptimisticLockTest extends BaseSchemaTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [dirname(__DIR__, 4) . '/Fixtures/OptimisticLock'],
            'exclude' => [],
        ])));
    }

    public function testAddIntColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('version'));
        $this->assertTrue($fields->hasColumn('version'));
        $this->assertSame('integer', $fields->get('version')->getType());
    }

    public function testAddStringColumn(): void
    {
        $fields = $this->registry->getEntity(Comment::class)->getFields();

        $this->assertTrue($fields->has('version'));
        $this->assertTrue($fields->hasColumn('version'));
        $this->assertSame(AbstractColumn::STRING, $fields->get('version')->getType());
    }

    public function testAddDatetimeColumn(): void
    {
        $fields = $this->registry->getEntity(Page::class)->getFields();

        $this->assertTrue($fields->has('version'));
        $this->assertTrue($fields->hasColumn('version'));
        $this->assertSame('datetime', $fields->get('version')->getType());
    }

    public function testExistColumn(): void
    {
        $fields = $this->registry->getEntity(Product::class)->getFields();

        $this->assertTrue($fields->has('revision'));
        $this->assertTrue($fields->hasColumn('revision_field'));
        $this->assertSame('integer', $fields->get('revision')->getType());

        // not added new columns
        $this->assertSame(2, $fields->count());
    }

    public function testExistColumnAndAllOptimisticLockParameters(): void
    {
        $fields = $this->registry->getEntity(WithAllParameters::class)->getFields();

        $this->assertTrue($fields->has('revision'));
        $this->assertTrue($fields->hasColumn('revision_field'));
        $this->assertSame('integer', $fields->get('revision')->getType());

        // not added new columns
        $this->assertSame(2, $fields->count());
    }

    public function testAddDefaultColumAndRule(): void
    {
        $fields = $this->registry->getEntity(News::class)->getFields();

        $this->assertTrue($fields->has('version'));
        $this->assertTrue($fields->hasColumn('version'));
        $this->assertSame('integer', $fields->get('version')->getType());
        $this->assertSame(2, $fields->count());
    }
}
