<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Timestamped;

use Cycle\Schema\Definition\Field;
use Cycle\Schema\Registry;
use Cycle\ORM\Entity\Macros\Preset\BaseModifier;
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
        return UpdatedAtListener::class;
    }

    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field
        ];
    }

    public function compute(Registry $registry): void
    {
        $this->addDatetimeColumn($registry, $this->column, $this->field);
    }

    private function addDatetimeColumn(Registry $registry, string $columnName, string $fieldName): void
    {
        $entity = $registry->getEntity($this->role);
        $table = $registry->getTableSchema($entity);
        $fields = $entity->getFields();

        if ($fields->has($fieldName)) {
            // todo check field
            return;
        }

        $field = new Field();
        $field->setColumn($columnName)->setType('datetime')->setTypecast('datetime');
        $table->column($field->getColumn())->type($field->getType());
        $fields->set($fieldName, $field);
    }
}
