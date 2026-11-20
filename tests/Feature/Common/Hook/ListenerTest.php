<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\Hook;

use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnUpdate;
use Cycle\ORM\Entity\Behavior\Listener\Hook;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\Hook\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Behavior\Tests\Support\ListenerTestCase;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ListenerTest extends ListenerTestCase
{
    #[\Testo\Test]
    public function testCreate(): void
    {
        $post = $this->createPost();

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();

        \Testo\Assert::notNull($data->createdAt);
        \Testo\Assert::notNull($data->updatedAt);
        \Testo\Assert::null($data->content);
        \Testo\Assert::same($post->slug, 'test');
        \Testo\Assert::same($data->lastEvent, OnCreate::class);
    }

    #[\Testo\Test]
    public function testUpdate(): void
    {
        $this->createPost();

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Post::class);

        $post = $select->fetchOne();
        $post->title = 'TEST 2';
        $content = $post->content;
        $this->save($post);

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();

        // Triggered OnUpdate event
        \Testo\Assert::null($content);
        \Testo\Assert::same($data->content, 'modified by service');

        // Not triggered OnCreate event, slug NOT changed.
        \Testo\Assert::same($data->slug, 'test');
        \Testo\Assert::same($data->title, 'TEST 2');

        // Triggered union type event OnCreate|OnUpdate
        \Testo\Assert::notNull($data->updatedAt);
        \Testo\Assert::notNull($data->createdAt);
        \Testo\Assert::same($data->lastEvent, OnUpdate::class);
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $this->makeTable(
            'posts',
            [
                'id' => 'primary',
                'title' => 'string,nullable',
                'content' => 'string,nullable',
                'slug' => 'string,nullable',
                'last_event' => 'string,nullable',
                'created_at' => 'datetime,nullable',
                'updated_at' => 'datetime,nullable',
            ],
        );

        $this->withSchema(new Schema([
            Post::class => [
                SchemaInterface::ROLE => 'post',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'posts',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => [
                    'id' => 'id',
                    'title' => 'title',
                    'content' => 'content',
                    'slug' => 'slug',
                    'lastEvent' => 'last_event',
                    'createdAt' => 'created_at',
                    'updatedAt' => 'updated_at',
                ],
                // listeners for different events
                SchemaInterface::LISTENERS => [
                    [
                        Hook::class,
                        ['callable' => PostService::class . '::update', 'events' => [OnUpdate::class]],
                    ],
                    [
                        Hook::class,
                        ['callable' => [Post::class, 'onCreate'], 'events' => [OnCreate::class]],
                    ],
                    [
                        Hook::class,
                        ['callable' => [Post::class, 'touch'], 'events' => [OnCreate::class, OnUpdate::class]],
                    ],
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'createdAt' => 'datetime',
                    'updatedAt' => 'datetime',
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }

    private function createPost(): Post
    {
        $post = new Post();
        $post->title = 'TEST';

        $this->save($post);

        return $post;
    }
}
