<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\ORM\Entity\Macros\Common\Schema\BaseModifier;
use Cycle\ORM\Entity\Macros\Common\Schema\RegistryModifier;
use Cycle\ORM\Entity\Macros\Listener\CreatedAt as Listener;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * CreateadAt behavior will automate adding a creating date to your entity. You can add the behavior to an already
 * existing created_at field, or the behavior can add the field automatically.
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 * @Attributes({
 *     @Attribute("field", type="string"),
 *     @Attribute("column", type="string")
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class CreatedAt extends BaseModifier
{
    public function __construct(
        private string $field = 'createdAt',
        private string $column = 'created_at'
    ) {
    }

    protected function getListenerClass(): string
    {
        return Listener::class;
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
