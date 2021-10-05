<?php

declare(strict_types=1);

namespace Cycle\SmartMapper\Behavior;

use Cycle\Schema\Registry;
use Cycle\Schema\SchemaModifierInterface;
use Cycle\SmartMapper\MapperBehaviorInterface;

abstract class BaseModifier implements SchemaModifierInterface
{
    protected string $role;

    /**
     * @return class-string
     */
    abstract protected function getListenerClass(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function getListenerArgs(): array;

    public function compute(Registry $registry): void
    {
    }

    public function render(Registry $registry): void
    {
    }

    final public function withRole(string $role): static
    {
        $clone = clone $this;
        $clone->role = $role;
        return $clone;
    }

    final public function modifySchema(array &$schema): void
    {
        // todo: compare with default constructor values
        $args = $this->getListenerArgs();
        if ($args === []) {
            $schema[MapperBehaviorInterface::SCHEMA_LISTENERS_CONTAINER][] = $this->getListenerClass();
            return;
        }
        $schema[MapperBehaviorInterface::SCHEMA_LISTENERS_CONTAINER][] = [
            MapperBehaviorInterface::DEFINITION_CLASS => $this->getListenerClass(),
            MapperBehaviorInterface::DEFINITION_ARGS => $args,
        ];
    }
}
