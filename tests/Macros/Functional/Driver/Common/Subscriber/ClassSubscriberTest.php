<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Subscriber;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\Comment;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\CommentService;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\Post;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Macros\Tests\Traits\TableTrait;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class ClassSubscriberTest extends BaseTest
{
    use TableTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            'posts',
            [
                'id' => 'primary',
                'title' => 'string,nullable',
                'content' => 'string,nullable'
            ]
        );

        $reader = new AttributeReader();
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [dirname(__DIR__, 4) . '/Fixtures'],
            'exclude' => ['Wrong'],
        ]));

        $this->withSchema(new Schema((new Compiler())->compile(new Registry($this->dbal), [
            new Entities($tokenizer->classLocator(), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new GenerateTypecast(),
            new GenerateModifiers()
        ])));
    }

    public function testSchemaWithArgs(): void
    {
        $macro = $this->orm->getSchema()->define(Post::class, SchemaInterface::MACROS);

        $this->assertIsArray($macro);
        $this->assertIsArray($macro[0]);
        $this->assertSame(PostService::class, $macro[0][0]);
        $this->assertSame(['foo' => 'modified by ClassSubscriberMacro', 'bar' => ['baz']], $macro[0][1]);
        $this->assertCount(2, $macro[0]);
    }

    public function testSchema(): void
    {
        $macro = $this->orm->getSchema()->define(Comment::class, SchemaInterface::MACROS);

        $this->assertIsArray($macro);
        $this->assertIsArray($macro[0]);
        $this->assertSame(CommentService::class, $macro[0][0]);
        $this->assertCount(1, $macro[0]);
    }

    public function testApply(): void
    {
        $post = new Post();

        $this->save($post);

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();

        $this->assertSame('modified by ClassSubscriberMacro', $data->title);
        $this->assertSame('baz', $data->content);
    }
}
