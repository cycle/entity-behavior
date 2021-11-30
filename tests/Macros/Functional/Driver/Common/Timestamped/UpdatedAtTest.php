<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Timestamped;

use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Macros\EventDrivenCommandGenerator;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\Post;
use Cycle\ORM\Entity\Macros\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Macros\Tests\Utils\SimpleContainer;
use Cycle\ORM\Entity\Macros\Timestamped\UpdatedAtListener;
use Cycle\ORM\Factory;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

abstract class UpdatedAtTest extends BaseTest
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

        $schema = new Schema([
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
                SchemaInterface::MACROS => [
                    UpdatedAtListener::class,
                    [
                        UpdatedAtListener::class,
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
