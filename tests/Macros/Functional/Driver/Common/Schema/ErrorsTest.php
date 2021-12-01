<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Schema;

use Cycle\ORM\Entity\Macros\Exception\MacroCompilationException;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
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
}
