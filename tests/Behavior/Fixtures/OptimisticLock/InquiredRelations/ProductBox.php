<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\InquiredRelations;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\ORM\Entity\Behavior\OptimisticLock;
use Modules\Catalog\Domain\Entity\Element;

#[Entity(table: 'product_boxes')]
class ProductBox
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'integer', name: 'parent_id')]
    public int $parentId;

    #[Column(type: 'integer', name: 'box_item_id')]
    public int $boxItemId;

    #[Column(type: 'integer')]
    public int $count;

    #[BelongsTo(
        target: Product::class,
        innerKey: 'parentId',
        outerKey: 'id',
        fkCreate: false,
    )]
    public Product $parent;

    #[BelongsTo(
        target: Product::class,
        innerKey: 'boxItemId',
        outerKey: 'id'
    )]
    public Product $boxItem;

    public function __construct(
         Product $parent,
         Product $boxItem,
    ) {
        $this->parent = $parent;
        $this->boxItem = $boxItem;
        $this->count = 9999;
    }
}
