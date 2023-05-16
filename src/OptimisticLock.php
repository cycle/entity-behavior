<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior;

use Cycle\ORM\Entity\Behavior\Schema\BaseModifier;
use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Cycle\ORM\Entity\Behavior\Exception\BehaviorCompilationException;
use Cycle\ORM\Entity\Behavior\Listener\OptimisticLock as Listener;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * Implements the Optimistic Lock strategy.
 * Used to prevent concurrent editing of a record in the database. When an entity is locked, the transaction is aborted.
 * Please keep in mind, the behavior wraps the command in a special WrappedCommand wrapper.
 * The behavior has three parameters:
 *    - field - is a property with the version in the entity
 *    - column - is a column in the database.
 *    - rule - the strategy for storing the version of the entity
 * Rule can be one of several rules (class constants can be used):
 *    - RULE_MICROTIME - string with microtime value
 *    - RULE_RAND_STR - random string
 *    - RULE_INCREMENT - automatically incrementing integer version
 *    - RULE_DATETIME - datetime of the entity version
 *    - RULE_MANUAL - manually configured rule
 * The MANUAL rule provides for the completely manual configuration of an entity property and entity versioning.
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class OptimisticLock extends BaseModifier
{
    public const RULE_MICROTIME = Listener::RULE_MICROTIME;
    public const RULE_RAND_STR = Listener::RULE_RAND_STR;
    public const RULE_INCREMENT = Listener::RULE_INCREMENT;
    public const RULE_DATETIME = Listener::RULE_DATETIME;
    public const RULE_MANUAL = Listener::RULE_MANUAL;

    private const DEFAULT_INT_VERSION = 1;
    private const STRING_COLUMN_LENGTH = 32;

    private ?string $column = null;

    /**
     * @param non-empty-string $field Version property name
     * @param non-empty-string|null $column Version column name
     * @param non-empty-string|null $rule
     */
    public function __construct(
        private string $field = 'version',
        ?string $column = null,
        /** @Enum({"microtime", "random-string", "increment", "datetime"}) */
        #[ExpectedValues(valuesFromClass: Listener::class)]
        private ?string $rule = null
    ) {
        $this->column = $column;
    }

    protected function getListenerClass(): string
    {
        return Listener::class;
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
        $this->column = $modifier->findColumnName($this->field, $this->column);

        if ($this->column !== null) {
            $this->addField($registry);
        }
    }

    public function render(Registry $registry): void
    {
        $this->column = (new RegistryModifier($registry, $this->role))
                ->findColumnName($this->field, $this->column)
            ?? $this->field;

        $this->addField($registry);
    }

    /**
     * Compute rule based on column type
     *
     * @return non-empty-string
     *
     * @throws BehaviorCompilationException
     */
    private function computeRule(Field $field): string
    {
        $type = $field->getType();

        return match (true) {
            RegistryModifier::isIntegerType($type) => self::RULE_INCREMENT,
            RegistryModifier::isStringType($type) => self::RULE_MICROTIME,
            RegistryModifier::isDatetimeType($type) => self::RULE_DATETIME,
            default => throw new BehaviorCompilationException('Failed to compute rule based on column type.')
        };
    }

    private function addField(Registry $registry): void
    {
        $fields = $registry->getEntity($this->role)->getFields();

        assert($this->column !== null);

        $this->rule ??= $fields->has($this->field)
            ? $this->computeRule($fields->get($this->field))
            // rule not set, field not fount
            : Listener::DEFAULT_RULE;

        $modifier = new RegistryModifier($registry, $this->role);

        switch ($this->rule) {
            case self::RULE_INCREMENT:
                $modifier->addIntegerColumn($this->column, $this->field)
                    ->nullable(false)
                    ->defaultValue(self::DEFAULT_INT_VERSION);
                break;
            case self::RULE_RAND_STR:
            case self::RULE_MICROTIME:
                $modifier->addStringColumn($this->column, $this->field)
                    ->nullable(false)
                    ->string(self::STRING_COLUMN_LENGTH);
                break;
            case self::RULE_DATETIME:
                $modifier->addDatetimeColumn($this->column, $this->field);
                break;
            default:
                throw new BehaviorCompilationException(
                    sprintf(
                        'Wrong rule `%s` for the %s behavior in the `%s.%s` field.',
                        $this->rule,
                        self::class,
                        $this->role,
                        $this->field
                    )
                );
        }
    }
}
