<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\SoftDelete;

use Cycle\ORM\Entity\Behavior\Tests\Fixtures\SoftDelete\Post;
use Cycle\ORM\Entity\Behavior\Tests\Support\SchemaTestCase;
use Cycle\ORM\Schema\GeneratedField;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class SoftDeleteTest extends SchemaTestCase
{
    #[Test]
    public function testExistenceColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('deletedAt'));
        Assert::true($fields->hasColumn('deleted_at'));
        Assert::same($fields->get('deletedAt')->getType(), 'datetime');
        Assert::same($fields->get('deletedAt')->getGenerated(), GeneratedField::BEFORE_UPDATE);

        // No new fields added
        Assert::same($fields->count(), 3);
    }

    #[Test]
    public function testAddedColumn(): void
    {
        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('newField'));
        Assert::true($fields->hasColumn('new_field'));
        Assert::same($fields->get('newField')->getType(), 'datetime');
        Assert::same($fields->get('newField')->getGenerated(), GeneratedField::BEFORE_UPDATE);
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->compileWithTokenizer(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/SoftDelete'],
            'exclude' => [],
        ])));
    }
}
