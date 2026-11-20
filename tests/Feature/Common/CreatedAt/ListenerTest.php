<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\CreatedAt;

use Cycle\ORM\Entity\Behavior\Listener\CreatedAt;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CreatedAt\Post;
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
        Assert::notNull($data->createdAt);
        Assert::notNull($data->customCreatedAt);
    }

    #[Test]
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

        Assert::same($data->createdAt <=> $createdAt, 0);
        Assert::same($data->customCreatedAt <=> $customCreatedAt, 0);
        Assert::same($data->content, 'test');
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->makeTable(
            'posts',
            [
                'id' => 'primary',
                'created_at' => 'datetime,nullable',
                'custom_created_at' => 'datetime,nullable',
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
                    'createdAt' => 'created_at',
                    'customCreatedAt' => 'custom_created_at',
                    'content' => 'content',
                ],
                SchemaInterface::LISTENERS => [
                    CreatedAt::class,
                    [
                        CreatedAt::class,
                        ['field' => 'customCreatedAt'],
                    ],
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'createdAt' => 'datetime',
                    'customCreatedAt' => 'datetime',
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }
}
