<?php

declare(strict_types=1);

namespace Cycle\SmartMapper\Behavior\OptimisticLock;

use Cycle\Schema\Registry;
use Cycle\SmartMapper\Behavior\BaseModifier;
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
 *      @Attribute("field", type="string"),
 *      @Attribute("column", type="string"),
 *      @Attribute("column", type="string"),
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
#[NamedArgumentConstructor]
final class OptimisticLock extends BaseModifier
{

    public function __construct(
        private string $field,
        #[ExpectedValues(valuesFromClass: self::class)]
        private ?string $rule = 'string',
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
        // $this->
        return [
            'field' => $this->field,
            'rule' => $this->rule,
            'column' => $this->column,
        ];
    }

    public function compute(Registry $registry): void
    {
        $this->addDatetimeColumn($registry, $this->column, $this->field);
    }
}
