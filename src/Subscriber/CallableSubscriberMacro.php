<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Subscriber;

use Cycle\ORM\Entity\Macros\Exception\MacrosCompilationException;
use Cycle\ORM\Entity\Macros\Preset\BaseModifier;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 * @Attributes({
 *     @Attribute("callable", type="mixed"),
 *     @Attribute("events", type="mixed")
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class CallableSubscriberMacro extends BaseModifier
{
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
            throw new MacrosCompilationException('Cann\'t build callable from empty array.');
        }

        if (!\is_callable($callable)) {
            throw new MacrosCompilationException(
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

    protected function getListenerArgs(): array
    {
        return [
            'callable' => $this->callable,
            'events' => $this->events
        ];
    }
}
