<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\UpdatedAt;

use Cycle\ORM\Entity\Behavior\Listener\UpdatedAt;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\UpdatedAt\Post;
use Cycle\ORM\Entity\Behavior\Tests\Support\ListenerTestCase;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class ListenerTest extends ListenerTestCase
{
    #[Test]
    public function testCreate(): void
    {
        $post = new Post();

        $this->save($post);

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();
        Assert::null($data->updatedAt);
        Assert::null($data->customUpdatedAt);
    }

    #[Test]
    public function testCreateNotNullable(): void
    {
        $post = new Post();

        $this->save($post);

        $this->orm->getHeap()->clean();
        $select = new Select($this->orm, Post::class);
        $data = $select->fetchOne();

        Assert::instanceOf($data->notNullableUpdatedAt, \DateTimeImmutable::class);
    }

    #[Test]
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

        Assert::numeric($data->updatedAt <=> $updatedAt)->greaterThan(0);
        Assert::numeric($data->customUpdatedAt <=> $customUpdatedAt)->greaterThan(0);
        Assert::same($data->content, 'test');
    }

    #[BeforeTest]
    protected function setUp(): void
    {
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
