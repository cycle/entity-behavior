<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\ORM\Entity\Macros\Tests\Fixtures\Post;
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

abstract class SchemaTest extends BaseTest
{
    protected Registry $registry;

    public function setUp(): void
    {
        parent::setUp();

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [dirname(__DIR__, 3) . '/Fixtures'],
            'exclude' => ['Wrong'],
        ]));
        $reader = new AttributeReader();
        $this->registry = new Registry($this->dbal);

        new Schema((new Compiler())->compile($this->registry, [
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

    public function testAddDynamicDatetimeField(): void
    {
        $entity = $this->registry->getEntity(Post::class);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('dynamicUpdatedAt'));
        $this->assertSame('datetime', $fields->get('dynamicUpdatedAt')->getType());
        $this->assertSame('dynamic_updated_at', $fields->get('dynamicUpdatedAt')->getColumn());
    }
}
