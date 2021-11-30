<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Subscriber;

use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Registry;
use Cycle\Schema\SchemaModifierInterface;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class ClassSubscriberMacro implements SchemaModifierInterface
{
    private string $role;

    /**
     * @psalm-param class-string $listener
     */
    public function __construct(
        private string $listener,
        private array $args = []
    ) {
    }

    public function compute(Registry $registry): void
    {
    }

    public function render(Registry $registry): void
    {
    }

    public function modifySchema(array &$schema): void
    {
        $schema[SchemaInterface::MACROS][] = $this->args === [] ? $this->listener : [$this->listener, $this->args];
    }

    final public function withRole(string $role): static
    {
        $clone = clone $this;
        $clone->role = $role;

        return $clone;
    }
}
