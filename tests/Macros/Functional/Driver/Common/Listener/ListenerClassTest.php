<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Listener;

use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Macros\EventDrivenCommandGenerator;
use Cycle\ORM\Entity\Macros\Listener\ListenerClassListener;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\Post;
use Cycle\ORM\Entity\Macros\Tests\Traits\TableTrait;
use Cycle\ORM\Factory;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

abstract class ListenerClassTest extends BaseTest
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
                    'createdAt' => 'created_at',
                    'updatedAt' => 'updated_at',
                ],
                // macros for different events and with different parameters variants
                SchemaInterface::MACROS => [
                    [
                        ListenerClassListener::class,
                        ['class' => PostService::class, 'parameters' => 'modified content']
                    ],
                    [
                        ListenerClassListener::class,
                        ['class' => Post::class, 'method' => 'onCreate']
                    ],
                    [
                        ListenerClassListener::class,
                        ['class' => Post::class, 'method' => 'touch']
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
            new EventDrivenCommandGenerator($schema)
        );
    }

    public function testCreate(): void
    {
        $post = new Post();
        $post->title = 'TEST';

        $this->save($post);

        $select = new Select($this->orm->withHeap(new Heap()), Post::class);
        $data = $select->fetchOne();

        $this->assertNotNull($data->createdAt);
        $this->assertNotNull($data->updatedAt);
        $this->assertNull($data->content);
        $this->assertSame('test', $post->slug);
    }

    public function testUpdate(): void
    {
        $post = new Post();
        $post->title = 'TEST';

        $this->save($post);

        $this->orm = $this->orm->withHeap(new Heap());
        $select = new Select($this->orm, Post::class);

        $post = $select->fetchOne();
        $post->title = 'TEST 2';

        $content = $post->content;
        $createdAt = $post->createdAt;
        $updatedAt = $post->updatedAt;

        $this->save($post);

        $select = new Select($this->orm->withHeap(new Heap()), Post::class);
        $data = $select->fetchOne();

        // Triggered OnUpdate event
        $this->assertNull($content);
        $this->assertSame('modified content', $data->content);


        // Not triggered OnCreate event, slug NOT changed.
        $this->assertSame('test', $data->slug);

        // Triggered union type event OnCreate|OnUpdate
        $this->assertSame(0, $data->createdAt <=> $createdAt);
        $this->assertSame(0, $data->updatedAt <=> $updatedAt);

        $this->assertSame('TEST 2', $data->title);
    }
}
