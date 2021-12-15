<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\CreatedAt;

use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseListenerTest;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CreatedAt\Post;
use Cycle\ORM\Entity\Behavior\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Behavior\Listener\CreatedAt;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

abstract class ListenerTest extends BaseListenerTest
{
    use TableTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            'posts',
            [
                'id' => 'primary',
                'created_at' => 'datetime,nullable',
                'custom_created_at' => 'datetime,nullable',
                'content' => 'string,nullable'
            ]
        );

        $this->withSchema(new Schema([
            Post::class => [
                SchemaInterface::ROLE => 'post',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'posts',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => [
                    'id' => 'id',
                    'createdAt' => 'created_at',
                    'customCreatedAt' => 'custom_created_at',
                    'content' => 'content'
                ],
                SchemaInterface::MACROS => [
                    CreatedAt::class,
                    [
                        CreatedAt::class,
                        ['field' => 'customCreatedAt']
                    ]
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'createdAt' => 'datetime',
                    'customCreatedAt' => 'datetime'
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }

    public function testCreate(): void
    {
        $post = new Post();

        $this->save($post);

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();
        $this->assertNotNull($data->createdAt);
        $this->assertNotNull($data->customCreatedAt);
    }

    public function testUpdate(): void
    {
        $post = new Post();
        $this->save($post);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Post::class);

        $post = $select->fetchOne();

        $createdAt = $post->createdAt;
        $customCreatedAt = $post->customCreatedAt;
        $post->content = 'test';

        $this->save($post);

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();

        $this->assertSame(0, $data->createdAt <=> $createdAt);
        $this->assertSame(0, $data->customCreatedAt <=> $customCreatedAt);
        $this->assertSame('test', $data->content);
    }
}
