<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Unit\Schema;

use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use PHPUnit\Framework\TestCase;

final class RegistryModifierTest extends TestCase
{
    /**
     * @dataProvider integerDataProvider
     */
    public function testIsIntegerTypeTrue(mixed $type): void
    {
        $this->assertTrue(RegistryModifier::isIntegerType($type));
    }

    /**
     * @dataProvider datetimeDataProvider
     * @dataProvider stringDataProvider
     * @dataProvider invalidDataProvider
     */
    public function testIsIntegerTypeFalse(mixed $type): void
    {
        $this->assertFalse(RegistryModifier::isIntegerType($type));
    }

    /**
     * @dataProvider datetimeDataProvider
     */
    public function testIsDatetimeTypeTrue(mixed $type): void
    {
        $this->assertTrue(RegistryModifier::isDatetimeType($type));
    }

    /**
     * @dataProvider integerDataProvider
     * @dataProvider stringDataProvider
     * @dataProvider invalidDataProvider
     */
    public function testIsDatetimeTypeFalse(mixed $type): void
    {
        $this->assertFalse(RegistryModifier::isDatetimeType($type));
    }

    /**
     * @dataProvider stringDataProvider
     */
    public function testIsStringTypeTrue(mixed $type): void
    {
        $this->assertTrue(RegistryModifier::isStringType($type));
    }

    /**
     * @dataProvider integerDataProvider
     * @dataProvider datetimeDataProvider
     * @dataProvider invalidDataProvider
     */
    public function testIsStringTypeFalse(mixed $type): void
    {
        $this->assertFalse(RegistryModifier::isStringType($type));
    }

    public function testIsUuidTypeTrue(): void
    {
        $this->assertTrue(RegistryModifier::isUuidType('uuid'));
    }

    /**
     * @dataProvider integerDataProvider
     * @dataProvider datetimeDataProvider
     * @dataProvider invalidDataProvider
     * @dataProvider stringDataProvider
     */
    public function testIsUuidTypeFalse(mixed $type): void
    {
        $this->assertFalse(RegistryModifier::isUuidType($type));
    }

    public static function integerDataProvider(): \Traversable
    {
        yield ['int'];
        yield ['smallint'];
        yield ['tinyint'];
        yield ['bigint'];
        yield ['integer'];
        yield ['tinyInteger'];
        yield ['smallInteger'];
        yield ['bigInteger'];
        yield ['integer(4)'];
    }

    public static function datetimeDataProvider(): \Traversable
    {
        yield ['datetime'];
        yield ['datetime2'];
        yield ['datetime2(7)'];
    }

    public static function stringDataProvider(): \Traversable
    {
        yield ['string'];
        yield ['string(32)'];
    }

    public static function invalidDataProvider(): \Traversable
    {
        yield ['text'];
        yield ['json'];
        yield ['foo'];
        yield ['bar(32)'];
    }
}
