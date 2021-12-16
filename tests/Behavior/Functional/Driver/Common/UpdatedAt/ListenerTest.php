<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\UpdatedAt;

use Cycle\ORM\Entity\Behavior\Listener\UpdatedAt;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseListenerTest;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\UpdatedAt\Post;
use Cycle\ORM\Entity\Behavior\Tests\Traits\TableTrait;
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
                'updated_at' => 'datetime,nullable',
                'custom_updated_at' => 'datetime,nullable',
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
                    'updatedAt' => 'updated_at',
                    'customUpdatedAt' => 'custom_updated_at',
                    'content' => 'content'
                ],
                SchemaInterface::LISTENERS => [
                    UpdatedAt::class,
                    [
                        UpdatedAt::class,
                        ['field' => 'customUpdatedAt']
                    ]
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'updatedAt' => 'datetime',
                    'customUpdatedAt' => 'datetime'
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
        $this->assertNull($data->updatedAt);
        $this->assertNull($data->customUpdatedAt);
    }

    public function testUpdate(): void
    {
        $post = new Post();
        $this->save($post);

        $select = new Select($this->orm, Post::class);

        $post = $select->fetchOne();

        $updatedAt = $post->updatedAt;
        $customUpdatedAt = $post->customUpdatedAt;
        $post->content = 'test';

        $this->save($post);

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();

        $this->assertGreaterThan(0, $data->updatedAt <=> $updatedAt);
        $this->assertGreaterThan(0, $data->customUpdatedAt <=> $customUpdatedAt);
        $this->assertSame('test', $data->content);
    }
}
