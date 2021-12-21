# Cycle ORM Entity Behavior

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

- [UUID](https://cycle-orm.dev/docs/en/entity-behaviors/uuid.md)
- [CreatedAt and UpdatedAt](https://cycle-orm.dev/docs/en/entity-behaviors/timestamps.md)
- [SoftDelete](https://cycle-orm.dev/docs/en/entity-behaviors/soft-delete.md)
- [OptimisticLock](https://cycle-orm.dev/docs/en/entity-behaviors/optimistic-lock.md)
- [Hook](https://cycle-orm.dev/docs/en/entity-behaviors/hooks.md)
- [EventListener](https://cycle-orm.dev/docs/en/entity-behaviors/event-listener.md)

## License:

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained
by [Spiral Scout](https://spiralscout.com).
