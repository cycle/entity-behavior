<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Common\Schema;

use Cycle\Database\ColumnInterface;
use Cycle\Database\Schema\AbstractColumn;
use Cycle\ORM\Entity\Macros\Exception\MacrosCompilationException;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Registry;

class RegistryModifier
{
    public function __construct(
        private Registry $registry,
        private string $role
    ) {
    }

    public function addDatetimeColumn(string $columnName, string $fieldName): AbstractColumn
    {
        $entity = $this->registry->getEntity($this->role);
        $table = $this->registry->getTableSchema($entity);
        $fields = $entity->getFields();

        if ($fields->has($fieldName)) {
            if (!$this->isDatetimeColumn($fields->get($fieldName))) {
                throw new MacrosCompilationException(sprintf('Field %s must be of type datetime.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);

            return $table->column($columnName);
        }

        $field = new Field();
        $field->setColumn($columnName)->setType('datetime')->setTypecast('datetime');

        $table->column($field->getColumn())->type($field->getType());

        $fields->set($fieldName, $field);

        return $table->column($field->getColumn());
    }

    public function addIntegerColumn(string $columnName, string $fieldName): AbstractColumn
    {
        $entity = $this->registry->getEntity($this->role);
        $table = $this->registry->getTableSchema($entity);
        $fields = $entity->getFields();

        if ($fields->has($fieldName)) {
            if (!$this->isIntegerColumn($fields->get($fieldName))) {
                throw new MacrosCompilationException(sprintf('Field %s must be of type integer.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);

            return $table->column($columnName);
        }

        $field = new Field();
        $field->setColumn($columnName)->setType(ColumnInterface::INT)->setTypecast('int');

        $table->column($field->getColumn())->type($field->getType());

        $fields->set($fieldName, $field);

        return $table->column($field->getColumn());
    }

    public function addStringColumn(string $columnName, string $fieldName): AbstractColumn
    {
        $entity = $this->registry->getEntity($this->role);
        $table = $this->registry->getTableSchema($entity);
        $fields = $entity->getFields();

        if ($fields->has($fieldName)) {
            if (!$this->isStringColumn($fields->get($fieldName))) {
                throw new MacrosCompilationException(sprintf('Field %s must be of type string.', $fieldName));
            }
            $this->validateColumnName($fieldName, $columnName);

            return $table->column($columnName);
        }

        $field = new Field();
        $field->setColumn($columnName)->setType(ColumnInterface::STRING);

        $table->column($field->getColumn())->type($field->getType());

        $fields->set($fieldName, $field);

        return $table->column($field->getColumn());
    }

    /** @throws MacrosCompilationException */
    private function validateColumnName(string $fieldName, string $columnName): void
    {
        $field = $this->registry->getEntity($this->role)->getFields()->get($fieldName);

        if ($field->getColumn() !== $columnName) {
            throw new MacrosCompilationException(
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

    private function isDatetimeColumn(Field $field): bool
    {
        return $field->getType() === 'datetime';
    }

    private function isIntegerColumn(Field $field): bool
    {
        return $field->getType() === ColumnInterface::INT;
    }

    private function isStringColumn(Field $field): bool
    {
        return $field->getType() === ColumnInterface::STRING;
    }
}
