<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\OptimisticLock;

use Cycle\ORM\Entity\Behavior\Exception\OptimisticLock\ChangedVersionException;
use Cycle\ORM\Entity\Behavior\Exception\OptimisticLock\RecordIsLockedException;
use Cycle\ORM\Entity\Behavior\Listener\OptimisticLock;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Comment;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\Author;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\InquiredRelations\Product;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\InquiredRelations\ProductBox;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\InquiredRelations\ProductRepository;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\BaseListenerTest;
use Cycle\ORM\Entity\Behavior\Tests\Traits\TableTrait;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction;

abstract class InquiredRelationTest extends BaseListenerTest
{
    use TableTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            'products2',
            [
                'id' => 'primary',
                'name' => 'string,nullable',
                'revision' => 'int',
            ]
        );

        $this->makeTable(
            'product_boxes',
            [
                'id' => 'primary',
                'parent_id' => 'int',
                'box_item_id' => 'int',
                'count' => 'int',
            ]
        );
        $this->withSchema(new Schema([
            Product::class => [
                SchemaInterface::ROLE => 'product2',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::REPOSITORY => ProductRepository::class,
                SchemaInterface::TABLE => 'products2',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => [
                    'id' => 'id',
                    'name' => 'name',
                    'revision' => 'revision',
                ],
                SchemaInterface::LISTENERS => [
                    [
                        OptimisticLock::class,
                        [
                            'field' => 'revision',
                            'rule' => OptimisticLock::RULE_INCREMENT
                        ]
                    ]
                ],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'revision' => 'int'
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [
                    'boxItems' => [
                        Relation::TYPE => Relation::HAS_MANY,
                        Relation::TARGET => ProductBox::class,
                        Relation::LOAD => Relation::LOAD_EAGER,
                        Relation::SCHEMA => [
                            Relation::INNER_KEY => 'id',
                            Relation::OUTER_KEY => 'parentId',
                        ],
                    ]
                ],
            ],
            ProductBox::class => [
                SchemaInterface::ENTITY => ProductBox::class,
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'product_boxes',
                SchemaInterface::PRIMARY_KEY => ['id'],
                SchemaInterface::COLUMNS => [
                    'parentId' => 'parent_id',
                    'boxItemId' => 'box_item_id',
                    'count' => 'count',
                ],
                SchemaInterface::LISTENERS => [],
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                    'parentId' => 'int',
                    'boxItemId' => 'int',
                    'count' => 'int'
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [
                    'parent' => [
                        Relation::TYPE => Relation::REFERS_TO,
                        Relation::TARGET => Product::class,
                        Relation::SCHEMA => [
                            Relation::INNER_KEY => 'parentId',
                            Relation::OUTER_KEY => 'id',
                        ],
                    ],
                    'boxItem' => [
                        Relation::TYPE => Relation::REFERS_TO,
                        Relation::TARGET => Product::class,
                        Relation::SCHEMA => [
                            Relation::INNER_KEY => 'boxItemId',
                            Relation::OUTER_KEY => 'id',
                        ],
                    ]
                ],
            ]
        ]));
    }

    public function testUpdateWithSelectCollection(): void
    {
        $em = new EntityManager($this->orm);
        $repo = $this->orm->getRepository(Product::class);

        // Make 2 products
        $product1= new Product();
        $product1->name = 'test';
        $product1->revision = 1;

        $product2= new Product();
        $product2->name = 'test2';
        $product2->revision = 1;

        /// Add box item
        $product1->addBoxItem(new ProductBox(
            $product1,
            $product2
        ));

        // Persist 2 products
        $em->persist($product1)
            ->persist($product2)
            ->run();

        /// Persist 2 with Fetch all products
        $this->assertEquals(1, $product1->revision);
        $product1->name = '222';

        /// Persist 2
        $em->persist($product1)->run();
        $this->assertEquals(2, $product1->revision);

        /// Persist 3 with Fetch all products
        $product1->name = '333';

        $products = $repo->findAll();
        $em->persist($product1)->run();
        $this->assertEquals(3, $product1->revision);
    }
}
