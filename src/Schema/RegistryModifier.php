<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Schema;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Database\Schema\AbstractTable;
use Cycle\ORM\Entity\Behavior\Exception\BehaviorCompilationException;
use Cycle\ORM\Parser\Typecast;
use Cycle\ORM\Parser\TypecastInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Defaults;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Definition\Map\FieldMap;
use Cycle\Schema\Registry;

/**
 * @internal
 */
class RegistryModifier
{
    protected const DEFINITION = '/(?P<type>[a-z]+)(?: *\((?P<options>[^\)]+)\))?/i';
    protected const INTEGER_TYPES = [
        'int',
        'smallint',
        'tinyint',
        'bigint',
        'integer',
        'tinyInteger',
        'smallInteger',
        'bigInteger',
    ];
    protected const BIG_INTEGER_TYPES = [
        'bigint',
        'bigInteger',
    ];
    protected const DATETIME_TYPES = ['datetime', 'datetime2'];
    protected const INT_COLUMN = AbstractColumn::INT;
    protected const STRING_COLUMN = AbstractColumn::STRING;
    protected const BIG_INTEGER_COLUMN = 'bigInteger';
    protected const DATETIME_COLUMN = 'datetime';
    protected const SNOWFLAKE_COLUMN = 'snowflake';
    protected const ULID_COLUMN = 'ulid';
    protected const UUID_COLUMN = 'uuid';

    protected FieldMap $fields;
    protected AbstractTable $table;
    protected Entity $entity;
    protected Defaults $defaults;

    public function __construct(Registry $registry, string $role)
    {
        $this->entity = $registry->getEntity($role);
        $this->fields = $this->entity->getFields();
        $this->table = $registry->getTableSchema($this->entity);
        $this->defaults = $registry->getDefaults();
    }

    public static function isIntegerType(string $type): bool
    {
        \preg_match(self::DEFINITION, $type, $matches);

        return \in_array($matches['type'], self::INTEGER_TYPES, true);
    }

    public static function isBigIntegerType(string $type): bool
    {
        \preg_match(self::DEFINITION, $type, $matches);

        return \in_array($matches['type'], self::BIG_INTEGER_TYPES, true);
    }

    public static function isDatetimeType(string $type): bool
    {
        \preg_match(self::DEFINITION, $type, $matches);

        return \in_array($matches['type'], self::DATETIME_TYPES, true);
    }

    public static function isStringType(string $type): bool
    {
        \preg_match(self::DEFINITION, $type, $matches);

        return $matches['type'] === 'string';
    }

    public static function isSnowflakeType(string $type): bool
    {
        \preg_match(self::DEFINITION, $type, $matches);

        return $matches['type'] === self::SNOWFLAKE_COLUMN;
    }

    public static function isUlidType(string $type): bool
    {
        \preg_match(self::DEFINITION, $type, $matches);

        return $matches['type'] === self::ULID_COLUMN;
    }

    public static function isUuidType(string $type): bool
    {
        \preg_match(self::DEFINITION, $type, $matches);

        return $matches['type'] === self::UUID_COLUMN;
    }

