<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Support;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderModifiers;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Cycle\Testing\Database\DatabaseTestCase;
use Spiral\Attributes\AttributeReader;
use Spiral\Tokenizer\Tokenizer;

/**
 * Compiles the annotated entities of a directory without creating tables.
 */
abstract class SchemaTestCase extends DatabaseTestCase
{
    protected Registry $registry;
    protected SchemaInterface $schema;

    protected function compileWithTokenizer(Tokenizer $tokenizer): void
    {
        $reader = new AttributeReader();

        $classLocator = $tokenizer->classLocator();

        $this->schema = new Schema((new Compiler())->compile($this->registry = new Registry($this->dbal), [
            new Embeddings(new TokenizerEmbeddingLocator($classLocator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($classLocator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new MergeIndexes($reader),
            new GenerateRelations(),
            new GenerateModifiers(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new RenderModifiers(),
            new GenerateTypecast(),
        ]));
    }

    /**
     * Stops after the modifiers' compute phase, before render can fill in what compute left out.
     */
    protected function computeWithTokenizer(Tokenizer $tokenizer): void
    {
        $reader = new AttributeReader();
        $classLocator = $tokenizer->classLocator();

        (new Compiler())->compile($this->registry = new Registry($this->dbal), [
            new Embeddings(new TokenizerEmbeddingLocator($classLocator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($classLocator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateModifiers(),
        ]);
    }
}
