<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\Schema;

use Cycle\Database\ColumnInterface;
use Cycle\ORM\Entity\Macros\Common\RegistryModifier;
use Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common\BaseTest;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Registry;

abstract class RegistryModifierTest extends BaseTest
{
    private const ROLE_TEST = 'test';

    protected Registry $registry;
    protected RegistryModifier $modifier;

    public function setUp(): void
    {
        parent::setUp();

        $this->registry = new Registry($this->dbal);

        $entity = (new Entity())->setRole(self::ROLE_TEST);
        $this->registry->register($entity);
        $this->registry->linkTable($entity, 'default', 'tests');

        $this->modifier = new RegistryModifier($this->registry, self::ROLE_TEST);
    }

    public function testAddDatetimeField(): void
    {
        $this->modifier->addDatetimeColumn('created_at', 'createdAt');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('createdAt'));
        $this->assertSame('datetime', $fields->get('createdAt')->getType());
        $this->assertSame('created_at', $fields->get('createdAt')->getColumn());
    }

    public function testAddStringField(): void
    {
        $this->modifier->addStringColumn('version_str', 'version');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('version'));
        $this->assertSame(ColumnInterface::STRING, $fields->get('version')->getType());
        $this->assertSame('version_str', $fields->get('version')->getColumn());
    }

    public function testAddIntegerField(): void
    {
        $this->modifier->addIntegerColumn('version_int', 'version');

        $entity = $this->registry->getEntity(self::ROLE_TEST);
        $fields = $entity->getFields();

        $this->assertTrue($fields->has('version'));
        $this->assertSame(ColumnInterface::INT, $fields->get('version')->getType());
        $this->assertSame('version_int', $fields->get('version')->getColumn());
    }
}
