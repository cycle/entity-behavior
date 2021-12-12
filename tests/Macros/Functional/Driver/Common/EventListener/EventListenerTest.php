<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\EventListener;

use Cycle\ORM\Entity\Macros\Tests\Fixtures\EventListener\Comment;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\EventListener\Post;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\CommentService;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseSchemaTest;
use Cycle\ORM\SchemaInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class EventListenerTest extends BaseSchemaTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [dirname(__DIR__, 4) . '/Fixtures/EventListener'],
            'exclude' => [],
        ])));
    }

    public function testSchemaWithArgs(): void
    {
        $macro = $this->schema->define(Post::class, SchemaInterface::MACROS);

        $this->assertIsArray($macro);
        $this->assertIsArray($macro[0]);
        $this->assertSame(PostService::class, $macro[0][0]);
        $this->assertSame(['foo' => 'modified by EventListener', 'bar' => ['baz']], $macro[0][1]);
        $this->assertCount(2, $macro[0]);
    }

    public function testSchema(): void
    {
        $macro = $this->schema->define(Comment::class, SchemaInterface::MACROS);
        $this->assertIsArray($macro);
        $this->assertSame(CommentService::class, $macro[0]);
        $this->assertCount(1, $macro);
    }
}
