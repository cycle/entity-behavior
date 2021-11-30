<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Schema;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\ORM\Entity\Macros\Exception\MacroCompilationException;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class ErrorsTest extends BaseTest
{
    /** @dataProvider errorsProvider */
    public function testErrors(Tokenizer $tokenizer, string $message): void
    {
        $this->expectException(MacroCompilationException::class);
        $this->expectExceptionMessageMatches(sprintf('/%s/', $message));

        $this->compileWithTokenizer($tokenizer);
    }

    public function errorsProvider(): array
    {
        return [
            'Field type' => [
                new Tokenizer(new TokenizerConfig([
                    'directories' => [dirname(__DIR__, 4) . '/Fixtures/Wrong/FieldType'],
                    'exclude' => [],
                ])),
                'Field wrongCreatedAt must be of type datetime.'
            ],
            'Column name' => [
                new Tokenizer(new TokenizerConfig([
                    'directories' => [dirname(__DIR__, 4) . '/Fixtures/Wrong/ColumnName'],
                    'exclude' => [],
                ])),
                'Ambiguous column name definition. The `updatedAt` field already linked with the `custom_updated_at`'
            ]
        ];
    }

    private function compileWithTokenizer(Tokenizer $tokenizer): void
    {
        $reader = new AttributeReader();

        new Schema((new Compiler())->compile(new Registry($this->dbal), [
            new Entities($tokenizer->classLocator(), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new GenerateTypecast(),
            new GenerateModifiers()
        ]));
    }
}
