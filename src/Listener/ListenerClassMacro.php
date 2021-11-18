<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Listener;

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
 *     @Attribute("class", type="string"),
 *     @Attribute("method", type="string"),
 *     @Attribute("parameters", type="mixed")
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class ListenerClassMacro extends BaseModifier
{
    /**
     * @param class-string $class
     */
    public function __construct(
        private string $class,
        private string $method = '__invoke',
        private mixed $parameters = null
    ) {
        if (!class_exists($this->class, false)) {
            throw new MacrosCompilationException(sprintf('Class %s not found!', $this->class));
        }

        if (!method_exists($this->class, $this->method)) {
            throw new MacrosCompilationException(sprintf('Method %s not found!', $this->method));
        }
    }

    protected function getListenerClass(): string
    {
        return ListenerClassListener::class;
    }

    protected function getListenerArgs(): array
    {
        return [
            'class' => $this->class,
            'method' => $this->method,
            'parameters' => $this->parameters
        ];
    }
}
