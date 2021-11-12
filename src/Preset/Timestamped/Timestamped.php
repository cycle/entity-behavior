<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Preset\Timestamped;

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
 *     @Attribute("fieldCreatedAt", type="string"),
 *     @Attribute("fieldUpdatedAt", type="string"),
 *     @Attribute("columnCreatedAt", type="string"),
 *     @Attribute("columnUpdatedAt", type="string")
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Timestamped extends BaseModifier
{
    public function __construct(
        private string $fieldCreatedAt = 'createdAt',
        private string $fieldUpdatedAt = 'updatedAt',
        private string $columnCreatedAt = 'created_at',
        private string $columnUpdatedAt = 'updated_at',
    ) {
    }

    protected function getListenerClass(): string
    {
        return TimestampedListener::class;
    }

    protected function getListenerArgs(): array
    {
        return [
            'fieldCreatedAt' => $this->fieldCreatedAt,
            'fieldUpdatedAt' => $this->fieldUpdatedAt,
        ];
    }

    public function compute(Registry $registry): void
    {
        $this->addDatetimeColumn($registry, $this->columnCreatedAt, $this->fieldCreatedAt);
        $this->addDatetimeColumn($registry, $this->columnUpdatedAt, $this->fieldUpdatedAt);
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
