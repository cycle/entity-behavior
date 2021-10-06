<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Preset;

use Cycle\ORM\Entity\Macros\Dispatcher\ListenerProvider;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Registry;
use Cycle\Schema\SchemaModifierInterface;

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
            $schema[SchemaInterface::MACROS][] = $this->getListenerClass();
            return;
        }
        $schema[SchemaInterface::MACROS][] = [
            ListenerProvider::DEFINITION_CLASS => $this->getListenerClass(),
            ListenerProvider::DEFINITION_ARGS => $args,
        ];
    }
}
