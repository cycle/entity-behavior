<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Support;

use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\Testing\ORM\OrmTestCase;

/**
 * An ORM whose command generator dispatches the behavior listeners.
 */
abstract class ListenerTestCase extends OrmTestCase
{
    protected function withSchema(SchemaInterface|array $schema): ORMInterface
    {
        $schema = \is_array($schema) ? new Schema($schema) : $schema;

        return $this->orm = new ORM(
            new Factory($this->dbal, RelationConfig::getDefault(), null, new ArrayCollectionFactory()),
            $schema,
            new EventDrivenCommandGenerator($schema, new SimpleContainer()),
        );
    }
}
