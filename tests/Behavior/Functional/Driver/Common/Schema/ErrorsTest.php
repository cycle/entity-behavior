<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Schema;

use Cycle\ORM\Entity\Behavior\Exception\BehaviorCompilationException;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseSchemaTest;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class ErrorsTest extends BaseSchemaTest
{
    /** @dataProvider errorsProvider */
    public function testErrors(Tokenizer $tokenizer, string $message): void
    {
        $this->expectException(BehaviorCompilationException::class);
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
