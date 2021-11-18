<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\OptimisticLock;

use Cycle\Database\ColumnInterface;
use Cycle\Database\Schema\AbstractColumn;
use Cycle\ORM\Entity\Macros\Exception\MacrosCompilationException;
use Cycle\ORM\Entity\Macros\Preset\BaseModifier;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 * @Attributes({
 *     @Attribute("field", type="string"),
 *     @Attribute("column", type="string"),
 *     @Attribute("column", type="string"),
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class OptimisticLockMacro extends BaseModifier
{
    private const DEFAULT_RULE = OptimisticLockListener::RULE_INCREMENT;
    /**
     * @param string $field Version field
     * @param null|string $rule
     * @param null|string $column
     */
    public function __construct(
        private string $field,
        #[ExpectedValues(valuesFromClass: OptimisticLockListener::class)]
        private ?string $rule = 'string',
        private ?string $column = null
    ) {
        $this->column = $column ?? $field;
    }

    protected function getListenerClass(): string
    {
        return OptimisticLockListener::class;
    }

    protected function getListenerArgs(): array
    {
        if ($this->rule === null) {
            throw new MacrosCompilationException();
        }
        return [
            'field' => $this->field,
            'rule' => $this->rule,
            'column' => $this->column,
        ];
    }

    public function compute(Registry $registry): void
    {
        if ($this->column === null) {
            return;
        }

        $entity = $registry->getEntity($this->role);
        $table = $registry->getTableSchema($entity);
        $fields = $entity->getFields();

        // If field-column pair is registered
        if ($fields->has($this->field)) {
            // Check configured column
            $columnName = $fields->get($this->field)->getColumn();
            if ($columnName !== $this->column) {
                throw new MacrosCompilationException(
                    sprintf(
                        'Ambiguous column name definition. '
                        . 'The `%s` field already linked with the `%s` column but the macros expects `%s`.',
                        $this->field,
                        $columnName,
                        $this->column
                    )
                );
            }
            $column = $table->column($columnName);

            // Get rule from column type
            if ($this->rule === null) {
                $this->rule = $this->computeRule($column);
                return;
            }
            $this->matchColumnWithRule($table->column($this->column), $this->rule);

            return;
        }

        $this->rule ??= self::DEFAULT_RULE;

        // Create field with type based on rule name
        $field = new Field();
        $field->setColumn($this->column);
        $column = $table->column($field->getColumn());
        switch ($this->rule) {
            case OptimisticLockListener::RULE_INCREMENT:
                $field->setType('int')->setTypecast('int');
                $column->integer();
                break;
            case OptimisticLockListener::RULE_RAND_STR:
            case OptimisticLockListener::RULE_MICROTIME:
                $field->setColumn($this->column)->setType('string');
                $column->string(32);
                break;
            case OptimisticLockListener::RULE_DATETIME:
                $field->setColumn($this->column)->setType('datetime')->setTypecast('datetime');
                $column->datetime();
                break;
            default:
                throw new MacrosCompilationException(
                    sprintf(
                        'Wrong rule `%s` for the %s macros in the `%s.%s` field.',
                        $this->rule,
                        self::class,
                        $this->role,
                        $this->field
                    )
                );
        }

        $fields->set($this->field, $field);
    }

    public function render(Registry $registry): void
    {
        if ($this->column !== null) {
            return;
        }
        $entity = $registry->getEntity($this->role);
        $table = $registry->getTableSchema($entity);
        $fields = $entity->getFields();

        if (!$fields->has($this->field)) {
            throw new MacrosCompilationException(
                sprintf(
                    'Entity has no field `%s` related with any column.',
                    $this->field
                )
            );
        }

        // get column name
        $this->column = $fields->get($this->field)->getColumn();

        if ($this->rule === null) {
            $column = $table->column($this->column);
            $this->rule = $this->computeRule($column);
            return;
        }
        $this->matchColumnWithRule($table->column($this->column), $this->rule);
    }

    /**
     * Match column type with rule
     *
     * @throws MacrosCompilationException
     */
    private function matchColumnWithRule(AbstractColumn $column, string $rule): void
    {
        // todo
        $type = $column->getType();
    }

    /**
     * Compute rule based on column type
     *
     * @throws MacrosCompilationException
     */
    private function computeRule(AbstractColumn $column): string
    {
        // todo getType can be wrong for this task
        return match ($column->getType()) {
            ColumnInterface::INT => OptimisticLockListener::RULE_INCREMENT,
            ColumnInterface::STRING => OptimisticLockListener::RULE_MICROTIME,
            'datetime' => OptimisticLockListener::RULE_DATETIME,
            default => throw new MacrosCompilationException('Failed to compute rule based on column type.')
        };
    }
}
