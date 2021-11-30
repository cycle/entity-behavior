<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Subscriber;

use Cycle\ORM\Entity\Macros\Exception\MacroCompilationException;
use Cycle\ORM\Entity\Macros\Common\Schema\BaseModifier;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class CallableSubscriberMacro extends BaseModifier
{
    /**
     * @psalm-param non-empty-string|non-empty-array<string> $callable Callable
     * @psalm-param class-string|non-empty-array<class-string> $events Listen events
     */
    public function __construct(
        private array|string $callable,
        private array|string $events
    ) {
        if (\is_string($callable)) {
            $this->callable = [$callable];
        }
        if (\is_string($events)) {
            $this->events = [$events];
        }

        if ($callable === []) {
            throw new MacroCompilationException('Cann\'t build callable from empty array.');
        }

        if (!\is_callable($callable)) {
            throw new MacroCompilationException(
                sprintf(
                    'Cann\'t build callable from instance of `%s` and `%s` method name.',
                    $callable[0],
                    $callable[1]
                )
            );
        }
    }

    protected function getListenerClass(): string
    {
        return CallableSubscriberListener::class;
    }

    #[ArrayShape(['callable' => 'array', 'events' => 'array'])]
    protected function getListenerArgs(): array
    {
        return [
            'callable' => $this->callable,
            'events' => $this->events
        ];
    }
}
