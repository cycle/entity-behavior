<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\OptimisticLock;

use Cycle\Database\ColumnInterface;
use Cycle\Database\Schema\AbstractColumn;
use Cycle\ORM\Entity\Macros\Exception\MacroCompilationException;
use Cycle\ORM\Entity\Macros\Preset\BaseModifier;
use Cycle\ORM\Entity\Macros\Schema\RegistryModifier;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class OptimisticLockMacro extends BaseModifier
{
    private string $column;

    /**
     * @param non-empty-string $field Version property name
     * @param non-empty-string|null $column Version column name
     * @param non-empty-string|null $rule
     */
    public function __construct(
        private string $field = 'version',
        /** @enum({"microtime", "random-string", "increment", "datetime"}) */
        #[ExpectedValues(valuesFromClass: OptimisticLockListener::class)]
        ?string $column = null,
        private ?string $rule = null
    ) {
        $this->column = $column ?? $field;
    }

    protected function getListenerClass(): string
    {
        return OptimisticLockListener::class;
    }

    #[ArrayShape(['field' => 'string', 'rule' => 'null|string'])]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'rule' => $this->rule
        ];
    }

    public function compute(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);

        if ($this->rule === null && !$registry->getEntity($this->role)->getFields()->has($this->column)) {
            throw new MacroCompilationException(
                'The OptimisticLockMacro must be configured with a rule parameter or the existence column name.'
            );
        }

        if ($this->rule === null) {
            $this->rule = $this->computeRule(
                $registry->getTableSchema($registry->getEntity($this->role))->column($this->column)
            );
        }

        switch ($this->rule) {
            case OptimisticLockListener::RULE_INCREMENT:
                $modifier->addIntegerColumn($this->column, $this->field)
                    ->nullable(false)
                    ->defaultValue(1);
                break;
            case OptimisticLockListener::RULE_RAND_STR:
                $modifier->addStringColumn($this->column, $this->field)
                    ->nullable(false)
                    ->string(64);
                break;
            case OptimisticLockListener::RULE_MICROTIME:
                $modifier->addStringColumn($this->column, $this->field)
                    ->nullable(false)
                    ->string(64);
                break;
            case OptimisticLockListener::RULE_DATETIME:
                $modifier->addDatetimeColumn($this->column, $this->field);
                break;
            default:
                throw new MacroCompilationException(
                    sprintf(
                        'Wrong rule `%s` for the %s macros in the `%s.%s` field.',
                        $this->rule,
                        self::class,
                        $this->role,
                        $this->field
                    )
                );
        }
    }

    /**
     * Compute rule based on column type
     *
     * @return non-empty-string
     *
     * @throws MacroCompilationException
     */
    private function computeRule(AbstractColumn $column): string
    {
        return match ($column->getType()) {
            ColumnInterface::INT => OptimisticLockListener::RULE_INCREMENT,
            ColumnInterface::STRING => OptimisticLockListener::RULE_MICROTIME,
            'datetime' => OptimisticLockListener::RULE_DATETIME,
            default => throw new MacroCompilationException('Failed to compute rule based on column type.')
        };
    }
}
