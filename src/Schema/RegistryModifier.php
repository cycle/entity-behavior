<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Schema;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Database\Schema\AbstractTable;
use Cycle\ORM\Entity\Behavior\Exception\BehaviorCompilationException;
use Cycle\ORM\Parser\Typecast;
use Cycle\ORM\Parser\TypecastInterface;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Definition\Map\FieldMap;
use Cycle\Schema\Registry;

/**
 * @internal
 */
class RegistryModifier
{
    protected const INT_COLUMN = AbstractColumn::INT;
    protected const STRING_COLUMN = AbstractColumn::STRING;
    protected const DATETIME_COLUMN = 'datetime';
    protected const UUID_COLUMN = 'uuid';

    protected FieldMap $fields;
    protected AbstractTable $table;
    protected Entity $entity;

    public function __construct(Registry $registry, string $role)
    {
        $this->entity = $registry->getEntity($role);
        $this->fields = $this->entity->getFields();
        $this->table = $registry->getTableSchema($this->entity);
    }

    public function addDatetimeColumn(string $columnName, string $fieldName): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!$this->isType(self::DATETIME_COLUMN, $fieldName, $columnName)) {
                throw new BehaviorCompilationException(sprintf('Field %s must be of type datetime.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);

            return $this->table->column($columnName);
        }

        $this->fields->set(
            $fieldName,
            (new Field())->setColumn($columnName)->setType('datetime')->setTypecast('datetime')
        );

        return $this->table->column($columnName)->type(self::DATETIME_COLUMN);
    }

    public function addIntegerColumn(string $columnName, string $fieldName): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!$this->isType(self::INT_COLUMN, $fieldName, $columnName)) {
                throw new BehaviorCompilationException(sprintf('Field %s must be of type integer.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);

            return $this->table->column($columnName);
        }

        $this->fields->set($fieldName, (new Field())->setColumn($columnName)->setType('integer')->setTypecast('int'));

        return $this->table->column($columnName)->type(self::INT_COLUMN);
    }

    public function addStringColumn(string $columnName, string $fieldName): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!$this->isType(self::STRING_COLUMN, $fieldName, $columnName)) {
                throw new BehaviorCompilationException(sprintf('Field %s must be of type string.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);

            return $this->table->column($columnName);
        }

        $this->fields->set($fieldName, (new Field())->setColumn($columnName)->setType('string'));

        return $this->table->column($columnName)->type(self::STRING_COLUMN);
    }

    /**
     * @throws BehaviorCompilationException
     */
    public function addUuidColumn(string $columnName, string $fieldName): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!$this->isType(self::UUID_COLUMN, $fieldName, $columnName)) {
                throw new BehaviorCompilationException(sprintf('Field %s must be of type uuid.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);

            return $this->table->column($columnName);
        }

        $this->fields->set($fieldName, (new Field())->setColumn($columnName)->setType('uuid'));

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

        $handlers = $this->entity->getTypecast();
        if ($handlers === null) {
            $this->entity->setTypecast($handler);
            return $field;
        }

        $handlers = (array) $handlers;
        $handlers[] = $handler;
        $this->entity->setTypecast(array_unique($handlers));

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
                sprintf(
                    'Ambiguous column name definition. '
                    . 'The `%s` field already linked with the `%s` column but the behavior expects `%s`.',
                    $fieldName,
                    $field->getColumn(),
                    $columnName
                )
            );
        }
    }

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
