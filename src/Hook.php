<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros;

use Cycle\ORM\Entity\Macros\Common\Schema\BaseModifier;
use Cycle\ORM\Entity\Macros\Listener\Hook as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Hook allows easy listening for any event using callable.
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Hook extends BaseModifier
{
    /** @var callable */
    private $callable;

    /**
     * @psalm-param callable $callable Callable
     * @psalm-param class-string|non-empty-array<class-string> $events Listen events
     */
    public function __construct(
        callable $callable,
        private array|string $events
    ) {
        $this->callable = $callable;

        if (\is_string($events)) {
            $this->events = [$events];
        }
    }

    protected function getListenerClass(): string
    {
        return Listener::class;
    }

    #[ArrayShape(['callable' => 'callable', 'events' => 'array'])]
    protected function getListenerArgs(): array
    {
        return [
            'callable' => $this->callable,
            'events' => $this->events
        ];
    }
}