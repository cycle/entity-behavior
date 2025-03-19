<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Integration;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Entity\Behavior\Tests\Utils\SimpleContainer;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Transaction\UnitOfWork;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\ForeignKeys;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderModifiers;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class BaseTest extends \Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseTest
{
    protected SchemaInterface $schema;
    protected ORMInterface $orm;

    public function tearDown(): void
    {
        $this->disableProfiling();
        unset($this->orm, $this->schema);
        parent::tearDown();
    }

    /**
     * @param non-empty-array<non-empty-string>|non-empty-string $entitiesPath
     */
    protected function prepareOrm(string|array $entitiesPath): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => (array) $entitiesPath,
            'exclude' => [],
        ]));

        $reader = new AttributeReader();

        $classLocator = $tokenizer->classLocator();

        $this->schema = new Schema((new Compiler())->compile(new Registry($this->dbal), [
            new ResetTables(),
            new Embeddings($classLocator, $reader),
            new Entities($classLocator, $reader),
            new TableInheritance($reader),
            new MergeColumns($reader),
            new GenerateRelations(),
            new GenerateModifiers(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new RenderModifiers(),
            new ForeignKeys(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]));

        $this->orm = new ORM(
            new Factory(
                $this->dbal,
                RelationConfig::getDefault(),
                null,
                new ArrayCollectionFactory(),
            ),
            $this->schema,
            new EventDrivenCommandGenerator($this->schema, new SimpleContainer()),
        );
    }

    protected function save(object ...$entities): void
    {
        $uow = new UnitOfWork($this->orm);

        foreach ($entities as $entity) {
            $uow->persistDeferred($entity);
        }
        $result = $uow->run();
        $result->isSuccess() or throw $uow->getLastError();
    }

    protected function cleanHeap(): void
    {
        $this->orm->getHeap()->clean();
    }
}
