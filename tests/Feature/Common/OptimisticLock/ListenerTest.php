<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\OptimisticLock;

use Cycle\ORM\Entity\Behavior\Exception\OptimisticLock\ChangedVersionException;
use Cycle\ORM\Entity\Behavior\Exception\OptimisticLock\RecordIsLockedException;
use Cycle\ORM\Entity\Behavior\Listener\OptimisticLock;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Author;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Comment;
use Cycle\ORM\Entity\Behavior\Tests\Support\ListenerTestCase;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction;
use Testo\Assert;
use Testo\Expect;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class ListenerTest extends ListenerTestCase
{
    #[Test]
    public function testAddVersionOnCreate(): void
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();

        Assert::same($comment->versionInt, 1);
        Assert::instanceOf($comment->versionDatetime, \DateTimeImmutable::class);
        Assert::string($comment->versionMicrotime);
        Assert::string($comment->versionStr);
    }

    #[Test]
    public function testManualAddVersionOnCreate(): void
    {
        $comment = new Comment();
        $comment->content = 'test';
        $comment->versionInt = 3;
        $comment->versionStr = 'b0b55bb7237bb75611f7df8175e926d1';
        $comment->versionMicrotime = '1638275263.792011';
        $comment->versionDatetime = (new \DateTimeImmutable())->setTimestamp(1638275394);

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();

        Assert::same($comment->versionInt, 3);
        Assert::same($comment->versionDatetime->getTimestamp(), 1638275394);
        Assert::same($comment->versionMicrotime, '1638275263.792011');
        Assert::same($comment->versionStr, 'b0b55bb7237bb75611f7df8175e926d1');
    }

    #[Test]
    public function testManualVersionControl(): void
    {
        $comment = new Comment();
        $comment->content = 'test';
        $comment->versionCustom = 1;

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();
        Assert::same($comment->versionCustom, 1);

        $comment->versionCustom = 2;
        $this->save($comment);

        Assert::same($comment->versionCustom, 2);
    }

    #[Test]
    public function testExceptionOnChangeVersion(): void
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();
        Assert::same($comment->versionInt, 1);

        $comment->versionInt = 2;

        Expect::exception(ChangedVersionException::class)->withMessageContaining('Record version change detected. Old value `1`, a new value `2`');
        $this->save($comment);
    }

    #[Test]
    public function testUpdate(): void
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();

        Assert::same($comment->versionInt, 1);
        Assert::same($comment->content, 'test');

        $comment->content = 'changed';

        $this->save($comment);

        $select = new Select($this->orm->with(heap: new Heap()), Comment::class);
        $data = $select->fetchOne();

        Assert::same($data->content, 'changed');
        Assert::same($data->versionInt, 2);
    }

    #[Test]
    public function testLock(): void
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $orm = $this->orm->with(heap: new Heap());
        $orm2 = $this->orm->with(heap: new Heap());

        $data1 = (new Select($orm, Comment::class))->fetchOne();
        $data2 = (new Select($orm2, Comment::class))->fetchOne();

        $data1->content = 'changed';
        $data2->content = 'changed2';

        (new Transaction($orm))->persist($data1)->run();

        Expect::exception(RecordIsLockedException::class)->withMessageContaining('The `comment` record is locked.');
        (new Transaction($orm2))->persist($data2)->run();
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->makeTable(
            'comments',
            [
                'id' => 'primary',
                'version_int' => 'int',
                'version_str' => 'string',
                'version_datetime' => 'datetime',
                'version_microtime' => 'string',
                'version_custom' => 'int,nullable',
                'content' => 'string,nullable',
                'author_first_name' => 'string',
                'author_last_name' => 'string',
            ],
        );

        $this->withSchema(new Schema([
            Comment::class => [
                SchemaInterface::ROLE => 'comment',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'comments',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => [
                    'id' => 'id',
                    'versionInt' => 'version_int',
                    'versionStr' => 'version_str',
                    'versionDatetime' => 'version_datetime',
                    'versionMicrotime' => 'version_microtime',
                    'versionCustom' => 'version_custom',
                    'content' => 'content',
                ],
                SchemaInterface::LISTENERS => [
                    [
                        OptimisticLock::class,
                        ['field' => 'versionInt'],
                    ],
                    [
                        OptimisticLock::class,
                        ['field' => 'versionStr', 'rule' => OptimisticLock::RULE_RAND_STR],
                    ],
                    [
                        OptimisticLock::class,
                        ['field' => 'versionDatetime', 'rule' => OptimisticLock::RULE_DATETIME],
                    ],
                    [
                        OptimisticLock::class,
                        ['field' => 'versionMicrotime', 'rule' => OptimisticLock::RULE_MICROTIME],
                    ],
                    [
                        OptimisticLock::class,
                        ['field' => 'versionCustom', 'rule' => OptimisticLock::RULE_MANUAL],
                    ],
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'versionInt' => 'int',
                    'versionCustom' => 'int',
                    'versionDatetime' => 'datetime',
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [
                    'author' => [
                        Relation::TYPE => Relation::EMBEDDED,
                        Relation::TARGET => Author::class,
                        Relation::LOAD => Relation::LOAD_EAGER,
                        Relation::SCHEMA => [],
                    ],
                ],
            ],
            Author::class => [
                SchemaInterface::ENTITY => Author::class,
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'comments',
                SchemaInterface::PRIMARY_KEY => ['id'],
                SchemaInterface::COLUMNS => [
                    'first_name' => 'author_first_name',
                    'last_name' => 'author_last_name',
                ],
            ],
        ]));
    }
}
