<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\Schema;

use Cycle\Database\ColumnInterface;
use Cycle\ORM\Entity\Behavior\Exception\BehaviorCompilationException;
use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CustomTypecast;
use Cycle\Testing\Database\DatabaseTestCase;
use Cycle\ORM\Parser\Typecast;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Registry;
use Ramsey\Uuid\Uuid;

final class RegistryModifierTest extends DatabaseTestCase
{
    private const ROLE_TEST = 'test';

    protected RegistryModifier $modifier;
    protected Registry $registry;

    #[\Testo\Test]
    public function testAddDatetimeField(): void
    {
        $this->modifier->addDatetimeColumn('created_at', 'createdAt');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        \Testo\Assert::true($fields->has('createdAt'));
        \Testo\Assert::same($fields->get('createdAt')->getType(), 'datetime');
        \Testo\Assert::same($fields->get('createdAt')->getColumn(), 'created_at');
    }

    #[\Testo\Test]
    public function testAddStringField(): void
    {
        $this->modifier->addStringColumn('version_str', 'version');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        \Testo\Assert::true($fields->has('version'));
        \Testo\Assert::same($fields->get('version')->getType(), ColumnInterface::STRING);
        \Testo\Assert::same($fields->get('version')->getColumn(), 'version_str');
    }

    #[\Testo\Test]
    public function testAddIntegerField(): void
    {
        $this->modifier->addIntegerColumn('version_int', 'version');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        \Testo\Assert::true($fields->has('version'));
        \Testo\Assert::same($fields->get('version')->getType(), 'integer');
        \Testo\Assert::same($fields->get('version')->getColumn(), 'version_int');
    }

    #[\Testo\Test]
    public function testAddBigIntegerField(): void
    {
        $this->modifier->addBigIntegerColumn('snowflake_column', 'snowflake');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        \Testo\Assert::true($fields->has('snowflake'));
        \Testo\Assert::same($fields->get('snowflake')->getType(), 'bigInteger');
        \Testo\Assert::same($fields->get('snowflake')->getColumn(), 'snowflake_column');
    }

    #[\Testo\Test]
    public function testAddBigIntegerFieldThrowsException(): void
    {
        $this->modifier->addIntegerColumn('snowflake_column', 'snowflake');

        \Testo\Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Field snowflake must be of type big integer.');

        $this->modifier->addBigIntegerColumn('snowflake_column', 'snowflake');
    }

    #[\Testo\Test]
    public function testAddSnowflakeField(): void
    {
        $this->modifier->addSnowflakeColumn('snowflake_column', 'snowflake');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        \Testo\Assert::true($fields->has('snowflake'));
        \Testo\Assert::same($fields->get('snowflake')->getType(), 'snowflake');
        \Testo\Assert::same($fields->get('snowflake')->getColumn(), 'snowflake_column');
    }

    #[\Testo\Test]
    public function testAddSnowflakeFieldThrowsException(): void
    {
        $this->modifier->addStringColumn('snowflake_column', 'snowflake');

        \Testo\Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Field snowflake must be of type snowflake.');

        $this->modifier->addSnowflakeColumn('snowflake_column', 'snowflake');
    }

    #[\Testo\Test]
    public function testAddUlidField(): void
    {
        $this->modifier->addUlidColumn('ulid_column', 'ulid');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        \Testo\Assert::true($fields->has('ulid'));
        \Testo\Assert::same($fields->get('ulid')->getType(), 'ulid');
        \Testo\Assert::same($fields->get('ulid')->getColumn(), 'ulid_column');
    }

    #[\Testo\Test]
    public function testAddUlidFieldThrowsException(): void
    {
        $this->modifier->addIntegerColumn('ulid_column', 'ulid');

        \Testo\Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Field ulid must be of type ulid.');

        $this->modifier->addUlidColumn('ulid_column', 'ulid');
    }

    #[\Testo\Test]
    public function testAddUuidField(): void
    {
        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        \Testo\Assert::true($fields->has('uuid'));
        \Testo\Assert::same($fields->get('uuid')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('uuid')->getColumn(), 'uuid_column');
    }

    #[\Testo\Test]
    public function testAddUuidFieldThrowsException(): void
    {
        $this->modifier->addIntegerColumn('uuid_column', 'uuid');

        \Testo\Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Field uuid must be of type uuid.');

        $this->modifier->addUuidColumn('uuid_column', 'uuid');
    }

    #[\Testo\Test]
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
        \Testo\Assert::same($field1->getTypecast(), [Uuid::class, 'fromString']);
        \Testo\Assert::same($field2->getTypecast(), 'int');
        \Testo\Assert::same($field3->getTypecast(), 'int');

        // entity has default typecast
        \Testo\Assert::same($this->registry->getEntity(self::ROLE_TEST)->getTypecast(), [Typecast::class, CustomTypecast::class]);
    }

    #[\Testo\Test]
    public function testAddTypecastEntityWithTypecast(): void
    {
        $this->registry->getEntity(self::ROLE_TEST)->setTypecast(CustomTypecast::class);

        $this->modifier->addUuidColumn('uuid_column', 'uuid');
        $field = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid');

        $this->modifier->setTypecast($field, [Uuid::class, 'fromString']);

        // field has custom UUID typecast
        \Testo\Assert::same($field->getTypecast(), [Uuid::class, 'fromString']);

        // entity has default typecast and custom typecast
        \Testo\Assert::same($this->registry->getEntity(self::ROLE_TEST)->getTypecast(), [CustomTypecast::class, Typecast::class]);
    }

    #[\Testo\Test]
    public function testAddTypecastShouldBeSkipped(): void
    {
        $this->registry->getEntity(self::ROLE_TEST);

        $this->modifier->addUuidColumn('uuid_column', 'uuid');
        $this->registry->getDefaults()->offsetSet(SchemaInterface::TYPECAST_HANDLER, Typecast::class);

        \Testo\Assert::null($this->registry->getEntity(self::ROLE_TEST)->getTypecast());
    }

    #[\Testo\Test]
    public function testAddTypecastShouldBeDuplicated(): void
    {
        $this->registry->getEntity(self::ROLE_TEST)->setTypecast(CustomTypecast::class);

        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        \Testo\Assert::same($this->registry->getEntity(self::ROLE_TEST)->getTypecast(), CustomTypecast::class);
    }

    #[\Testo\Test]
    public function testCustomTypecastNotOverridden(): void
    {
        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $field = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid');
        $field->setTypecast(['foo', 'bar']);

        $this->modifier->setTypecast($field, [Uuid::class, 'fromString']);

        \Testo\Assert::same($field->getTypecast(), ['foo', 'bar']);
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $this->registry = new Registry($this->dbal);

        $entity = (new Entity())->setRole(self::ROLE_TEST);
        $this->registry->register($entity);
        $this->registry->linkTable($entity, 'default', 'tests');

        $this->modifier = new RegistryModifier($this->registry, self::ROLE_TEST);
    }
}
