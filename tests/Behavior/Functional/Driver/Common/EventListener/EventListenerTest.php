<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\EventListener;

use Cycle\ORM\Entity\Behavior\Tests\Fixtures\EventListener\Comment;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\EventListener\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CommentService;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseSchemaTest;
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
        $listeners = $this->schema->define(Post::class, SchemaInterface::LISTENERS);

        $this->assertIsArray($listeners);
        $this->assertIsArray($listeners[0]);
        $this->assertSame(PostService::class, $listeners[0][0]);
        $this->assertSame(['foo' => 'modified by EventListener', 'bar' => ['baz']], $listeners[0][1]);
        $this->assertCount(2, $listeners[0]);
    }

    public function testSchema(): void
    {
        $listeners = $this->schema->define(Comment::class, SchemaInterface::LISTENERS);
        $this->assertIsArray($listeners);
        $this->assertSame(CommentService::class, $listeners[0]);
        $this->assertCount(1, $listeners);
    }
}
