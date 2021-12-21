# Cycle ORM Entity Behavior
[![Latest Stable Version](https://poser.pugx.org/cycle/entity-behavior/version)](https://packagist.org/packages/cycle/entity-behavior)
[![Build Status](https://github.com/cycle/entity-behavior/workflows/build/badge.svg)](https://github.com/cycle/entity-behavior/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cycle/entity-behavior/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/cycle/entity-behavior/?branch=1.x)
[![Codecov](https://codecov.io/gh/cycle/entity-behavior/graph/badge.svg)](https://codecov.io/gh/cycle/entity-behavior)
<a href="https://discord.gg/TFeEmCs"><img src="https://img.shields.io/badge/discord-chat-magenta.svg"></a>

The package provides a collection of attributes that add behaviors to Cycle ORM entities. It also provides a convenient
API to create custom behavior attributes.

## Installation

The package is available via composer and can be installed using the following command:

```bash
composer require cycle/entity-behavior
```

## Configuration

After installation the package you need to create `Cycle\ORM\ORM` object with
passing `\Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator` generator object as third (`commandGenerator`)
argument.

**Example**

```php
use Cycle\ORM\ORM;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;

// Application container (PSR-11 compatible).
// https://www.php-fig.org/psr/psr-11/
$container = new Container();
$commandGenerator = new EventDrivenCommandGenerator($schema, $container);

$orm = new ORM(
  factory: $factory, 
  schema: $schema, 
  commandGenerator: $commandGenerator
);
```

That's it. Now you can use all benefits of this package.

### Available behaviors

- [UUID](https://cycle-orm.dev/docs/entity-behaviors-uuid)
- [CreatedAt and UpdatedAt](https://cycle-orm.dev/docs/entity-behaviors-timestamps)
- [SoftDelete](https://cycle-orm.dev/docs/entity-behaviors-soft-delete)
- [OptimisticLock](https://cycle-orm.dev/docs/entity-behaviors-optimistic-lock)
- [Hook](https://cycle-orm.dev/docs/entity-behaviors-hooks)
- [EventListener](https://cycle-orm.dev/docs/entity-behaviors-event-listener)

## License:

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained
by [Spiral Scout](https://spiralscout.com).
