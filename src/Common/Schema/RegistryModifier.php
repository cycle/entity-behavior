<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Common\Schema;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Database\Schema\AbstractTable;
use Cycle\ORM\Entity\Macros\Exception\MacroCompilationException;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Definition\Map\FieldMap;
use Cycle\Schema\Registry;

class RegistryModifier
{
    private const INT_COLUMN = AbstractColumn::INT;
    private const STRING_COLUMN = AbstractColumn::STRING;
    private const DATETIME_COLUMN = 'datetime';
    private const UUID_COLUMN = 'uuid';

    private FieldMap $fields;
    private AbstractTable $table;

    public function __construct(Registry $registry, string $role)
    {
        $this->fields = $registry->getEntity($role)->getFields();
        $this->table = $registry->getTableSchema($registry->getEntity($role));
    }

    public function addDatetimeColumn(string $columnName, string $fieldName): AbstractColumn
    {
        if ($this->fields->has($fieldName)) {
            if (!$this->isType(self::DATETIME_COLUMN, $fieldName, $columnName)) {
                throw new MacroCompilationException(sprintf('Field %s must be of type datetime.', $fieldName));
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
                throw new MacroCompilationException(sprintf('Field %s must be of type integer.', $fieldName));
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
                throw new MacroCompilationException(sprintf('Field %s must be of type string.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);

            return $this->table->column($columnName);
        }

        $this->fields->set($fieldName, (new Field())->setColumn($columnName)->setType('string'));

        return $this->table->column($columnName)->type(self::STRING_COLUMN);
    }

    public function findColumnName(string $fieldName, ?string $columnName): ?string
    {
        if ($columnName !== null) {
            return $columnName;
        }

        return $this->fields->has($fieldName) ? $this->fields->get($fieldName)->getColumn() : null;
    }

    /** @throws MacroCompilationException */
    private function validateColumnName(string $fieldName, string $columnName): void
    {
        $field = $this->fields->get($fieldName);

        if ($field->getColumn() !== $columnName) {
            throw new MacroCompilationException(
                sprintf(
                    'Ambiguous column name definition. '
                    . 'The `%s` field already linked with the `%s` column but the macros expects `%s`.',
                    $fieldName,
                    $field->getColumn(),
                    $columnName
                )
            );
        }
    }

    private function isType(string $type, string $fieldName, string $columnName): bool
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
