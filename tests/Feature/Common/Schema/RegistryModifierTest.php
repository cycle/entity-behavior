<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\Schema;

use Cycle\Database\ColumnInterface;
use Cycle\ORM\Entity\Behavior\Exception\BehaviorCompilationException;
use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\CustomTypecast;
use Cycle\ORM\Parser\Typecast;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Registry;
use Cycle\Testing\Database\DatabaseTestCase;
use Ramsey\Uuid\Uuid;
use Testo\Assert;
use Testo\Data\DataSet;
use Testo\Expect;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class RegistryModifierTest extends DatabaseTestCase
{
    private const ROLE_TEST = 'test';

    protected RegistryModifier $modifier;
    protected Registry $registry;

    #[Test]
    public function testAddDatetimeField(): void
    {
        $this->modifier->addDatetimeColumn('created_at', 'createdAt');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        Assert::true($fields->has('createdAt'));
        Assert::same($fields->get('createdAt')->getType(), 'datetime');
        Assert::same($fields->get('createdAt')->getColumn(), 'created_at');
    }

    #[Test]
    public function testAddStringField(): void
    {
        $this->modifier->addStringColumn('version_str', 'version');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        Assert::true($fields->has('version'));
        Assert::same($fields->get('version')->getType(), ColumnInterface::STRING);
        Assert::same($fields->get('version')->getColumn(), 'version_str');
    }

    #[Test]
    public function testAddIntegerField(): void
    {
        $this->modifier->addIntegerColumn('version_int', 'version');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        Assert::true($fields->has('version'));
        Assert::same($fields->get('version')->getType(), 'integer');
        Assert::same($fields->get('version')->getColumn(), 'version_int');
    }

    #[Test]
    public function testAddBigIntegerField(): void
    {
        $this->modifier->addBigIntegerColumn('snowflake_column', 'snowflake');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        Assert::true($fields->has('snowflake'));
        Assert::same($fields->get('snowflake')->getType(), 'bigInteger');
        Assert::same($fields->get('snowflake')->getColumn(), 'snowflake_column');
    }

    #[Test]
    public function testAddBigIntegerFieldThrowsException(): void
    {
        $this->modifier->addIntegerColumn('snowflake_column', 'snowflake');

        Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Field snowflake must be of type big integer.');

        $this->modifier->addBigIntegerColumn('snowflake_column', 'snowflake');
    }

    #[Test]
    public function testAddSnowflakeField(): void
    {
        $this->modifier->addSnowflakeColumn('snowflake_column', 'snowflake');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        Assert::true($fields->has('snowflake'));
        Assert::same($fields->get('snowflake')->getType(), 'snowflake');
        Assert::same($fields->get('snowflake')->getColumn(), 'snowflake_column');
    }

    #[Test]
    public function testAddSnowflakeFieldThrowsException(): void
    {
        $this->modifier->addStringColumn('snowflake_column', 'snowflake');

        Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Field snowflake must be of type snowflake.');

        $this->modifier->addSnowflakeColumn('snowflake_column', 'snowflake');
    }

    #[Test]
    public function testAddUlidField(): void
    {
        $this->modifier->addUlidColumn('ulid_column', 'ulid');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        Assert::true($fields->has('ulid'));
        Assert::same($fields->get('ulid')->getType(), 'ulid');
        Assert::same($fields->get('ulid')->getColumn(), 'ulid_column');
    }

    #[Test]
    public function testAddUlidFieldThrowsException(): void
    {
        $this->modifier->addIntegerColumn('ulid_column', 'ulid');

        Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Field ulid must be of type ulid.');

        $this->modifier->addUlidColumn('ulid_column', 'ulid');
    }

    #[Test]
    public function testAddUuidField(): void
    {
        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        Assert::true($fields->has('uuid'));
        Assert::same($fields->get('uuid')->getType(), 'uuid');
        Assert::same($fields->get('uuid')->getColumn(), 'uuid_column');
    }

    #[Test]
    public function testAddUuidFieldThrowsException(): void
    {
        $this->modifier->addIntegerColumn('uuid_column', 'uuid');

        Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Field uuid must be of type uuid.');

        $this->modifier->addUuidColumn('uuid_column', 'uuid');
    }

    #[Test]
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
        Assert::same($field1->getTypecast(), [Uuid::class, 'fromString']);
        Assert::same($field2->getTypecast(), 'int');
        Assert::same($field3->getTypecast(), 'int');

        // entity has default typecast
        Assert::same($this->registry->getEntity(self::ROLE_TEST)->getTypecast(), [Typecast::class, CustomTypecast::class]);
    }

    #[Test]
    public function testAddTypecastEntityWithTypecast(): void
    {
        $this->registry->getEntity(self::ROLE_TEST)->setTypecast(CustomTypecast::class);

        $this->modifier->addUuidColumn('uuid_column', 'uuid');
        $field = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid');

        $this->modifier->setTypecast($field, [Uuid::class, 'fromString']);

        // field has custom UUID typecast
        Assert::same($field->getTypecast(), [Uuid::class, 'fromString']);

        // entity has default typecast and custom typecast
        Assert::same($this->registry->getEntity(self::ROLE_TEST)->getTypecast(), [CustomTypecast::class, Typecast::class]);
    }

    #[Test]
    public function testAddTypecastShouldBeSkipped(): void
    {
        $this->registry->getEntity(self::ROLE_TEST);

        $this->modifier->addUuidColumn('uuid_column', 'uuid');
        $this->registry->getDefaults()->offsetSet(SchemaInterface::TYPECAST_HANDLER, Typecast::class);

        Assert::null($this->registry->getEntity(self::ROLE_TEST)->getTypecast());
    }

    #[Test]
    public function testAddTypecastShouldBeDuplicated(): void
    {
        $this->registry->getEntity(self::ROLE_TEST)->setTypecast(CustomTypecast::class);

        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        Assert::same($this->registry->getEntity(self::ROLE_TEST)->getTypecast(), CustomTypecast::class);
    }

    #[Test]
    public function testAddIntegerFieldWithOtherColumnThrowsException(): void
    {
        $this->modifier->addIntegerColumn('counter_column', 'counter');

        Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Ambiguous column name definition.');

        $this->modifier->addIntegerColumn('other_column', 'counter');
    }

    #[Test]
    public function testAddStringFieldWithOtherColumnThrowsException(): void
    {
        $this->modifier->addStringColumn('title_column', 'title');

        Expect::exception(BehaviorCompilationException::class)->withMessageContaining('Ambiguous column name definition.');

        $this->modifier->addStringColumn('other_column', 'title');
    }

    #[Test]
    public function testAddExistingStringFieldSetsGenerated(): void
    {
        $this->modifier->addStringColumn('title_column', 'title');

        $this->modifier->addStringColumn('title_column', 'title', GeneratedField::BEFORE_INSERT);

        Assert::same(
            $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('title')->getGenerated(),
            GeneratedField::BEFORE_INSERT,
        );
    }

    #[Test]
    #[DataSet(['addIntegerColumn'], 'integer')]
    #[DataSet(['addBigIntegerColumn'], 'big integer')]
    #[DataSet(['addDatetimeColumn'], 'datetime')]
    #[DataSet(['addStringColumn'], 'string')]
    #[DataSet(['addSnowflakeColumn'], 'snowflake')]
    #[DataSet(['addUlidColumn'], 'ulid')]
    #[DataSet(['addUuidColumn'], 'uuid')]
    public function testExistingFieldOfSameTypeIsReused(string $method): void
    {
        $this->modifier->{$method}('value_column', 'value');

        $column = $this->modifier->{$method}('value_column', 'value', GeneratedField::BEFORE_INSERT);

        $fields = $this->registry->getEntity(self::ROLE_TEST)->getFields();
        Assert::same($fields->count(), 1);
        Assert::same($fields->get('value')->getGenerated(), GeneratedField::BEFORE_INSERT);
        Assert::same($column->getName(), 'value_column');
    }

    #[Test]
    #[DataSet(['addIntegerColumn'], 'integer')]
    #[DataSet(['addBigIntegerColumn'], 'big integer')]
    #[DataSet(['addDatetimeColumn'], 'datetime')]
    #[DataSet(['addStringColumn'], 'string')]
    #[DataSet(['addSnowflakeColumn'], 'snowflake')]
    #[DataSet(['addUlidColumn'], 'ulid')]
    #[DataSet(['addUuidColumn'], 'uuid')]
    public function testExistingFieldOfSameTypeOnOtherColumnIsRejected(string $method): void
    {
        $this->modifier->{$method}('value_column', 'value');

        Expect::exception(BehaviorCompilationException::class)->withMessage(
            'Ambiguous column name definition. '
            . 'The `value` field already linked with the `value_column` column but the behavior expects `other_column`.',
        );

        $this->modifier->{$method}('other_column', 'value');
    }

    #[Test]
    #[DataSet(['addIntegerColumn', 'Field value must be of type integer.'], 'integer')]
    #[DataSet(['addStringColumn', 'Field value must be of type string.'], 'string')]
    #[DataSet(['addDatetimeColumn', 'Field value must be of type datetime.'], 'datetime')]
    public function testExistingFieldOfOtherTypeIsRejected(string $method, string $message): void
    {
        $this->modifier->addUuidColumn('value_column', 'value');

        Expect::exception(BehaviorCompilationException::class)->withMessage($message);

        $this->modifier->{$method}('value_column', 'value');
    }

    #[Test]
    public function testDefaultTypecastHandlerIsNotAddedToEntity(): void
    {
        $this->registry->getDefaults()->offsetSet(SchemaInterface::TYPECAST_HANDLER, Typecast::class);
        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $this->modifier->setTypecast(
            $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid'),
            [Uuid::class, 'fromString'],
        );

        Assert::null($this->registry->getEntity(self::ROLE_TEST)->getTypecast());
    }

    #[Test]
    public function testDefaultTypecastHandlerListIsNotAddedToEntity(): void
    {
        $this->registry->getDefaults()->offsetSet(SchemaInterface::TYPECAST_HANDLER, [Typecast::class]);
        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $this->modifier->setTypecast(
            $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid'),
            [Uuid::class, 'fromString'],
        );

        Assert::null($this->registry->getEntity(self::ROLE_TEST)->getTypecast());
    }

    #[Test]
    public function testCustomTypecastNotOverridden(): void
    {
        $this->modifier->addUuidColumn('uuid_column', 'uuid');

        $field = $this->registry->getEntity(self::ROLE_TEST)->getFields()->get('uuid');
        $field->setTypecast(['foo', 'bar']);

        $this->modifier->setTypecast($field, [Uuid::class, 'fromString']);

        Assert::same($field->getTypecast(), ['foo', 'bar']);
    }

    #[Test]
    #[DataSet(['datetime', 'datetime', 'datetime'], 'datetime field over a datetime column')]
    #[DataSet(['datetime', 'string', 'datetime'], 'datetime field over a string column')]
    #[DataSet(['int', 'integer', 'int'], 'integer column')]
    #[DataSet(['uuid', 'uuid', 'uuid'], 'uuid field over a uuid column')]
    #[DataSet(['uuid', 'string', 'uuid'], 'uuid field over a string column')]
    #[DataSet(['string', 'string', 'string'], 'any other type')]
    public function testDeprecatedIsTypeMatches(string $type, string $columnType, string $fieldType): void
    {
        Assert::true($this->checkType($type, $columnType, $fieldType));
    }

    #[Test]
    #[DataSet(['datetime', 'string', 'string'], 'datetime')]
    #[DataSet(['int', 'string', 'int'], 'integer')]
    #[DataSet(['uuid', 'string', 'string'], 'uuid')]
    #[DataSet(['string', 'integer', 'string'], 'any other type')]
    public function testDeprecatedIsTypeMismatches(string $type, string $columnType, string $fieldType): void
    {
        Assert::false($this->checkType($type, $columnType, $fieldType));
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->registry = new Registry($this->dbal);

        $entity = (new Entity())->setRole(self::ROLE_TEST);
        $this->registry->register($entity);
        $this->registry->linkTable($entity, 'default', 'tests');

        $this->modifier = new RegistryModifier($this->registry, self::ROLE_TEST);
    }

    /**
     * Calls the deprecated protected `isType()` the way a subclass would, on a field linked to a column of its name.
     */
    private function checkType(string $type, string $columnType, string $fieldType): bool
    {
        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $this->registry->getTableSchema($entity)->column('value')->type($columnType);
        $entity->getFields()->set('value', (new Field())->setColumn('value')->setType($fieldType));

        $modifier = new class($this->registry, self::ROLE_TEST) extends RegistryModifier {
            public function check(string $type, string $fieldName, string $columnName): bool
            {
                return $this->isType($type, $fieldName, $columnName);
            }
        };

        return $modifier->check($type, 'value', 'value');
    }
}
