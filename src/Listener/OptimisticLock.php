<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Listener;

use Cycle\ORM\Command\ScopeCarrierInterface;
use Cycle\ORM\Command\Special\WrappedCommand;
use Cycle\ORM\Command\StoreCommandInterface;
use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnDelete;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnUpdate;
use Cycle\ORM\Entity\Behavior\Exception\OptimisticLock\ChangedVersionException;
use Cycle\ORM\Entity\Behavior\Exception\OptimisticLock\OptimisticLockException;
use Cycle\ORM\Entity\Behavior\Exception\OptimisticLock\RecordIsLockedException;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use DateTimeImmutable;
use DateTimeInterface;
use JetBrains\PhpStorm\ExpectedValues;

final class OptimisticLock
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
    /**
     * This means that the user manually sets a new version and defines the field
     */
    public const RULE_MANUAL = 'manual';

    /**
     * @var bool Listener uses predefined rule
     */
    private bool $isKnownRule;

    public function __construct(
        private string $field = 'version',
        #[ExpectedValues(valuesFromClass: self::class)]
        private string $rule = self::DEFAULT_RULE
    ) {
        $this->isKnownRule = $this->rule !== self::RULE_MANUAL;
    }

    #[Listen(OnCreate::class)]
    public function onCreate(OnCreate $event): void
    {
        if ($this->isKnownRule && !isset($event->state->getData()[$this->field])) {
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
        $nodeValue = $node->getData()[$this->field] ?? null;
        if ($nodeValue === null) {
            throw new OptimisticLockException(\sprintf('The `%s` field is not set.', $this->field));
        }

        // Process known rule
        if ($this->isKnownRule) {
            $stateValue = $state->getData()[$this->field];

            // Check diff between original and current values
            if (($stateValue <=> $nodeValue) !== 0) {
                throw new ChangedVersionException($nodeValue, $stateValue);
            }

            // Store new lock value
            if ($command instanceof StoreCommandInterface) { // todo: check working with SoftDelete behavior
                $state->register($this->field, $this->getLockingValue($nodeValue));
            }
        }

        $command->setScope($this->field, $nodeValue);

        return WrappedCommand::wrapCommand($command)
            ->withAfterExecution(static function (ScopeCarrierInterface $command) use ($node): void {
                if ($command->getAffectedRows() === 0) {
                    throw new RecordIsLockedException($node);
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
