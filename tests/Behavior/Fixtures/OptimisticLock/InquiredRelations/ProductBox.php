<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\InquiredRelations;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\ORM\Entity\Behavior\OptimisticLock;
use Modules\Catalog\Domain\Entity\Element;

#[Entity]
class ProductBox
{
    #[Column(type: 'primary')]
    public int $id;

    #[BelongsTo(
        target: Product::class,
        innerKey: 'parentId',
        outerKey: 'id'
    )]
    public Product $parent;

    public int $parentId;

    #[BelongsTo(
        target: Product::class,
        innerKey: 'boxItemId',
        outerKey: 'id'
    )]
    public Product $boxItem;

    public int $boxItemId;

    public int $count;

    public function __construct(
         Product $parent,
         Product $boxItem,
    ) {
        $this->parent = $parent;
        $this->boxItem = $boxItem;
        $this->count = 9999;
    }
}
