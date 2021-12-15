<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior;

use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Registry;
use Cycle\Schema\SchemaModifierInterface;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * EventListener adds a custom listener to the ORM schema. Allows you to create your own behaviors.
 * A custom listener can accept the required dependencies in the constructor, implement one or more methods
 * that receive the event as the first parameter and can work with him.
 * The behavior has two parameters:
 *   - listener - listener class
 *   - args - array with additional parameters
 *
 * A special attribute must be added to the listener methods to indicate which event to listen for.
 * For example:
 * #[Listen(OnCreate::class)]
 * public function method(OnCreate $event): void
 * {
 *    //
 * }
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class EventListener implements SchemaModifierInterface
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
