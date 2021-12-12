<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common;

use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Macros\EventDrivenCommandGenerator;
use Cycle\ORM\Entity\Macros\Tests\Utils\SimpleContainer;
use Cycle\ORM\Factory;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\Transaction;

abstract class BaseListenerTest extends BaseTest
{
    protected ?ORM $orm = null;

    public function tearDown(): void
    {
        parent::tearDown();

        $this->orm = null;
    }

    public function withSchema(SchemaInterface $schema): ORM
    {
        $this->orm = new ORM(
            new Factory(
                $this->dbal,
                RelationConfig::getDefault(),
                null,
                new ArrayCollectionFactory()
            ),
            $schema,
            new EventDrivenCommandGenerator($schema, new SimpleContainer())
        );

        return $this->orm;
    }

    protected function save(object ...$entities): void
    {
        $tr = new Transaction($this->orm);
        foreach ($entities as $entity) {
            $tr->persist($entity);
        }
        $tr->run();
    }
}
