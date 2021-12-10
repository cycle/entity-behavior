<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros;

use Cycle\ORM\Entity\Macros\Common\Schema\BaseModifier;
use Cycle\ORM\Entity\Macros\Common\Schema\RegistryModifier;
use Cycle\ORM\Entity\Macros\Listener\UpdatedAt as Listener;
use Cycle\Schema\Registry;
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
final class UpdatedAt extends BaseModifier
{
    public function __construct(
        private string $field = 'updatedAt',
        private string $column = 'updated_at'
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

        $modifier->addDatetimeColumn($this->column, $this->field);
    }
}
