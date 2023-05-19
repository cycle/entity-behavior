<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Schema;

use Cycle\Database\ColumnInterface;
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

    public function setUp(): void
    {
        parent::setUp();

        $this->registry = new Registry($this->dbal);

        $entity = (new Entity())->setRole(self::ROLE_TEST);
        $this->registry->register($entity);
        $this->registry->linkTable($entity, 'default', 'tests');

        $this->modifier = new RegistryModifier($this->registry, self::ROLE_TEST);
    }

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

    public function testAddUuidField(): void
    {
        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('uuid'));
        $this->assertSame('uuid', $fields->get('uuid')->getType());
        $this->assertSame('uuid_column', $fields->get('uuid')->getColumn());
    }

    public function testAddTypecast(): void
    {
        $this->modifier->addUuidColumn('uuid_column', 'uuid');
        $this->modifier->addIntegerColumn('counter_column', 'counter');
        $field1 = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid');
        $field2 = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('counter');

        $this->modifier->setTypecast($field1, [Uuid::class, 'fromString']);
        $this->modifier->setTypecast($field2, 'int', CustomTypecast::class);

        // field has custom UUID typecast
        $this->assertSame([Uuid::class, 'fromString'], $field1->getTypecast());
        $this->assertSame('int', $field2->getTypecast());

        // entity has default typecast
        $this->assertSame(
            [Typecast::class, CustomTypecast::class],
            $this->registry->getEntity(self::ROLE_TEST)->getTypecast()
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

        // entity has custom typecast
        $this->assertSame(
            [CustomTypecast::class, Typecast::class],
            $this->registry->getEntity(self::ROLE_TEST)->getTypecast()
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
}
