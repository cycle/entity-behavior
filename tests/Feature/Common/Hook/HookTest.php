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

final class HookTest extends SchemaTestCase
{
    #[\Testo\Test]
    public function testAddListener(): void
    {
        $listeners = $this->schema->define(Post::class, SchemaInterface::LISTENERS);

        \Testo\Assert::array($listeners);
        \Testo\Assert::count($listeners, 2);
        \Testo\Assert::array($listeners[0]);
        \Testo\Assert::array($listeners[1]);

        \Testo\Assert::same($listeners[0][0], Hook::class);
        \Testo\Assert::same($listeners[0][1], ['callable' => [PostService::class, 'update'], 'events' => [OnUpdate::class]]);

        \Testo\Assert::same($listeners[1][0], Hook::class);
        \Testo\Assert::same($listeners[1][1], ['callable' => [Post::class, 'touch'], 'events' => [OnCreate::class, OnUpdate::class]]);
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/Hook'],
            'exclude' => [],
        ])));
    }
}
