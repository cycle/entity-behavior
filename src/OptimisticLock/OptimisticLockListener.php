<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\OptimisticLock;

use Cycle\ORM\Command\ScopeCarrierInterface;
use Cycle\ORM\Command\Special\WrappedCommand;
use Cycle\ORM\Command\StoreCommandInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnDelete;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnUpdate;
use Cycle\ORM\Heap\State;
use DateTimeImmutable;
use DateTimeInterface;
use JetBrains\PhpStorm\ExpectedValues;

final class OptimisticLockListener
{
    public const DEFAULT_RULE = self::RULE_INCREMENT;

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
        private string $rule = self::DEFAULT_RULE
    ) {
    }

    #[Listen(OnCreate::class)]
    public function onCreate(OnCreate $event): void
    {
        if (!isset($event->state->getData()[$this->field])) {
            $event->state->register($this->field, $this->getLockingValue(0));
        }
    }

    #[Listen(OnUpdate::class)]
    #[Listen(OnDelete::class)]
    public function __invoke(OnDelete|OnUpdate $event): void
    {
        if (!$event->command instanceof ScopeCarrierInterface) {
            return;
        }
        $event->command = $this->lock($event->node, $event->state, $event->command);
    }

    private function lock(Node $node, State $state, ScopeCarrierInterface $command): WrappedCommand
    {
        $scopeValue = $node->getData()[$this->field] ?? null;
        if ($scopeValue === null) {
            throw new \RuntimeException(\sprintf('The `%s` field is not set.', $this->field));
        }

        // Check if a new lock-value has been assigned
        if ($command instanceof StoreCommandInterface && $state->getData()[$this->field] === $scopeValue) {
            // Generate new value
            $state->register($this->field, $this->getLockingValue($scopeValue));
        }

        $command->setScope($this->field, $scopeValue);

        return WrappedCommand::wrapCommand($command)
            ->withAfterExecution(static function (ScopeCarrierInterface $command) use ($node): void {
                if ($command->getAffectedRows() === 0) {
                    throw new OptimisticLockException($node);
                }
            });
    }

    private function getLockingValue(mixed $previousValue): int|string|DateTimeInterface
    {
        return match ($this->rule) {
            self::RULE_INCREMENT => (int)$previousValue + 1,
            self::RULE_DATETIME => new DateTimeImmutable(),
            self::RULE_RAND_STR => \bin2hex(\random_bytes(16)),
            default => \number_format(\microtime(true), 6, '.', '')
        };
    }
}
