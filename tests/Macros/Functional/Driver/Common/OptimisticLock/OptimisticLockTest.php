<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\OptimisticLock;

use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Macros\EventDrivenCommandGenerator;
use Cycle\ORM\Entity\Macros\OptimisticLock\OptimisticLockException;
use Cycle\ORM\Entity\Macros\OptimisticLock\OptimisticLockListener;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\Comment;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Macros\Tests\Traits\TableTrait;
use Cycle\ORM\Factory;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

abstract class OptimisticLockTest extends BaseTest
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
                'content' => 'string,nullable',
            ]
        );

        $schema = new Schema([
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
                    'content' => 'content'
                ],
                SchemaInterface::MACROS => [
                    [
                        OptimisticLockListener::class,
                        ['field' => 'versionInt']
                    ],
                    [
                        OptimisticLockListener::class,
                        ['field' => 'versionStr', 'rule' => OptimisticLockListener::RULE_RAND_STR]
                    ],
                    [
                        OptimisticLockListener::class,
                        ['field' => 'versionDatetime', 'rule' => OptimisticLockListener::RULE_DATETIME]
                    ],
                    [
                        OptimisticLockListener::class,
                        ['field' => 'versionMicrotime', 'rule' => OptimisticLockListener::RULE_MICROTIME]
                    ]
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'versionInt' => 'int',
                    'versionDatetime' => 'datetime'
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
            new EventDrivenCommandGenerator($schema)
        );
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
        $this->assertInstanceOf(\DateTimeImmutable::class, $comment->versionDatetime);
        $this->assertIsString($comment->versionMicrotime);
        $this->assertIsString($comment->versionStr);
        $this->assertSame('test', $comment->content);

        $versionMicrotime = $comment->versionMicrotime;
        $versionStr = $comment->versionStr;
        $comment->content = 'changed';

        $this->save($comment);

        $select = new Select($this->orm->with(heap: new Heap()), Comment::class);
        $data = $select->fetchOne();

        $this->assertSame('changed', $data->content);
        $this->assertSame(2, $data->versionInt);
        $this->assertNotSame($versionMicrotime, $data->versionMicrotime);
        $this->assertNotSame($versionStr, $data->versionStr);
    }

    public function testLockInt(): void
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();

        $this->assertSame(1, $comment->versionInt);

        // other operation changed version
        $comment->versionInt = 2;
        $comment->content = 'changed';

        $this->expectException(OptimisticLockException::class);
        $this->expectExceptionMessage('The `comment` record is locked.');

        $this->save($comment);
    }

    public function testLockString(): void
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();

        $this->assertIsString($comment->versionStr);

        // other operation changed version
        $comment->versionStr = \random_bytes(32);
        $comment->content = 'changed';

        $this->expectException(OptimisticLockException::class);
        $this->expectExceptionMessage('The `comment` record is locked.');

        $this->save($comment);
    }

    public function testLockMicrotime(): void
    {
        $comment = new Comment();
        $comment->content = 'test';

        $this->save($comment);

        $this->orm = $this->orm->with(heap: new Heap());
        $select = new Select($this->orm, Comment::class);

        $comment = $select->fetchOne();

        $this->assertIsString($comment->versionMicrotime);

        // other operation changed version
        $comment->versionMicrotime = number_format(microtime(true), 6);
        $comment->content = 'changed';

        $this->expectException(OptimisticLockException::class);
        $this->expectExceptionMessage('The `comment` record is locked.');

        $this->save($comment);
    }
}
