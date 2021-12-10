<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
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
use Spiral\Attributes\AttributeReader;
use Spiral\Tokenizer\Tokenizer;

abstract class BaseSchemaTest extends BaseTest
{
    protected ?Registry $registry = null;
    protected ?SchemaInterface $schema = null;

    public function compileWithTokenizer(Tokenizer $tokenizer): void
    {
        $reader = new AttributeReader();

        $this->schema = new Schema((new Compiler())->compile($this->registry = new Registry($this->dbal), [
            new Entities($tokenizer->classLocator(), $reader),
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

    public function tearDown(): void
    {
        parent::tearDown();

        $this->schema = null;
        $this->registry = null;
    }
}
