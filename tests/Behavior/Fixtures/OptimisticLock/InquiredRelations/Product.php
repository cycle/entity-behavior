<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Fixtures\OptimisticLock\InquiredRelations;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\ORM\Entity\Behavior\OptimisticLock;

#[Entity(table: 'products')]
#[OptimisticLock(field: 'revision', rule: OptimisticLock::RULE_INCREMENT)]
class Product
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string', nullable: true)]
    public ?string $name = null;

    #[Column(type: 'integer', name: 'parent_id', nullable: true)]
    public ?int $parentId = null;

    #[HasMany(
        target: ProductBox::class,
        innerKey: 'id',
        outerKey: 'parentId',
    )]
    public array $boxItems = [];

    #[Column(type: 'integer', name: 'revision')]
    public int $revision;

    public function addBoxItem(ProductBox $boxItem): void
    {
        $this->boxItems[] = $boxItem;
    }

    public function getBoxItems(): array
    {
        return $this->boxItems;
    }

    public function getBoxItemById(int $id): ?ProductBox
    {
        foreach ($this->boxItems as $boxItem) {
            if ($boxItem->id === $id) {
                return $boxItem;
            }
        }

        return null;
    }
}
