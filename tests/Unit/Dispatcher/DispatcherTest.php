<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Unit\Dispatcher;

use Cycle\ORM\Entity\Behavior\Dispatcher\Dispatcher;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Testo\Assert;
use Testo\Test;

#[Test]
final class DispatcherTest
{
    public function testStopsAfterListenerStopsPropagation(): void
    {
        $event = new class implements StoppableEventInterface {
            public array $calls = [];
            public bool $stopped = false;

            public function isPropagationStopped(): bool
            {
                return $this->stopped;
            }
        };
        $provider = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield static function (object $event): void {
                    $event->calls[] = 'first';
                    $event->stopped = true;
                };
                yield static function (object $event): void {
                    $event->calls[] = 'second';
                };
            }
        };

        $result = (new Dispatcher($provider))->dispatch($event);

        Assert::same($result, $event);
        Assert::same($event->calls, ['first']);
    }
}
