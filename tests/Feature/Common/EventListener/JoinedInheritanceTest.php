<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Feature\Common\EventListener;

use Cycle\ORM\Entity\Behavior\Listener\CreatedAt;
use Cycle\ORM\Entity\Behavior\Listener\UpdatedAt;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\JoinedInheritance\Document;
use Cycle\ORM\Entity\Behavior\Tests\Fixtures\JoinedInheritance\Invoice;
use Cycle\ORM\Entity\Behavior\Tests\Support\ListenerTestCase;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Testo\Assert;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class JoinedInheritanceTest extends ListenerTestCase
{
    #[Test]
    public function parentListenersFillParentColumnsOnCreate(): void
    {
        $invoice = new Invoice();
        $invoice->title = 'first';
        $invoice->amount = 10;

        $this->save($invoice);

        $row = $this->getDatabase()->select()->from('documents')->fetchAll()[0];
        Assert::same($row['title'], 'first');
        Assert::notNull($row['created_at']);
        Assert::notNull($row['updated_at']);
        Assert::same((int) $this->getDatabase()->select()->from('invoices')->fetchAll()[0]['amount'], 10);
    }

    #[Test]
    public function updateWritesParentColumns(): void
    {
        $invoice = new Invoice();
        $invoice->title = 'first';
        $this->save($invoice);

        $this->orm->getHeap()->clean();
        $invoice = (new Select($this->orm, Invoice::class))->fetchOne();
        $invoice->title = 'second';
        $invoice->amount = 20;
        $this->save($invoice);

        $row = $this->getDatabase()->select()->from('documents')->fetchAll()[0];
        Assert::same($row['title'], 'second');
        Assert::same((int) $this->getDatabase()->select()->from('invoices')->fetchAll()[0]['amount'], 20);
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $this->makeTable('documents', [
            'id' => 'primary',
            'title' => 'string',
            'created_at' => 'datetime,nullable',
            'updated_at' => 'datetime,nullable',
        ]);
        $this->makeTable('invoices', ['id' => 'integer', 'amount' => 'integer'], pk: ['id']);

        $this->withSchema(new Schema([
            Document::class => [
                SchemaInterface::ROLE => 'document',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'documents',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => [
                    'id' => 'id',
                    'title' => 'title',
                    'createdAt' => 'created_at',
                    'updatedAt' => 'updated_at',
                ],
                SchemaInterface::LISTENERS => [CreatedAt::class, UpdatedAt::class],
                SchemaInterface::TYPECAST => ['id' => 'int', 'createdAt' => 'datetime', 'updatedAt' => 'datetime'],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
            Invoice::class => [
                SchemaInterface::ROLE => 'invoice',
                SchemaInterface::PARENT => 'document',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'invoices',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => ['id' => 'id', 'amount' => 'amount'],
                SchemaInterface::TYPECAST => ['id' => 'int', 'amount' => 'int'],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]));
    }
}
