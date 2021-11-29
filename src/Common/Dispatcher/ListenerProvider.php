<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Common\Dispatcher;

use Cycle\ORM\Entity\Macros\Common\Event\MapperEvent;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\QueueCommand;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Entity\Macros\Attribute\Listen;
use Cycle\ORM\Entity\Macros\Exception\Dispatcher\RuntimeException;
use Yiisoft\Injector\Injector;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

// todo: how to replace listener resolver
final class ListenerProvider implements ListenerProviderInterface
{
    public const DEFINITION_CLASS = 0;
    public const DEFINITION_ARGS = 1;

    /**
     * @var array<string, array<string, array<int, callable>>>
     */
    private array $listeners = [];

    public function __construct(
        SchemaInterface $schema,
        ContainerInterface $container
    ) {
        $this->configure($schema, new Injector($container));
    }

    public function getListenersForEvent(object $event): iterable
    {
        assert($event instanceof MapperEvent);

        $role = $event->role;
        if (!array_key_exists($role, $this->listeners)) {
            return [];
        }

        if (!array_key_exists($event::class, $this->listeners[$role])) {
            return [];
        }

        return $this->listeners[$role][$event::class];
    }

    private function configure(SchemaInterface $schema, Injector $injector): void
    {
        foreach ($schema->getRoles() as $role) {
            $config = $schema->define($role, SchemaInterface::MACROS);
            if (!is_array($config) || $config === []) {
                continue;
            }
            $this->resolveListeners($role, $config, $injector);
        }
    }

    private function resolveListeners(string $role, array $config, Injector $injector): void
    {
        foreach ($config as $definition) {
            assert(is_array($definition) || is_string($definition));

            $definition = (array)$definition;

            if (!$this->validateMacrosDefinition($definition)) {
                continue;
            }
            /** @psalm-var class-string $class */
            $class = $definition[self::DEFINITION_CLASS];
            $arguments = $definition[self::DEFINITION_ARGS] ?? [];

            $events = $this->findListenersInAttributes($class);
            try {
                $listener = $injector->make($class, $arguments);

                if ($listener instanceof EventListProviderInterface) {
                    $events = [...$events, ...$this->getProvidedListeners($listener)];
                }
            } catch (\Throwable $e) {
                throw new \Exception("Cann't create listener `$class` for the `$role` role.", 0, $e);
            }

            if ($events === []) {
                continue;
            }

            foreach ($events as [$event, $method]) {
                if ($event === QueueCommand::class) {
                    $this->listeners[$role][Command\AfterCreate::class][] = [$listener, $method];
                    $this->listeners[$role][Command\AfterDelete::class][] = [$listener, $method];
                    $this->listeners[$role][Command\AfterUpdate::class][] = [$listener, $method];
                    $this->listeners[$role][Command\OnCreate::class][] = [$listener, $method];
                    $this->listeners[$role][Command\OnUpdate::class][] = [$listener, $method];
                    $this->listeners[$role][Command\OnDelete::class][] = [$listener, $method];
                } else {
                    $this->listeners[$role][$event][] = [$listener, $method];
                }
            }
        }
    }

    private function validateMacrosDefinition(mixed $definition): bool
    {
        if (!class_exists($definition[self::DEFINITION_CLASS], true)) {
            return false;
        }
        if (
            array_key_exists(self::DEFINITION_ARGS, $definition) &&
            !is_array($definition[self::DEFINITION_ARGS])
        ) {
            return false;
        }
        return true;
    }

    /**
     * @return array<int, array{string, string}> Array of [event, method]
     */
    private function getProvidedListeners(EventListProviderInterface $listener): array
    {
        $events = $listener->getEventsList();
        foreach ($events as $event) {
            // validate
            if (!is_array($event) || array_keys($event) !== [0, 1]) {
                throw new RuntimeException(
                    sprintf(
                        'The method %s::getEventsList() should return list of tuples [event-class, listener-method].',
                        $listener::class
                    )
                );
            }
            $method = $event[1];
            $callable = [$listener, $method];
            if (!\is_callable($callable)) {
                throw new RuntimeException(
                    sprintf(
                        'Cann\'t build callable from instance of `%s` and `%s` method name.',
                        $listener::class,
                        $method
                    )
                );
            }
        }

        return $events;
    }

    /**
     * @param class-string $class
     *
     * @return array<int, array{string, string}> Array of [event, method]
     */
    private function findListenersInAttributes(string $class): array
    {
        $result = [];
        foreach ((new \ReflectionClass($class))->getMethods() as $method) {
            foreach ($method->getAttributes(Listen::class) as $attribute) {
                try {
                    $listen = $attribute->newInstance();
                    assert($listen instanceof Listen);
                } catch (\Throwable $e) {
                    throw new RuntimeException(sprintf(
                        "Cann't instantiate attribute %s in the %s::%s method.",
                        Listen::class,
                        $class,
                        $method->getName()
                    ), 0, $e);
                }
                $result[] = [$listen->event, $method->getName()];
            }
        }
        return $result;
    }
}
