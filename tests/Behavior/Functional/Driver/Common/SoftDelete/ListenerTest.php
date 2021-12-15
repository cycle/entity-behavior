<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\SoftDelete;

use Cycle\ORM\Entity\Behavior\Listener\SoftDelete;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseListenerTest;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\SoftDelete\Post;
use Cycle\ORM\Entity\Behavior\Tests\Traits\TableTrait;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction;

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
                'deleted_at' => 'datetime,nullable',
                'custom_deleted_at' => 'datetime,nullable',
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
                    'deletedAt' => 'deleted_at',
                    'customDeletedAt' => 'custom_deleted_at',
                    'content' => 'content'
                ],
                SchemaInterface::MACROS => [
                    SoftDelete::class,
                    [
                        SoftDelete::class,
                        ['field' => 'customDeletedAt']
                    ]
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'deletedAt' => 'datetime',
                    'customDeletedAt' => 'datetime'
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }
    public function testDelete(): void
    {
        $this->save(new Post());

        $select = new Select($this->orm, Post::class);
        $post = $select->fetchOne();

        $this->assertNull($post->deletedAt);
        $this->assertNull($post->customDeletedAt);

        $tr = new Transaction($this->orm);
        $tr->delete($post);
        $tr->run();

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(Post::class, $data);
        $this->assertNotNull($data->deletedAt);
        $this->assertNotNull($data->customDeletedAt);
    }
}
