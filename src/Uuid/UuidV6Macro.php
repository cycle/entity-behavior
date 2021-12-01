<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Uuid\Type\Hexadecimal;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class UuidV6Macro extends UuidMacro
{
    /**
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param Hexadecimal|null $node
     * @param int|null $clockSeq
     */
    public function __construct(
        string $field = 'uuid',
        ?string $column = null,
        private ?Hexadecimal $node = null,
        private ?int $clockSeq = null
    ) {
        $this->field = $field;
        $this->column = $column;
    }

    protected function getListenerClass(): string
    {
        return UuidV6Listener::class;
    }

    #[ArrayShape(['field' => 'string', 'node' => Hexadecimal::class, 'clockSeq' => 'int|null'])]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'node' => $this->node,
            'clockSeq' => $this->clockSeq
        ];
    }
}
