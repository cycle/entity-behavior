<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Exception\MacroExecutionException;

final class UuidListener
{
    public const UUID_V1 = 1;
    public const UUID_V2 = 2;
    public const UUID_V3 = 3;
    public const UUID_V4 = 4;
    public const UUID_V5 = 5;
    public const UUID_V6 = 6;

    public function __construct(
        private UuidFactoryInterface $uuidFactory,
        private string $field = 'uuid',
        private int $version = self::UUID_V4,
        private array $options = []
    ) {
        if ($this->version === self::UUID_V2 && !isset($this->options['localDomain'])) {
            throw new MacroExecutionException('Option `localDomain` must not be empty.');
        }
        if (
            ($this->version === self::UUID_V3 || $this->version === self::UUID_V5) &&
            (!isset($this->options['namespace']) || !isset($this->options['name']))
        ) {
            throw new MacroExecutionException('Options `namespace` and `name` must not be empty.');
        }
    }

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        $event->state->register($this->field, $this->generateUuid());
    }

    private function generateUuid(): UuidInterface
    {
        return match ($this->version) {
            self::UUID_V1 => $this->uuidFactory->v1($this->options['node'] ?? null, $this->options['clockSeq'] ?? null),
            self::UUID_V2 => $this->uuidFactory->v2(
                (int) $this->options['localDomain'],
                $this->options['localIdentifier'] ?? null,
                $this->options['node'] ?? null,
                $this->options['clockSeq'] ?? null
            ),
            self::UUID_V3 => $this->uuidFactory->v3($this->options['namespace'], (string) $this->options['name']),
            self::UUID_V5 => $this->uuidFactory->v5($this->options['namespace'], (string) $this->options['name']),
            self::UUID_V6 => $this->uuidFactory->v6($this->options['node'] ?? null, $this->options['clockSeq'] ?? null),
            default => $this->uuidFactory->v4()
        };
    }
}
