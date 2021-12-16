<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior;

use Cycle\ORM\Entity\Behavior\Schema\BaseModifier;
use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Cycle\ORM\Entity\Behavior\Listener\SoftDelete as Listener;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * SoftDelete implements the soft delete strategy, replaces Delete command with Update command and set current
 * timestamp in the configured field.
 * Keep in mind that SoftDelete behavior doesn't run events related to Update command.
 * The behavior has two parameters:
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
final class SoftDelete extends BaseModifier
{
    private ?string $column = null;

    public function __construct(
        private string $field = 'deletedAt',
        ?string $column = null
    ) {
        $this->column = $column;
    }

    protected function getListenerClass(): string
    {
        return Listener::class;
    }

    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
        ];
    }

    public function compute(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        $this->column = $modifier->findColumnName($this->field, $this->column);

        if ($this->column !== null) {
            $modifier->addDatetimeColumn($this->column, $this->field)
                ->nullable(true);
        }
    }

    public function render(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);

        $this->column = $modifier->findColumnName($this->field, $this->column) ?? $this->field;

        $modifier->addDatetimeColumn($this->column, $this->field)
            ->nullable(true);
    }
}
