<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Uuid;

use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Macros\EventDrivenCommandGenerator;
use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Nonstandard\Uuid as NonstandardUuid;
use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Uuid;
use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\UuidFactory;
use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\UuidTypecast;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\User;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Macros\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Macros\Uuid\UuidFactoryInterface;
use Cycle\ORM\Entity\Macros\Uuid\UuidListener;
use Cycle\ORM\Factory;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Spiral\Core\Container;
use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Rfc4122;

abstract class RamseyUuidTest extends BaseTest
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

    public function testCreateUuidV1(): void
    {
        $this->withMacro([UuidListener::class, ['version' => UuidListener::UUID_V1]]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(Uuid::class, $data->uuid);
        $this->assertInstanceOf(Rfc4122\UuidV1::class, $data->uuid);
        $this->assertIsString($data->uuid->toString());
    }

    public function testCreateUuidV2(): void
    {
        $this->withMacro([
            UuidListener::class,
            [
                'version' => UuidListener::UUID_V2,
                'options' => [
                    'localDomain' => Uuid::DCE_DOMAIN_PERSON
                ]
            ]
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(Uuid::class, $data->uuid);
        $this->assertInstanceOf(Rfc4122\UuidV2::class, $data->uuid);
        $this->assertIsString($data->uuid->toString());
    }

    public function testCreateUuidV3(): void
    {
        $this->withMacro([
            UuidListener::class,
            [
                'version' => UuidListener::UUID_V3,
                'options' => [
                    'namespace' => Uuid::NAMESPACE_DNS,
                    'name' => 'python.org'
                ]
            ]
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(Uuid::class, $data->uuid);
        $this->assertInstanceOf(Rfc4122\UuidV3::class, $data->uuid);
        $this->assertIsString($data->uuid->toString());
    }

    public function testCreateUuidV4(): void
    {
        $this->withMacro([UuidListener::class]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(Uuid::class, $data->uuid);
        $this->assertInstanceOf(Rfc4122\UuidV4::class, $data->uuid);
        $this->assertIsString($data->uuid->toString());
    }

    public function testCreateUuidV5(): void
    {
        $this->withMacro([
            UuidListener::class,
            [
                'version' => UuidListener::UUID_V5,
                'options' => [
                    'namespace' => Uuid::NAMESPACE_DNS,
                    'name' => 'python.org'
                ]
            ]
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(Uuid::class, $data->uuid);
        $this->assertInstanceOf(Rfc4122\UuidV5::class, $data->uuid);
        $this->assertIsString($data->uuid->toString());
    }

    public function testCreateUuidV6(): void
    {
        $this->withMacro([UuidListener::class, ['version' => UuidListener::UUID_V6]]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(Uuid::class, $data->uuid);
        $this->assertInstanceOf(NonstandardUuid::class, $data->uuid);
        $this->assertIsString($data->uuid->toString());
    }

    private function withMacro(array $macro): void
    {
        $schema = new Schema([
            User::class => [
                SchemaInterface::ROLE => 'user',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'users',
                SchemaInterface::PRIMARY_KEY => 'uuid',
                SchemaInterface::COLUMNS => ['uuid'],
                SchemaInterface::MACROS => [$macro],
                SchemaInterface::TYPECAST => [
                    'uuid' => [UuidTypecast::class, 'cast']
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]);

        $container = new Container();
        $container->bind(UuidFactoryInterface::class, UuidFactory::class);

        $this->orm = new ORM(
            new Factory(
                $this->dbal,
                RelationConfig::getDefault(),
                null,
                new ArrayCollectionFactory()
            ),
            $schema,
            new EventDrivenCommandGenerator($schema, $container)
        );
    }
}
