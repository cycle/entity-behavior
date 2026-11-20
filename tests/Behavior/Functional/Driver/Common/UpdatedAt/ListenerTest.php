<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\UpdatedAt;

use Cycle\ORM\Entity\Behavior\Listener\UpdatedAt;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\UpdatedAt\Post;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseListenerTest;
use Cycle\ORM\Entity\Behavior\Tests\Traits\TableTrait;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

abstract class ListenerTest extends BaseListenerTest
{
    use TableTrait;

    #[\Testo\Test]
    public function testCreate(): void
    {
        $post = new Post();

        $this->save($post);

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();
        \Testo\Assert::null($data->updatedAt);
        \Testo\Assert::null($data->customUpdatedAt);
    }

    #[\Testo\Test]
    public function testCreateNotNullable(): void
    {
        $post = new Post();

        $this->save($post);

        $this->orm->getHeap()->clean();
        $select = new Select($this->orm, Post::class);
        $data = $select->fetchOne();

        \Testo\Assert::instanceOf($data->notNullableUpdatedAt, \DateTimeImmutable::class);
    }

    #[\Testo\Test]
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

        \Testo\Assert::numeric($data->updatedAt <=> $updatedAt)->greaterThan(0);
        \Testo\Assert::numeric($data->customUpdatedAt <=> $customUpdatedAt)->greaterThan(0);
        \Testo\Assert::same($data->content, 'test');
    }

    #[\Testo\Lifecycle\BeforeTest]
    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            'posts',
            [
                'id' => 'primary',
                'updated_at' => 'datetime,nullable',
                'custom_updated_at' => 'datetime,nullable',
                'not_nullable_updated_at' => 'datetime',
                'content' => 'string,nullable',
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
                    'updatedAt' => 'updated_at',
                    'customUpdatedAt' => 'custom_updated_at',
                    'notNullableUpdatedAt' => 'not_nullable_updated_at',
                    'content' => 'content',
                ],
                SchemaInterface::LISTENERS => [
                    [
                        UpdatedAt::class,
                        ['nullable' => true],
                    ],
                    [
                        UpdatedAt::class,
                        ['field' => 'customUpdatedAt', 'nullable' => true],
                    ],
                    [
                        UpdatedAt::class,
                        ['field' => 'notNullableUpdatedAt', 'nullable' => false],
                    ],
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'updatedAt' => 'datetime',
                    'customUpdatedAt' => 'datetime',
                    'notNullableUpdatedAt' => 'datetime',
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }
}
