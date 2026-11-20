<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\Hook;

use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnUpdate;
use Cycle\ORM\Entity\Behavior\Listener\Hook;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\Hook\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Behavior\Tests\Support\SchemaTestCase;
use Cycle\ORM\SchemaInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class HookTest extends SchemaTestCase
{
    #[Test]
    public function testAddListener(): void
    {
        $listeners = $this->schema->define(Post::class, SchemaInterface::LISTENERS);

        Assert::array($listeners);
        Assert::count($listeners, 2);
        Assert::array($listeners[0]);
        Assert::array($listeners[1]);

        Assert::same($listeners[0][0], Hook::class);
        Assert::same($listeners[0][1], ['callable' => [PostService::class, 'update'], 'events' => [OnUpdate::class]]);

        Assert::same($listeners[1][0], Hook::class);
        Assert::same($listeners[1][1], ['callable' => [Post::class, 'touch'], 'events' => [OnCreate::class, OnUpdate::class]]);
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/Hook'],
            'exclude' => [],
        ])));
    }
}
