<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\ORM\Entity\Macros\Exception\MacrosCompilationException;
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

abstract class SchemaErrorsTest extends BaseTest
{
    /** @dataProvider errorsProvider */
    public function testErrors(Tokenizer $tokenizer, string $message): void
    {
        $this->expectException(MacrosCompilationException::class);
        $this->expectExceptionMessageMatches(sprintf('/%s/', $message));

        $this->compileWithTokenizer($tokenizer);
    }

    public function errorsProvider(): array
    {
        return [
            'Field type' => [
                new Tokenizer(new TokenizerConfig([
                    'directories' => [dirname(__DIR__, 3) . '/Fixtures/Wrong/FieldType'],
                    'exclude' => [],
                ])),
                'Field wrongCreatedAt must be of type datetime.'
            ],
            'Column name' => [
                new Tokenizer(new TokenizerConfig([
                    'directories' => [dirname(__DIR__, 3) . '/Fixtures/Wrong/ColumnName'],
                    'exclude' => [],
                ])),
                'Ambiguous column name definition. The `updatedAt` field already linked with the `custom_updated_at`'
            ],
            'Callable empty array' => [
                new Tokenizer(new TokenizerConfig([
                    'directories' => [dirname(__DIR__, 3) . '/Fixtures/Wrong/CallableNotSet'],
                    'exclude' => [],
                ])),
                'Cann\'t build callable from empty array.'
            ],
            'Is not callable' => [
                new Tokenizer(new TokenizerConfig([
                    'directories' => [dirname(__DIR__, 3) . '/Fixtures/Wrong/CallableInvalid'],
                    'exclude' => [],
                ])),
                'Cann\'t build callable from instance of'
            ],
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
