<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\EventListener;

use Cycle\ORM\Entity\Behavior\Exception\Dispatcher\RuntimeException;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\AuditService;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\BrokenEventListService;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\EventListener\Post;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\PostService;
use Cycle\ORM\Entity\Behavior\Tests\Support\ListenerTestCase;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction;
use Testo\Assert;
use Testo\Assert\Api\ExpectedException;
use Testo\Expect;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class ListenerProviderTest extends ListenerTestCase
{
    #[Test]
    public function testAttributeAndProvidedListenersAllRun(): void
    {
        $this->withListeners(AuditService::class);
        $post = new Post();

        $this->save($post);
        $created = $this->fetchPost();
        $post->title = 'edited';
        $this->save($post);
        $updated = $this->fetchPost();
        (new Transaction($this->orm))->delete($post)->run();

        Assert::same([$created->title, $created->content], ['created', 'created']);
        Assert::same([$updated->title, $updated->content], ['updated', 'updated']);
        Assert::null($this->fetchPost());
    }

    #[Test]
    public function testDefinitionWithNonArrayArgumentsIsSkipped(): void
    {
        $this->withListeners([PostService::class, 'not-an-array'], AuditService::class);

        $this->save(new Post());

        Assert::same($this->fetchPost()->title, 'created');
    }

    #[Test]
    public function testEventListMustHoldEventMethodPairs(): never
    {
        Expect::exception(\Exception::class)
            ->withMessage(\sprintf("Cann't create listener `%s` for the `post` role.", BrokenEventListService::class))
            ->withCode(0)
            ->withPrevious(
                RuntimeException::class,
                static fn(ExpectedException $previous) => $previous->withMessageContaining('should return list of tuples'),
            );

        $this->withListeners(BrokenEventListService::class);
    }

    #[BeforeTest]
    protected function createTable(): void
    {
        $this->makeTable('posts', [
            'id' => 'primary',
            'title' => 'string,nullable',
            'content' => 'string,nullable',
        ]);
    }

    private function withListeners(array|string ...$definitions): void
    {
        $this->withSchema(new Schema([
            Post::class => [
                SchemaInterface::ROLE => 'post',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'posts',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => ['id', 'title', 'content'],
                SchemaInterface::LISTENERS => $definitions,
                SchemaInterface::TYPECAST => ['id' => 'int'],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }

    private function fetchPost(): ?Post
    {
        return (new Select($this->orm->with(heap: new Heap()), Post::class))->fetchOne();
    }
}
