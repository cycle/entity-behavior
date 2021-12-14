<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros;

use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Transaction\CommandGenerator;
use Cycle\ORM\Transaction\Tuple;
use Cycle\ORM\Entity\Macros\Common\Dispatcher\Dispatcher;
use Cycle\ORM\Entity\Macros\Common\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnDelete;
use Cycle\ORM\Entity\Macros\Common\Event\Mapper\Command\OnUpdate;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class EventDrivenCommandGenerator extends CommandGenerator
{
    private EventDispatcherInterface $eventDispatcher;
    private \DateTimeImmutable $generatedAt;

    // todo: add custom listener interface
    public function __construct(SchemaInterface $schema, ContainerInterface $container)
    {
        $listenerProvider = new ListenerProvider($schema, $container);

        $this->eventDispatcher = new Dispatcher($listenerProvider);
    }

    protected function storeEntity(ORMInterface $orm, Tuple $tuple, bool $isNew): ?CommandInterface
    {
        $role = $tuple->node->getRole();

        $event = $isNew
            ? new OnCreate($role, $tuple->mapper, $tuple->entity, $tuple->node, $tuple->state, $this->generatedAt)
            : new OnUpdate($role, $tuple->mapper, $tuple->entity, $tuple->node, $tuple->state, $this->generatedAt);

        $event->command = parent::storeEntity($orm, $tuple, $isNew);

        $event = $this->eventDispatcher->dispatch($event);

        return $event->command;
    }

    protected function generateParentStoreCommand(
        ORMInterface $orm,
        Tuple $tuple,
        string $parentRole,
        bool $isNew
    ): ?CommandInterface {
        $mapper = $orm->getMapper($parentRole);

        $event = new OnCreate($parentRole, $mapper, $tuple->entity, $tuple->node, $tuple->state, $this->generatedAt);
        $event->command = $isNew
            ? $mapper->queueCreate($tuple->entity, $tuple->node, $tuple->state)
            : $mapper->queueUpdate($tuple->entity, $tuple->node, $tuple->state);

        $event = $this->eventDispatcher->dispatch($event);

        return $event->command;
    }

    protected function deleteEntity(ORMInterface $orm, Tuple $tuple): ?CommandInterface
    {
        $role = $tuple->node->getRole();
        $event = new OnDelete($role, $tuple->mapper, $tuple->entity, $tuple->node, $tuple->state, $this->generatedAt);

        $event->command = parent::deleteEntity($orm, $tuple);

        $event = $this->eventDispatcher->dispatch($event);

        return $event->command;
    }

    public function generateStoreCommand(ORMInterface $orm, Tuple $tuple): ?CommandInterface
    {
        $this->generatedAt = new \DateTimeImmutable();

        $command = parent::generateStoreCommand($orm, $tuple);

        unset($this->generatedAt);

        return $command;
    }

    public function generateDeleteCommand(ORMInterface $orm, Tuple $tuple): ?CommandInterface
    {
        $this->generatedAt = new \DateTimeImmutable();

        $command = parent::generateDeleteCommand($orm, $tuple);

        unset($this->generatedAt);

        return $command;
    }
}
