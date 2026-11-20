<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Support;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\ORM\Schema;
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

/**
 * Base of the regression cases: compiles the case's entities, creates their tables and builds the ORM.
 */
abstract class IntegrationTestCase extends ListenerTestCase
{
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

        $this->withSchema(new Schema((new Compiler())->compile(new Registry($this->dbal), [
            new ResetTables(),
            new Embeddings(new TokenizerEmbeddingLocator($classLocator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($classLocator, $reader), $reader),
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
        ])));
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
