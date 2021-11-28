<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Cycle\ORM\Entity\Macros\Schema\RegistryModifier;
use Cycle\Schema\Registry;
use Cycle\ORM\Entity\Macros\Preset\BaseModifier;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class UuidMacro extends BaseModifier
{
    private string $column;

    /**
     * @param non-empty-string $field Uuid property name
     * @param int $version Uuid version
     * @param array $options Additional Uuid options
     * @param non-empty-string|null $column Uuid column name
     */
    public function __construct(
        private string $field = 'uuid',
        /** @enum({1, 2, 3, 4, 5, 6}) */
        #[ExpectedValues(valuesFromClass: UuidListener::class)]
        private int $version = UuidListener::UUID_V4,
        private array $options = [],
        ?string $column = null,
    ) {
        $this->column = $column ?? $field;
    }

    protected function getListenerClass(): string
    {
        return UuidListener::class;
    }

    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'version' => $this->version,
            'options' => $this->options
        ];
    }

    public function compute(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);

        $modifier->addUuidColumn($this->column, $this->field);
    }
}
