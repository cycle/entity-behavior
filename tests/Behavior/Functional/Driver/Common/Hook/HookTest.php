<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Hook;

use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnUpdate;
use Cycle\ORM\Entity\Behavior\Listener\Hook;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\Hook\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseSchemaTest;
use Cycle\ORM\SchemaInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class HookTest extends BaseSchemaTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [dirname(__DIR__, 4) . '/Fixtures/Hook'],
            'exclude' => [],
        ])));
    }

    public function testAddMacro(): void
    {
        $macro = $this->schema->define(Post::class, SchemaInterface::MACROS);

        $this->assertIsArray($macro);
        $this->assertCount(2, $macro);
        $this->assertIsArray($macro[0]);
        $this->assertIsArray($macro[1]);

        $this->assertSame(Hook::class, $macro[0][0]);
        $this->assertSame(
            ['callable' => [PostService::class, 'update'], 'events' => [OnUpdate::class]],
            $macro[0][1]
        );

        $this->assertSame(Hook::class, $macro[1][0]);
        $this->assertSame(
            ['callable' => [Post::class, 'touch'], 'events' => [OnCreate::class, OnUpdate::class]],
            $macro[1][1]
        );
    }
}
