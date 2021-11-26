<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\OptimisticLock;

use Cycle\ORM\Command\Database\Update;
use Cycle\ORM\Command\ScopeCarrierInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnDelete;
use Cycle\ORM\Entity\Macros\Event\Mapper\Command\OnUpdate;
use DateTimeImmutable;
use DateTimeInterface;
use JetBrains\PhpStorm\ExpectedValues;

final class OptimisticLockListener
{
    /**
     * Generates current timestamp with microseconds as string
     */
    public const RULE_MICROTIME = 'microtime';
    /**
     * Uses `random_bytes(32)` under hood
     */
    public const RULE_RAND_STR = 'random-string';
    /**
     * Only for the numeric column
     */
    public const RULE_INCREMENT = 'increment';
    /**
     * Only for the column of the `datetime` type
     */
    public const RULE_DATETIME = 'datetime';

    public function __construct(
        private string $field = 'version',
        #[ExpectedValues(valuesFromClass: self::class)]
        private string $rule = self::RULE_INCREMENT
    ) {
    }

    #[Listen(OnCreate::class)]
    public function onCreate(OnCreate $event): void
    {
        $event->state->register($this->field, $this->getLockingValue(0));
    }

    #[Listen(OnUpdate::class)]
    #[Listen(OnDelete::class)]
    public function __invoke(OnDelete|OnUpdate $event): void
    {
        if (!$event->command instanceof ScopeCarrierInterface) {
            return;
        }

        $this->lock($event->node, $event->command);
    }

    private function lock(Node $node, ScopeCarrierInterface $command): void
    {
        $scopeValue = $node->getInitialData()[$this->field] ?? null;
        if ($scopeValue === null) {
            throw new \RuntimeException(sprintf('The `%s` field is not set.', $this->field));
        }

        if ($node->getData()[$this->field] !== $scopeValue) {
            throw new OptimisticLockException($node);
        }

        // Check if a new lock-value has been assigned
        if ($command instanceof Update && $node->getData()[$this->field] === $scopeValue) {
            // Generate new value
            $command->register($this->field, $this->getLockingValue($scopeValue));
        }

        $command->setScope($this->field, $scopeValue);
    }

    private function getLockingValue(mixed $previousValue): int|string|DateTimeInterface
    {
        return match ($this->rule) {
            self::RULE_INCREMENT => (int)$previousValue + 1,
            self::RULE_DATETIME => new DateTimeImmutable(),
            self::RULE_RAND_STR => bin2hex(\random_bytes(32)),
            default => number_format(microtime(true), 6)
        };
    }
}
