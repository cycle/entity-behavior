<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\EventListener;

use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CommentService;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\EventListener\Comment;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\EventListener\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Behavior\Tests\Support\SchemaTestCase;
use Cycle\ORM\SchemaInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

final class EventListenerTest extends SchemaTestCase
{
    #[\Testo\Test]
    public function testSchemaWithArgs(): void
    {
        $listeners = $this->schema->define(Post::class, SchemaInterface::LISTENERS);

        \Testo\Assert::array($listeners);
        \Testo\Assert::array($listeners[0]);
        \Testo\Assert::same($listeners[0][0], PostService::class);
        \Testo\Assert::same($listeners[0][1], ['foo' => 'modified by EventListener', 'bar' => ['baz']]);
        \Testo\Assert::count($listeners[0], 2);
    }

    #[\Testo\Test]
    public function testSchema(): void
    {
        $listeners = $this->schema->define(Comment::class, SchemaInterface::LISTENERS);
        \Testo\Assert::array($listeners);
        \Testo\Assert::same($listeners[0], CommentService::class);
        \Testo\Assert::count($listeners, 1);
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/EventListener'],
            'exclude' => [],
        ])));
    }
}
