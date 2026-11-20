<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\Schema;

use Cycle\ORM\Entity\Behavior\Exception\BehaviorCompilationException;
use Cycle\ORM\Entity\Behavior\Tests\Support\SchemaTestCase;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

final class ErrorsTest extends SchemaTestCase
{
    public static function errorsProvider(): array
    {
        return [
            'Field type' => [
                new Tokenizer(new TokenizerConfig([
                    'directories' => [\dirname(__DIR__, 3) . '/Fixtures/Wrong/FieldType'],
                    'exclude' => [],
                ])),
                'Field wrongCreatedAt must be of type datetime.',
            ],
            'Column name' => [
                new Tokenizer(new TokenizerConfig([
                    'directories' => [\dirname(__DIR__, 3) . '/Fixtures/Wrong/ColumnName'],
                    'exclude' => [],
                ])),
                'Ambiguous column name definition. The `updatedAt` field already linked with the `custom_updated_at`',
            ],
        ];
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('errorsProvider')]
    public function testErrors(Tokenizer $tokenizer, string $message): void
    {
        \Testo\Expect::exception(BehaviorCompilationException::class)->withMessageMatchingRegex(\sprintf('/%s/', $message));

        $this->compileWithTokenizer($tokenizer);
    }
}
