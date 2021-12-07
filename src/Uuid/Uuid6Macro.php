<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Uuid\Type\Hexadecimal;

/**
 * Uses a version 6 (ordered-time) UUID from a host ID, sequence number,
 * and the current time
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Uuid6Macro extends UuidMacro
{
    /**
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param Hexadecimal|string|null $node A 48-bit number representing the hardware
     * @param int|null $clockSeq A 14-bit number used to help avoid duplicates
     *     that could arise when the clock is set backwards in time or if the
     *     node ID changes
     *
     * @see \Ramsey\Uuid\UuidFactoryInterface::uuid6()
     */
    public function __construct(
        string $field = 'uuid',
        ?string $column = null,
        private Hexadecimal|string|null $node = null,
        private ?int $clockSeq = null
    ) {
        $this->field = $field;
        $this->column = $column;
    }

    protected function getListenerClass(): string
    {
        return Uuid6Listener::class;
    }

    #[ArrayShape(['field' => 'string', 'node' => 'string|null', 'clockSeq' => 'int|null'])]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'node' => $this->node instanceof Hexadecimal ? (string) $this->node : $this->node,
            'clockSeq' => $this->clockSeq
        ];
    }
}
