<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Cycle\ORM\Entity\Macros\Common\Schema\BaseModifier;
use Cycle\ORM\Entity\Macros\Common\Schema\RegistryModifier;
use Cycle\Schema\Registry;

abstract class UuidMacro extends BaseModifier
{
    protected ?string $column = null;
    protected string $field;

    public function compute(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        $this->column = $modifier->findColumnName($this->field, $this->column);

        if ($this->column !== null) {
            $modifier->addUuidColumn($this->column, $this->field);
        }
    }

    public function render(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        $this->column = $modifier->findColumnName($this->field, $this->column) ?? $this->field;

        $modifier->addUuidColumn($this->column, $this->field);
    }
}
