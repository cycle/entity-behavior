<?php

declare(strict_types=1);

namespace Cycle\SmartMapper\Dispatcher;

use Cycle\ORM\SchemaInterface;
use Cycle\SmartMapper\Attribute\Listen;
use Cycle\SmartMapper\MapperBehaviorInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

final class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array<string, array<string, array<int, callable>>>
     */
    private array $listeners = [];

    public function __construct(SchemaInterface $schema)
    {
        $this->configure($schema);
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

    private function configure(SchemaInterface $schema): void
    {
        foreach ($schema->getRoles() as $role) {
            $config = $schema->define($role, MapperBehaviorInterface::SCHEMA_LISTENERS_CONTAINER);
            if (!is_array($config) || $config === []) {
                continue;
            }
            $this->resolveListeners($role, $config);
        }
    }

    private function resolveListeners(string $role, array $config): void
    {
        foreach ($config as $definition) {
            assert(is_array($definition) || is_string($definition));

            $definition = (array)$definition;

            if (!$this->validateDefinition($definition)) {
                continue;
            }
            $class = $definition[MapperBehaviorInterface::DEFINITION_CLASS];
            $arguments = $definition[MapperBehaviorInterface::DEFINITION_ARGS] ?? [];

            $events = $this->findListeners($class);
            if ($events === []) {
                continue;
            }

            try {
                $listener = new $class(...$arguments);
            } catch (\Throwable $e) {
                throw new \Exception("Cann't create listener `$class` for the `$role` role.", 0, $e);
            }

            foreach ($events as [$event, $method]) {
                $this->listeners[$role][$event][] = [$listener, $method];
            }
        }
    }

    private function validateDefinition(mixed $definition): bool
    {
        if (!class_exists($definition[MapperBehaviorInterface::DEFINITION_CLASS], true)) {
            return false;
        }
        if (array_key_exists(MapperBehaviorInterface::DEFINITION_ARGS, $definition)
            && !is_array($definition[MapperBehaviorInterface::DEFINITION_ARGS])) {
            return false;
        }
        return true;
    }

    /**
     * @param class-string $class
     *
     * @return array<int, array{string, string}> Array of [event, method]
     */
    private function findListeners(string $class): array
    {
        $result = [];
        foreach ((new \ReflectionClass($class))->getMethods() as $method) {
            foreach ($method->getAttributes(Listen::class) as $attribute) {
                try {
                    $listen = $attribute->newInstance();
                    assert($listen instanceof Listen);
                    $result[] = [$listen->event, $method->getName()];
                } catch (\Throwable $e) {
                    throw new \Exception(sprintf(
                            "Cann't instantiate attribute %s in the %s::%s method.",
                            Listen::class,
                            $class,
                            $method->getName()
                        ), 0, $e
                    );
                }
            }
        }
        return $result;
    }
}