    public function addDatetimeColumn(string $columnName, string $fieldName, int|null $generated = null): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!static::isDatetimeType($this->fields->get($fieldName)->getType())) {
                throw new BehaviorCompilationException(\sprintf('Field %s must be of type datetime.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);
            $this->fields->get($fieldName)->setGenerated($generated);

            return $this->table->column($columnName);
        }

        $field = (new Field())
            ->setColumn($columnName)
            ->setType('datetime')
            ->setTypecast('datetime')
            ->setGenerated($generated);
        $this->fields->set($fieldName, $field);

        return $this->table->column($columnName)->type(self::DATETIME_COLUMN);
    }

    public function addIntegerColumn(string $columnName, string $fieldName, int|null $generated = null): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!static::isIntegerType($this->fields->get($fieldName)->getType())) {
                throw new BehaviorCompilationException(\sprintf('Field %s must be of type integer.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);
            $this->fields->get($fieldName)->setGenerated($generated);

            return $this->table->column($columnName);
        }

        $field = (new Field())
            ->setColumn($columnName)
            ->setType('integer')
            ->setTypecast('int')
            ->setGenerated($generated);
        $this->fields->set($fieldName, $field);

        return $this->table->column($columnName)->type(self::INT_COLUMN);
    }

    public function addBigIntegerColumn(
        string $columnName,
        string $fieldName,
        int|null $generated = null,
    ): AbstractColumn {
        if ($this->fields->has($fieldName)) {
            if (! static::isBigIntegerType($this->fields->get($fieldName)->getType())) {
                throw new BehaviorCompilationException(\sprintf('Field %s must be of type big integer.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);
            $this->fields->get($fieldName)->setGenerated($generated);

            return $this->table->column($columnName);
        }

        $field = (new Field())
            ->setColumn($columnName)
            ->setType(self::BIG_INTEGER_COLUMN)
            ->setTypecast('int')
            ->setGenerated($generated);

        $this->fields->set($fieldName, $field);

        return $this->table->column($columnName)->type(self::BIG_INTEGER_COLUMN);
    }

    public function addStringColumn(string $columnName, string $fieldName, int|null $generated = null): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!static::isStringType($this->fields->get($fieldName)->getType())) {
                throw new BehaviorCompilationException(\sprintf('Field %s must be of type string.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);
            $this->fields->get($fieldName)->setGenerated($generated);

            return $this->table->column($columnName);
        }

        $field = (new Field())->setColumn($columnName)->setType('string')->setGenerated($generated);
        $this->fields->set($fieldName, $field);

        return $this->table->column($columnName)->type(self::STRING_COLUMN);
    }

    /**
     * @param non-empty-string $columnName
     * @throws BehaviorCompilationException
     */
    public function addSnowflakeColumn(string $columnName, string $fieldName, int|null $generated = null): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!static::isSnowflakeType($this->fields->get($fieldName)->getType())) {
                throw new BehaviorCompilationException(
                    \sprintf('Field %s must be of type %s.', $fieldName, self::SNOWFLAKE_COLUMN),
                );
            }
            $this->validateColumnName($fieldName, $columnName);
            $this->fields->get($fieldName)->setGenerated($generated);

            return $this->table->column($columnName);
        }

        $field = (new Field())->setColumn($columnName)->setType(self::SNOWFLAKE_COLUMN)->setGenerated($generated);
        $this->fields->set($fieldName, $field);

        return $this->table->column($columnName)->type(self::SNOWFLAKE_COLUMN);
    }

    /**
     * @param non-empty-string $columnName
     * @throws BehaviorCompilationException
     */
    public function addUlidColumn(string $columnName, string $fieldName, int|null $generated = null): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!static::isUlidType($this->fields->get($fieldName)->getType())) {
                throw new BehaviorCompilationException(
                    \sprintf('Field %s must be of type %s.', $fieldName, self::ULID_COLUMN),
                );
            }
            $this->validateColumnName($fieldName, $columnName);
            $this->fields->get($fieldName)->setGenerated($generated);

            return $this->table->column($columnName);
        }

        $field = (new Field())->setColumn($columnName)->setType(self::ULID_COLUMN)->setGenerated($generated);
        $this->fields->set($fieldName, $field);

        return $this->table->column($columnName)->type(self::ULID_COLUMN);
    }

    /**
     * @param non-empty-string $columnName
     * @throws BehaviorCompilationException
     */
    public function addUuidColumn(string $columnName, string $fieldName, int|null $generated = null): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!static::isUuidType($this->fields->get($fieldName)->getType())) {
                throw new BehaviorCompilationException(
                    \sprintf('Field %s must be of type %s.', $fieldName, self::UUID_COLUMN),
                );
            }
            $this->validateColumnName($fieldName, $columnName);
            $this->fields->get($fieldName)->setGenerated($generated);

            return $this->table->column($columnName);
        }

        $field = (new Field())->setColumn($columnName)->setType(self::UUID_COLUMN)->setGenerated($generated);
        $this->fields->set($fieldName, $field);

        return $this->table->column($columnName)->type(self::UUID_COLUMN);
    }

    public function findColumnName(string $fieldName, ?string $columnName): ?string
    {
        if ($columnName !== null) {
            return $columnName;
        }

        return $this->fields->has($fieldName) ? $this->fields->get($fieldName)->getColumn() : null;
    }

    /**
     * @param class-string<TypecastInterface> $handler
     */
    public function setTypecast(Field $field, array|string|null $rule, string $handler = Typecast::class): Field
    {
        if ($field->getTypecast() === null) {
            $field->setTypecast($rule);
        }

        $defaultHandlers = $this->defaults[SchemaInterface::TYPECAST_HANDLER] ?? [];
        if (!\is_array($defaultHandlers)) {
            $defaultHandlers = [$defaultHandlers];
        }

        $handlers = $this->entity->getTypecast() ?? [];
        if (!\is_array($handlers)) {
            $handlers = [$handlers];
        }

        if (!\in_array($handler, $handlers, true) && !\in_array($handler, $defaultHandlers, true)) {
            $this->entity->setTypecast(\array_merge($handlers, [$handler]));
        }

        return $field;
    }

    /**
     * @throws BehaviorCompilationException
     */
    protected function validateColumnName(string $fieldName, string $columnName): void
    {
        $field = $this->fields->get($fieldName);

        if ($field->getColumn() !== $columnName) {
            throw new BehaviorCompilationException(
                \sprintf(
                    'Ambiguous column name definition. '
                    . 'The `%s` field already linked with the `%s` column but the behavior expects `%s`.',
                    $fieldName,
                    $field->getColumn(),
                    $columnName,
                ),
            );
        }
    }

    /**
     * @deprecated since v1.2
     *
     * @param non-empty-string $type
     * @param non-empty-string $fieldName
     * @param non-empty-string $columnName
     */
    protected function isType(string $type, string $fieldName, string $columnName): bool
    {
        if ($type === self::DATETIME_COLUMN) {
            return
                $this->table->column($columnName)->getInternalType() === self::DATETIME_COLUMN ||
                $this->fields->get($fieldName)->getType() === self::DATETIME_COLUMN;
        }

        if ($type === self::INT_COLUMN) {
            return $this->table->column($columnName)->getType() === self::INT_COLUMN;
        }

        if ($type === self::UUID_COLUMN) {
            return
                $this->table->column($columnName)->getInternalType() === self::UUID_COLUMN ||
                $this->fields->get($fieldName)->getType() === self::UUID_COLUMN;
        }

        return $this->table->column($columnName)->getType() === $type;
    }
}
