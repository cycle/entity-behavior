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
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class EventListenerTest extends SchemaTestCase
{
    #[Test]
    public function testSchemaWithArgs(): void
    {
        $listeners = $this->schema->define(Post::class, SchemaInterface::LISTENERS);

        Assert::array($listeners);
        Assert::array($listeners[0]);
        Assert::same($listeners[0][0], PostService::class);
        Assert::same($listeners[0][1], ['foo' => 'modified by EventListener', 'bar' => ['baz']]);
        Assert::count($listeners[0], 2);
    }

    #[Test]
    public function testSchema(): void
    {
        $listeners = $this->schema->define(Comment::class, SchemaInterface::LISTENERS);
        Assert::array($listeners);
        Assert::same($listeners[0], CommentService::class);
        Assert::count($listeners, 1);
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/EventListener'],
            'exclude' => [],
        ])));
    }
}
