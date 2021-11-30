<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Subscriber;

use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnUpdate;
use Cycle\ORM\Entity\Macros\EventDrivenCommandGenerator;
use Cycle\ORM\Entity\Macros\Subscriber\CallableSubscriberListener;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\Subscriber\Post;
use Cycle\ORM\Entity\Macros\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Macros\Tests\Utils\SimpleContainer;
use Cycle\ORM\Factory;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

abstract class CallableSubscriberTest extends BaseTest
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
                'content' => 'string,nullable',
                'slug' => 'string,nullable',
                'last_event' => 'string,nullable',
                'created_at' => 'datetime,nullable',
                'updated_at' => 'datetime,nullable'
            ]
        );

        $schema = new Schema([
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
                // macros for different events
                SchemaInterface::MACROS => [
                    [
                        CallableSubscriberListener::class,
                        ['callable' => PostService::class . '::update', 'events' => [OnUpdate::class]]
                    ],
                    [
                        CallableSubscriberListener::class,
                        ['callable' => [Post::class, 'onCreate'], 'events' => [OnCreate::class]]
                    ],
                    [
                        CallableSubscriberListener::class,
                        ['callable' => [Post::class, 'touch'], 'events' => [OnCreate::class, OnUpdate::class]]
                    ]
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'createdAt' => 'datetime',
                    'updatedAt' => 'datetime'
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]);
        $this->orm = new ORM(
            new Factory(
                $this->dbal,
                RelationConfig::getDefault(),
                null,
                new ArrayCollectionFactory()
            ),
            $schema,
            new EventDrivenCommandGenerator($schema, new SimpleContainer())
        );
    }

    public function testCreate(): void
    {
        $post = $this->createPost();

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();

        $this->assertNotNull($data->createdAt);
        $this->assertNotNull($data->updatedAt);
        $this->assertNull($data->content);
        $this->assertSame('test', $post->slug);
        $this->assertSame(OnCreate::class, $data->lastEvent);
    }

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
        $this->assertNull($content);
        $this->assertSame('modified by service', $data->content);

        // Not triggered OnCreate event, slug NOT changed.
        $this->assertSame('test', $data->slug);
        $this->assertSame('TEST 2', $data->title);

        // Triggered union type event OnCreate|OnUpdate
        $this->assertNotNull($data->updatedAt);
        $this->assertNotNull($data->createdAt);
        $this->assertSame(OnUpdate::class, $data->lastEvent);
    }

    private function createPost(): Post
    {
        $post = new Post();
        $post->title = 'TEST';

        $this->save($post);

        return $post;
    }
}
