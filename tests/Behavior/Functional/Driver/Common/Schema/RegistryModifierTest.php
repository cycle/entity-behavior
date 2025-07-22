<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Schema;

use Cycle\Database\ColumnInterface;
use Cycle\ORM\Entity\Behavior\Exception\BehaviorCompilationException;
use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CustomTypecast;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Parser\Typecast;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Registry;
use Ramsey\Uuid\Uuid;

abstract class RegistryModifierTest extends BaseTest
{
    private const ROLE_TEST = 'test';

    protected RegistryModifier $modifier;
    protected Registry $registry;

    public function testAddDatetimeField(): void
    {
        $this->modifier->addDatetimeColumn('created_at', 'createdAt');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('createdAt'));
        $this->assertSame('datetime', $fields->get('createdAt')->getType());
        $this->assertSame('created_at', $fields->get('createdAt')->getColumn());
    }

    public function testAddStringField(): void
    {
        $this->modifier->addStringColumn('version_str', 'version');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('version'));
        $this->assertSame(ColumnInterface::STRING, $fields->get('version')->getType());
        $this->assertSame('version_str', $fields->get('version')->getColumn());
    }

    public function testAddIntegerField(): void
    {
        $this->modifier->addIntegerColumn('version_int', 'version');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('version'));
        $this->assertSame('integer', $fields->get('version')->getType());
        $this->assertSame('version_int', $fields->get('version')->getColumn());
    }

    public function testAddBigIntegerField(): void
    {
        $this->modifier->addBigIntegerColumn('snowflake_column', 'snowflake');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('snowflake'));
        $this->assertSame('bigInteger', $fields->get('snowflake')->getType());
        $this->assertSame('snowflake_column', $fields->get('snowflake')->getColumn());
    }

    public function testAddBigIntegerFieldThrowsException(): void
    {
        $this->modifier->addIntegerColumn('snowflake_column', 'snowflake');

        $this->expectException(BehaviorCompilationException::class);
        $this->expectExceptionMessage('Field snowflake must be of type big integer.');

        $this->modifier->addBigIntegerColumn('snowflake_column', 'snowflake');
    }

    public function testAddSnowflakeField(): void
    {
        $this->modifier->addSnowflakeColumn('snowflake_column', 'snowflake');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('snowflake'));
        $this->assertSame('snowflake', $fields->get('snowflake')->getType());
        $this->assertSame('snowflake_column', $fields->get('snowflake')->getColumn());
    }

    public function testAddSnowflakeFieldThrowsException(): void
    {
        $this->modifier->addStringColumn('snowflake_column', 'snowflake');

        $this->expectException(BehaviorCompilationException::class);
        $this->expectExceptionMessage('Field snowflake must be of type snowflake.');

        $this->modifier->addSnowflakeColumn('snowflake_column', 'snowflake');
    }

    public function testAddUlidField(): void
    {
        $this->modifier->addUlidColumn('ulid_column', 'ulid');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('ulid'));
        $this->assertSame('ulid', $fields->get('ulid')->getType());
        $this->assertSame('ulid_column', $fields->get('ulid')->getColumn());
    }

    public function testAddUlidFieldThrowsException(): void
    {
        $this->modifier->addIntegerColumn('ulid_column', 'ulid');

        $this->expectException(BehaviorCompilationException::class);
        $this->expectExceptionMessage('Field ulid must be of type ulid.');

        $this->modifier->addUlidColumn('ulid_column', 'ulid');
    }

    public function testAddUuidField(): void
    {
        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('uuid'));
        $this->assertSame('uuid', $fields->get('uuid')->getType());
        $this->assertSame('uuid_column', $fields->get('uuid')->getColumn());
    }

    public function testAddUuidFieldThrowsException(): void
    {
        $this->modifier->addIntegerColumn('uuid_column', 'uuid');

        $this->expectException(BehaviorCompilationException::class);
        $this->expectExceptionMessage('Field uuid must be of type uuid.');

        $this->modifier->addUuidColumn('uuid_column', 'uuid');
    }

    public function testAddTypecast(): void
    {
        $this->modifier->addUuidColumn('uuid_column', 'uuid');
        $this->modifier->addIntegerColumn('counter_column', 'counter');
        $this->modifier->addBigIntegerColumn('snowflake_column', 'snowflake');
        $field1 = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid');
        $field2 = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('counter');
        $field3 = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('snowflake');

        $this->modifier->setTypecast($field1, [Uuid::class, 'fromString']);
        $this->modifier->setTypecast($field2, 'int', CustomTypecast::class);
        $this->modifier->setTypecast($field3, 'int', CustomTypecast::class);

        // field has custom UUID typecast
        $this->assertSame([Uuid::class, 'fromString'], $field1->getTypecast());
        $this->assertSame('int', $field2->getTypecast());
        $this->assertSame('int', $field3->getTypecast());

        // entity has default typecast
        $this->assertSame(
            [Typecast::class, CustomTypecast::class],
            $this->registry->getEntity(self::ROLE_TEST)->getTypecast(),
        );
    }

    public function testAddTypecastEntityWithTypecast(): void
    {
        $this->registry->getEntity(self::ROLE_TEST)->setTypecast(CustomTypecast::class);

        $this->modifier->addUuidColumn('uuid_column', 'uuid');
        $field = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid');

        $this->modifier->setTypecast($field, [Uuid::class, 'fromString']);

        // field has custom UUID typecast
        $this->assertSame([Uuid::class, 'fromString'], $field->getTypecast());

        // entity has default typecast and custom typecast
        $this->assertSame(
            [CustomTypecast::class, Typecast::class],
            $this->registry->getEntity(self::ROLE_TEST)->getTypecast(),
        );
    }

    public function testAddTypecastShouldBeSkipped(): void
    {
        $this->registry->getEntity(self::ROLE_TEST);

        $this->modifier->addUuidColumn('uuid_column', 'uuid');
        $this->registry->getDefaults()->offsetSet(SchemaInterface::TYPECAST_HANDLER, Typecast::class);

        $this->assertNull($this->registry->getEntity(self::ROLE_TEST)->getTypecast());
    }

    public function testAddTypecastShouldBeDuplicated(): void
    {
        $this->registry->getEntity(self::ROLE_TEST)->setTypecast(CustomTypecast::class);

        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $this->assertSame(CustomTypecast::class, $this->registry->getEntity(self::ROLE_TEST)->getTypecast());
    }

    public function testCustomTypecastNotOverridden(): void
    {
        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $field = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid');
        $field->setTypecast(['foo', 'bar']);

        $this->modifier->setTypecast($field, [Uuid::class, 'fromString']);

        $this->assertSame(['foo', 'bar'], $field->getTypecast());
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->registry = new Registry($this->dbal);

        $entity = (new Entity())->setRole(self::ROLE_TEST);
        $this->registry->register($entity);
        $this->registry->linkTable($entity, 'default', 'tests');

        $this->modifier = new RegistryModifier($this->registry, self::ROLE_TEST);
    }
}
