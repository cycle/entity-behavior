<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;

/**
 * Uses a version 2 (DCE Security) UUID from a local domain, local
 * identifier, host ID, clock sequence, and the current time
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Uuid2Macro extends UuidMacro
{
    /**
     * @param int $localDomain The local domain to use when generating bytes,
     *     according to DCE Security
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param IntegerObject|string|null $localIdentifier The local identifier for the
     *     given domain; this may be a UID or GID on POSIX systems, if the local
     *     domain is person or group, or it may be a site-defined identifier
     *     if the local domain is org
     * @param Hexadecimal|string|null $node A 48-bit number representing the hardware
     *     address
     * @param int|null $clockSeq A 14-bit number used to help avoid duplicates
     *     that could arise when the clock is set backwards in time or if the
     *     node ID changes
     *
     * @see \Ramsey\Uuid\UuidFactoryInterface::uuid2()
     */
    public function __construct(
        private int $localDomain,
        string $field = 'uuid',
        ?string $column = null,
        private IntegerObject|string|null $localIdentifier = null,
        private Hexadecimal|string|null $node = null,
        private ?int $clockSeq = null
    ) {
        $this->field = $field;
        $this->column = $column;
    }

    protected function getListenerClass(): string
    {
        return Uuid2Listener::class;
    }

    #[ArrayShape([
        'field' => 'string',
        'localDomain' => 'int',
        'localIdentifier' => 'string|null',
        'node' => 'string|null',
        'clockSeq' => 'int|null'
    ])]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'localDomain' => $this->localDomain,
            'localIdentifier' => $this->localIdentifier instanceof IntegerObject
                ? (string) $this->localIdentifier
                : $this->localIdentifier,
            'node' => $this->node instanceof Hexadecimal ? (string) $this->node : $this->node,
            'clockSeq' => $this->clockSeq
        ];
    }
}
