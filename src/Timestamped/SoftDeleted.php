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
 * SoftDeleted replaces Delete command with Update command and set current timestamp in the configured field.
 * Keep in mind that SoftDelete behavior doesn't run events related to Update command.
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
final class SoftDeleted extends BaseModifier
{
    public function __construct(
        private string $field = 'deletedAt',
        private string $column = 'deleted_at',
    ) {
    }

    protected function getListenerClass(): string
    {
        return SoftDeletedListener::class;
    }

    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
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
        $table->column($field->getColumn())
            ->type($field->getType())
            ->nullable(true)
            ->defaultValue(null);
        $fields->set($fieldName, $field);
    }
}
