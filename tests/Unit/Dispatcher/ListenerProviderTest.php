<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Unit\Dispatcher;

use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\Dispatcher\Dispatcher;
use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnDelete;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnUpdate;
use Cycle\ORM\Entity\Behavior\Event\Mapper\QueueCommand;
use Cycle\ORM\Entity\Behavior\Hook;
use Cycle\ORM\Entity\Behavior\OptimisticLock;
use Cycle\ORM\Entity\Behavior\Schema\BaseModifier;
use Cycle\ORM\Entity\Behavior\SoftDelete;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\EventListener\Post;
use Cycle\ORM\Entity\Behavior\UpdatedAt;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\Testing\Container\SimpleContainer;
use Cycle\Testing\ORM\Memory;
use Testo\Assert;
use Testo\Test;

#[Test]
final class ListenerProviderTest
{
    public function behaviorsHandleOnCreate(): void
    {
        $orm = self::orm(
            new CreatedAt(),
            new UpdatedAt(),
            new OptimisticLock(rule: OptimisticLock::RULE_INCREMENT),
            new SoftDelete(),
            new Hook(static fn(OnCreate $event) => $event->state->register('content', 'hooked'), OnCreate::class),
        );
        $event = self::event($orm, OnCreate::class);

        (new Dispatcher(new ListenerProvider($orm->getSchema(), new SimpleContainer())))->dispatch($event);

        $data = $event->state->getData();
        Assert::same($data['createdAt'], $event->timestamp);
        Assert::same($data['updatedAt'], $event->timestamp);
        Assert::same($data['version'], 1);
        Assert::same($data['content'], 'hooked');
        Assert::false(\array_key_exists('deletedAt', $data));
    }

    public function queueCommandListenerReceivesEveryCommand(): void
    {
        $orm = self::orm(new Hook(static fn() => null, [OnCreate::class]));
        $provider = new ListenerProvider($orm->getSchema(), new SimpleContainer());

        foreach ([OnCreate::class, OnUpdate::class, OnDelete::class] as $class) {
            Assert::array($provider->getListenersForEvent(self::event($orm, $class)))->hasCount(1);
        }
    }

    public function eventWithoutListenersGetsNone(): void
    {
        $orm = self::orm(new CreatedAt());
        $provider = new ListenerProvider($orm->getSchema(), new SimpleContainer());

        Assert::same($provider->getListenersForEvent(self::event($orm, OnUpdate::class)), []);
    }

    public function roleWithoutListenersGetsNone(): void
    {
        $orm = self::orm();
        $provider = new ListenerProvider($orm->getSchema(), new SimpleContainer());

        Assert::same($provider->getListenersForEvent(self::event($orm, OnCreate::class)), []);
    }

    private static function orm(BaseModifier ...$behaviors): ORMInterface
    {
        $schema = [];
        foreach ($behaviors as $behavior) {
            $behavior->withRole('post')->modifySchema($schema);
        }

        return Memory::orm([
            'post' => $schema + [
                SchemaInterface::ENTITY => Post::class,
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'posts',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => ['id', 'title', 'content'],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]);
    }

    /**
     * @param class-string<QueueCommand> $class
     */
    private static function event(ORMInterface $orm, string $class): QueueCommand
    {
        return new $class(
            'post',
            $orm->getMapper('post'),
            new Post(),
            new Node(Node::NEW, [], 'post'),
            new State(Node::NEW, []),
            $orm->getSource('post'),
            new \DateTimeImmutable(),
        );
    }
}
