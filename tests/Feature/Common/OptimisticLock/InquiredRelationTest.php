<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\OptimisticLock;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\InquiredRelations\Product;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\InquiredRelations\ProductBox;
use Cycle\ORM\Entity\Behavior\Tests\Support\ListenerTestCase;
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
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

final class InquiredRelationTest extends ListenerTestCase
{
    #[\Testo\Test]
    public function testUpdateWithSelectCollection(): void
    {
        $repo = $this->orm->getRepository(Product::class);

        // Make 2 products
        $product1 = new Product();
        $product1->name = 'test';
        $product1->revision = 1;

        $product2 = new Product();
        $product2->name = 'test2';
        $product2->revision = 1;

        // Add box item
        $product1->addBoxItem(
            new ProductBox(
                $product1,
                $product2,
            ),
        );

        // Persist 2 products
        $this->save($product1, $product2);

        // Persist 2 with Fetch all products
        \Testo\Assert::same($product1->revision, 1);
        \Testo\Assert::same($product2->revision, 1);
        $product1->name = '222';

        // Persist 2
        $this->save($product1);
        \Testo\Assert::same($product1->revision, 2);

        // Persist 3 with Fetch all products
        $product1->name = '333';

        $products = $repo->findAll();
        $this->save($product1);
        \Testo\Assert::equals($product1->revision, 3);
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $schema = $this->compileSchema(new Tokenizer(new TokenizerConfig([
            'directories' => [\dirname(__DIR__, 3) . '/Fixtures/OptimisticLock/InquiredRelations'],
            'exclude' => [],
        ])));

        $this->withSchema($schema);
    }

    private function compileSchema(Tokenizer $tokenizer): SchemaInterface
    {
        $reader = new AttributeReader();
        $classLocator = $tokenizer->classLocator();
        return new Schema(
            (new Compiler())
                ->compile(
                    new Registry($this->dbal),
                    [
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
                        new SyncTables(),
                    ],
                ),
        );
    }
}
