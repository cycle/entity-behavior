<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Subscriber;

use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Registry;
use Cycle\Schema\SchemaModifierInterface;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

# todo complete
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Subscriber implements SchemaModifierInterface
{
    /**
     * @param class-string[] $listeners todo: callable[]?
     */
    private array $listener;

    private string $role;

    /**
     * @param class-string ...$listener
     */
    public function __construct(string ...$listener)
    {
        $this->listener = $listener;
    }

    public function compute(Registry $registry): void
    {
    }

    public function render(Registry $registry): void
    {
    }

    public function modifySchema(array &$schema): void
    {
        foreach ($this->listener as $listener) {
            $schema[SchemaInterface::MACROS][] = $listener;
        }
    }

    final public function withRole(string $role): static
    {
        $clone = clone $this;
        $clone->role = $role;
        return $clone;
    }
}
