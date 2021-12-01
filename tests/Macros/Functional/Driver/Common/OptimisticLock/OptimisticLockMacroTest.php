<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\OptimisticLock;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Database\Schema\AbstractColumn;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\OptimisticLock\Comment;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\OptimisticLock\Page;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\OptimisticLock\Post;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\OptimisticLock\Product;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderModifiers;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class OptimisticLockMacroTest extends BaseTest
{
    protected Registry $registry;

    public function setUp(): void
    {
        parent::setUp();

        $reader = new AttributeReader();
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [dirname(__DIR__, 4) . '/Fixtures/OptimisticLock'],
            'exclude' => [],
        ]));

        (new Compiler())->compile($this->registry = new Registry($this->dbal), [
            new Entities($tokenizer->classLocator(), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new MergeIndexes($reader),
            new GenerateRelations(),
            new GenerateModifiers(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new RenderModifiers(),
            new GenerateTypecast(),
        ]);
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
}
