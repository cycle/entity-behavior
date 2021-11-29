<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Timestamped;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Schema\Registry;
use Cycle\ORM\Entity\Macros\Common\BaseModifier;
use Cycle\ORM\Entity\Macros\Common\RegistryModifier;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 * @Attributes({
 *     @Attribute("field", type="string"),
 *     @Attribute("column", type="string")
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class CreatedAtMacro extends BaseModifier
{
    public function __construct(
        private string $field = 'createdAt',
        private string $column = 'created_at'
    ) {
    }

    protected function getListenerClass(): string
    {
        return CreatedAtListener::class;
    }

    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field
        ];
    }

    public function compute(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);

        $modifier->addDatetimeColumn($this->column, $this->field)
            ->nullable(false)
            ->defaultValue(AbstractColumn::DATETIME_NOW);
    }
}
