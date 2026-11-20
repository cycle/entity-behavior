<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\SoftDelete;

use Cycle\ORM\Entity\Behavior\Listener\SoftDelete;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\SoftDelete\Post;
use Cycle\ORM\Entity\Behavior\Tests\Support\ListenerTestCase;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction;

final class ListenerTest extends ListenerTestCase
{
    #[\Testo\Test]
    public function testDelete(): void
    {
        $this->save(new Post());

        $select = new Select($this->orm, Post::class);
        $post = $select->fetchOne();

        \Testo\Assert::null($post->deletedAt);
        \Testo\Assert::null($post->customDeletedAt);

        $tr = new Transaction($this->orm);
        $tr->delete($post);
        $tr->run();

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();

        \Testo\Assert::instanceOf($data, Post::class);
        \Testo\Assert::notNull($data->deletedAt);
        \Testo\Assert::notNull($data->customDeletedAt);
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $this->makeTable(
            'posts',
            [
                'id' => 'primary',
                'deleted_at' => 'datetime,nullable',
                'custom_deleted_at' => 'datetime,nullable',
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
                    'deletedAt' => 'deleted_at',
                    'customDeletedAt' => 'custom_deleted_at',
                    'content' => 'content',
                ],
                SchemaInterface::LISTENERS => [
                    SoftDelete::class,
                    [
                        SoftDelete::class,
                        ['field' => 'customDeletedAt'],
                    ],
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'deletedAt' => 'datetime',
                    'customDeletedAt' => 'datetime',
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }
}
