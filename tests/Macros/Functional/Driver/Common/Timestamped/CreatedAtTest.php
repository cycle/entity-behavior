<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Timestamped;

use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Macros\EventDrivenCommandGenerator;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\Post;
use Cycle\ORM\Entity\Macros\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Macros\Timestamped\CreatedAtListener;
use Cycle\ORM\Factory;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Spiral\Core\Container;

abstract class CreatedAtTest extends BaseTest
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

        $schema = new Schema([
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
                    [
                        CreatedAtListener::class
                    ],
                    [
                        CreatedAtListener::class,
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
        ]);
        $this->orm = new ORM(
            new Factory(
                $this->dbal,
                RelationConfig::getDefault(),
                null,
                new ArrayCollectionFactory()
            ),
            $schema,
            new EventDrivenCommandGenerator($schema, new Container())
        );
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
