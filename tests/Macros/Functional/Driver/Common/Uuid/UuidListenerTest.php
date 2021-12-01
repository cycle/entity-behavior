<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Uuid;

use Cycle\ORM\Entity\Macros\Tests\Fixtures\Uuid\User;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Macros\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Macros\Uuid\UuidTypecast;
use Cycle\ORM\Entity\Macros\Uuid\UuidV1Listener;
use Cycle\ORM\Entity\Macros\Uuid\UuidV2Listener;
use Cycle\ORM\Entity\Macros\Uuid\UuidV3Listener;
use Cycle\ORM\Entity\Macros\Uuid\UuidV4Listener;
use Cycle\ORM\Entity\Macros\Uuid\UuidV5Listener;
use Cycle\ORM\Entity\Macros\Uuid\UuidV6Listener;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class UuidListenerTest extends BaseTest
{
    use TableTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            'users',
            [
                'uuid' => 'string',
            ]
        );
    }

    public function testAssignMannualy(): void
    {
        $this->withMacros(UuidV4Listener::class);

        $user = new User();
        $user->uuid = Uuid::uuid4();
        $bytes = $user->uuid->getBytes();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertSame($bytes, $data->uuid->getBytes());
    }

    public function testUuidV1(): void
    {
        $this->withMacros([UuidV1Listener::class, ['node' => '00000fffffff', 'clockSeq' => 0xffff]]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidInterface::class, $data->uuid);
        $this->assertSame(1, $data->uuid->getVersion());
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuidV2(): void
    {
        $this->withMacros([
            UuidV2Listener::class,
            [
                'localDomain' => Uuid::DCE_DOMAIN_PERSON,
                'localIdentifier' => new Integer('12345678')
            ]
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidInterface::class, $data->uuid);
        $this->assertSame(2, $data->uuid->getVersion());
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuidV3(): void
    {
        $this->withMacros([
            UuidV3Listener::class,
            [
                'namespace' => Uuid::NAMESPACE_URL,
                'name' => 'https://example.com/foo'
            ]
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidInterface::class, $data->uuid);
        $this->assertSame(3, $data->uuid->getVersion());
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuidV4(): void
    {
        $this->withMacros(UuidV4Listener::class);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidInterface::class, $data->uuid);
        $this->assertSame(4, $data->uuid->getVersion());
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuidV5(): void
    {
        $this->withMacros([
            UuidV5Listener::class,
            ['namespace' => Uuid::NAMESPACE_URL, 'name' => 'https://example.com/foo']
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidInterface::class, $data->uuid);
        $this->assertSame(5, $data->uuid->getVersion());
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuidV6(): void
    {
        $this->withMacros([UuidV6Listener::class, ['node' => new Hexadecimal('0800200c9a66'), 'clockSeq' => 0x1669]]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidInterface::class, $data->uuid);
        $this->assertSame(6, $data->uuid->getVersion());
        $this->assertIsString($data->uuid->toString());
    }

    public function withMacros(array|string $macros): void
    {
        $this->withSchema(new Schema([
            User::class => [
                SchemaInterface::ROLE => 'user',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'users',
                SchemaInterface::PRIMARY_KEY => 'uuid',
                SchemaInterface::COLUMNS => ['uuid'],
                SchemaInterface::MACROS => [$macros],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::TYPECAST => [
                    'uuid' => [UuidTypecast::class, 'cast']
                ]
            ]
        ]));
    }
}
