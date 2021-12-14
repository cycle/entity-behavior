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
 * CreateadAt behavior will automate adding a creating date to your entity. The behavior has two parameters:
 *    - field - is a property in the entity
 *    - column - is a column in the database.
 * Behavior requires a field with the DateTime type.
 * A property in an entity and a field in the database can be added in several ways:
 *   - Can be added by a behavior automatically.
 *   - Can be configured with an existing field of the required type in the entity.
 *     If the existing field is not of the correct type, or if the property is set for a field in the database that is
 *     different from the one specified in the behavior parameters, an exception will be thrown.
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
