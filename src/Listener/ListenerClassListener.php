<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Listener;

use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Event\Mapper\QueueCommand;
use Cycle\ORM\Entity\Macros\Exception\MacrosExecutionException;

final class ListenerClassListener
{
    /**
     * @psalm-param class-string $class
     */
    public function __construct(
        private string $class,
        private string $method = '__invoke',
        private mixed $parameters = null
    ) {
    }

    #[Listen(QueueCommand::class)]
    public function __invoke(QueueCommand $event): void
    {
        if (!$this->isListenEvent($event)) {
            return;
        }

        $callable = (new $this->class($event, $this->parameters));

        $callable->{$this->method}($event, $this->parameters);
    }

    private function isListenEvent(QueueCommand $event): bool
    {
        $ref = new \ReflectionClass($this->class);
        $parameters = $ref->getMethod($this->method)->getParameters();

        if (!isset($parameters[0])) {
            throw new MacrosExecutionException(
                sprintf(
                    'The first parameter of method %s::%s must be an instance of an event!',
                    $this->class,
                    $this->method
                )
            );
        }

        $type = $parameters[0]->getType();
        if (!$type instanceof \ReflectionUnionType && !$type instanceof \ReflectionNamedType) {
            return false;
        }
        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($unionType->getName() === $event::class) {
                    return true;
                }
            }

            return false;
        }

        return $type->getName() === $event::class;
    }
}
