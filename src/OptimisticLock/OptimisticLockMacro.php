<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\OptimisticLock;

use Cycle\Database\ColumnInterface;
use Cycle\Database\Schema\AbstractColumn;
use Cycle\ORM\Entity\Macros\Exception\MacrosCompilationException;
use Cycle\ORM\Entity\Macros\Preset\BaseModifier;
use Cycle\ORM\Entity\Macros\Schema\RegistryModifier;
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
 *     @Attribute("rule", type="string"),
 *     @Attribute("column", type="string"),
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class OptimisticLockMacro extends BaseModifier
{
    private string $column;

    /**
     * @param string $field Version field
     * @param string $rule
     * @param null|string $column
     */
    public function __construct(
        private string $field = 'version',
        #[ExpectedValues(valuesFromClass: OptimisticLockListener::class)]
        private ?string $rule = null,
        ?string $column = null
    ) {
        $this->column = $column ?? $field;
    }

    protected function getListenerClass(): string
    {
        return OptimisticLockListener::class;
    }

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
            throw new MacrosCompilationException(
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
                    ->string(32);
                break;
            case OptimisticLockListener::RULE_MICROTIME:
                $modifier->addStringColumn($this->column, $this->field)
                    ->nullable(false)
                    ->string(32);
                break;
            case OptimisticLockListener::RULE_DATETIME:
                $modifier->addDatetimeColumn($this->column, $this->field);
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
    }

    /**
     * Compute rule based on column type
     *
     * @throws MacrosCompilationException
     */
    private function computeRule(AbstractColumn $column): string
    {
        return match ($column->getType()) {
            ColumnInterface::INT => OptimisticLockListener::RULE_INCREMENT,
            ColumnInterface::STRING => OptimisticLockListener::RULE_MICROTIME,
            'datetime' => OptimisticLockListener::RULE_DATETIME,
            default => throw new MacrosCompilationException('Failed to compute rule based on column type.')
        };
    }
}
