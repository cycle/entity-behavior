<div align="center">
    <a href="https://cycle-orm.dev" target="_blank">
        <picture>
            <source media="(prefers-color-scheme: dark)" srcset="https://github.com/cycle/.github/blob/main/logo/words-vector-dark.svg?raw=true">
            <img width="50%" align="center" src="https://github.com/cycle/.github/blob/main/logo/words-vector-light.svg?raw=true" alt="CycleORM Logo">
        </picture>
    </a>
</div>

<p align="center">Behavior attributes for Cycle ORM entities</p>

<div align="center">

[![Documentation](https://img.shields.io/badge/Documentation-blue?style=for-the-badge&logo=gitbook&logoColor=white)](https://cycle-orm.dev/docs/entity-behaviors-install)
[![Sponsor](https://img.shields.io/static/v1?style=for-the-badge&label=&message=Sponsor&logo=githubsponsors&logoColor=white&color=%23EA4AAA)](https://github.com/sponsors/cycle)
[![Discord](https://img.shields.io/discord/538114875570913290?style=for-the-badge&logo=discord&labelColor=7289d9&logoColor=white&color=39456d)](https://discord.gg/spiralphp)

[![Psalm Level](https://shepherd.dev/github/cycle/entity-behavior/level.svg)](https://shepherd.dev/github/cycle/entity-behavior)
[![Type Coverage](https://shepherd.dev/github/cycle/entity-behavior/coverage.svg)](https://shepherd.dev/github/cycle/entity-behavior)
[![Codecov](https://codecov.io/gh/cycle/entity-behavior/branch/1.x/graph/badge.svg)](https://codecov.io/gh/cycle/entity-behavior)

</div>

<br />

The package provides a collection of attributes that add behaviors to Cycle ORM entities. It also provides a convenient
API to create custom behavior attributes.

## Get Started

### Installation

```bash
composer require cycle/entity-behavior
```

[![PHP](https://img.shields.io/packagist/php-v/cycle/entity-behavior.svg?style=flat-square&logo=php)](https://packagist.org/packages/cycle/entity-behavior)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/cycle/entity-behavior.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/cycle/entity-behavior)
[![License](https://img.shields.io/packagist/l/cycle/entity-behavior.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/cycle/entity-behavior.svg?style=flat-square)](https://packagist.org/packages/cycle/entity-behavior/stats)

Requires PHP 8.2+.

### Configuration

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
    commandGenerator: $commandGenerator,
);
```

That's it. Now you can use all benefits of this package.

## Available behaviors

- [UUID](https://cycle-orm.dev/docs/entity-behaviors-uuid)
- [CreatedAt and UpdatedAt](https://cycle-orm.dev/docs/entity-behaviors-timestamps)
- [SoftDelete](https://cycle-orm.dev/docs/entity-behaviors-soft-delete)
- [OptimisticLock](https://cycle-orm.dev/docs/entity-behaviors-optimistic-lock)
- [Hook](https://cycle-orm.dev/docs/entity-behaviors-hooks)
- [EventListener](https://cycle-orm.dev/docs/entity-behaviors-event-listener)
