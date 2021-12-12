<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\OptimisticLock;

use Cycle\ORM\Entity\Macros\Exception\OptimisticLock\ChangedVersionException;
use Cycle\ORM\Entity\Macros\Exception\OptimisticLock\RecordIsLockedException;
use Cycle\ORM\Entity\Macros\Listener\OptimisticLock;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\OptimisticLock\Comment;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseListenerTest;
use Cycle\ORM\Entity\Macros\Tests\Traits\TableTrait;
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
            'comments',
            [
                'id' => 'primary',
                'version_int' => 'int',
                'version_str' => 'string',
                'version_datetime' => 'datetime',
                'version_microtime' => 'string',
                'version_custom' => 'int,nullable',
                'content' => 'string,nullable',
            ]
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
                    'content' => 'content'
                ],
                SchemaInterface::MACROS => [
                    [
                        OptimisticLock::class,
                        ['field' => 'versionInt']
                    ],
                    [
                        OptimisticLock::class,
                        ['field' => 'versionStr', 'rule' => OptimisticLock::RULE_RAND_STR]
                    ],
                    [
                        OptimisticLock::class,
                        ['field' => 'versionDatetime', 'rule' => OptimisticLock::RULE_DATETIME]
                    ],
                    [
                        OptimisticLock::class,
                        ['field' => 'versionMicrotime', 'rule' => OptimisticLock::RULE_MICROTIME]
                    ],
                    [
                        OptimisticLock::class,
                        ['field' => 'versionCustom', 'rule' => OptimisticLock::RULE_MANUAL]
                    ]
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'versionInt' => 'int',
                    'versionCustom' => 'int',
                    'versionDatetime' => 'datetime'
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }

    public function testAddVersionOnCreate()
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();

        $this->assertSame(1, $comment->versionInt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $comment->versionDatetime);
        $this->assertIsString($comment->versionMicrotime);
        $this->assertIsString($comment->versionStr);
    }

    public function testManualAddVersionOnCreate()
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

        $this->assertSame(3, $comment->versionInt);
        $this->assertSame(1638275394, $comment->versionDatetime->getTimestamp());
        $this->assertSame('1638275263.792011', $comment->versionMicrotime);
        $this->assertSame('b0b55bb7237bb75611f7df8175e926d1', $comment->versionStr);
    }

    public function testManualVersionControl()
    {
        $comment = new Comment();
        $comment->content = 'test';
        $comment->versionCustom = 1;

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();
        $this->assertSame(1, $comment->versionCustom);

        $comment->versionCustom = 2;
        $this->save($comment);

        $this->assertSame(2, $comment->versionCustom);
    }

    public function testExceptionOnChangeVersion()
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();
        $this->assertSame(1, $comment->versionInt);

        $comment->versionInt = 2;

        $this->expectException(ChangedVersionException::class);
        $this->expectExceptionMessage('Record version change detected. Old value `1`, a new value `2`');
        $this->save($comment);
    }

    public function testUpdate(): void
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();

        $this->assertSame(1, $comment->versionInt);
        $this->assertSame('test', $comment->content);

        $comment->content = 'changed';

        $this->save($comment);

        $select = new Select($this->orm->with(heap: new Heap()), Comment::class);
        $data = $select->fetchOne();

        $this->assertSame('changed', $data->content);
        $this->assertSame(2, $data->versionInt);
    }

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

        $this->expectException(RecordIsLockedException::class);
        $this->expectExceptionMessage('The `comment` record is locked.');
        (new Transaction($orm2))->persist($data2)->run();
    }
}
