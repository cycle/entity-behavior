<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\EventListener;

use Cycle\ORM\Entity\Behavior\Tests\Fixtures\EventListener\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\PostService;
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
    public function testApply(): void
    {
        $post = new Post();

        $this->save($post);

        $select = new Select($this->orm->with(heap: new Heap()), Post::class);
        $data = $select->fetchOne();

        Assert::same($data->title, 'modified by EventListener');
        Assert::same($data->content, 'baz');
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->makeTable(
            'posts',
            [
                'id' => 'primary',
                'title' => 'string,nullable',
                'content' => 'string,nullable',
            ],
        );

        $this->withSchema(new Schema([
            Post::class => [
                SchemaInterface::ROLE => 'post',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'posts',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => ['id', 'title', 'content'],
                SchemaInterface::LISTENERS => [
                    [
                        PostService::class,
                        ['foo' => 'modified by EventListener', 'bar' => ['baz']],
                    ],
                ],
                SchemaInterface::TYPECAST => ['id' => 'int'],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }
}
