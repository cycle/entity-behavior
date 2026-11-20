<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Unit\Schema;

use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use PHPUnit\Framework\TestCase;

final class RegistryModifierTest
{
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

    public static function bigIntegerDataProvider(): \Traversable
    {
        yield ['bigint'];
        yield ['bigInteger'];
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

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('integerDataProvider')]
    public function testIsIntegerTypeTrue(mixed $type): void
    {
        \Testo\Assert::true(RegistryModifier::isIntegerType($type));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('datetimeDataProvider')]
    #[\Testo\Data\DataProvider('stringDataProvider')]
    #[\Testo\Data\DataProvider('invalidDataProvider')]
    public function testIsIntegerTypeFalse(mixed $type): void
    {
        \Testo\Assert::false(RegistryModifier::isIntegerType($type));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('bigIntegerDataProvider')]
    public function testIsBigIntegerTypeTrue(mixed $type): void
    {
        \Testo\Assert::true(RegistryModifier::isBigIntegerType($type));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('datetimeDataProvider')]
    #[\Testo\Data\DataProvider('stringDataProvider')]
    #[\Testo\Data\DataProvider('invalidDataProvider')]
    public function testIsBigIntegerTypeFalse(mixed $type): void
    {
        \Testo\Assert::false(RegistryModifier::isBigIntegerType($type));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('datetimeDataProvider')]
    public function testIsDatetimeTypeTrue(mixed $type): void
    {
        \Testo\Assert::true(RegistryModifier::isDatetimeType($type));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('integerDataProvider')]
    #[\Testo\Data\DataProvider('stringDataProvider')]
    #[\Testo\Data\DataProvider('invalidDataProvider')]
    public function testIsDatetimeTypeFalse(mixed $type): void
    {
        \Testo\Assert::false(RegistryModifier::isDatetimeType($type));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('stringDataProvider')]
    public function testIsStringTypeTrue(mixed $type): void
    {
        \Testo\Assert::true(RegistryModifier::isStringType($type));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('integerDataProvider')]
    #[\Testo\Data\DataProvider('datetimeDataProvider')]
    #[\Testo\Data\DataProvider('invalidDataProvider')]
    public function testIsStringTypeFalse(mixed $type): void
    {
        \Testo\Assert::false(RegistryModifier::isStringType($type));
    }

    #[\Testo\Test]
    public function testIsSnowflakeTypeTrue(): void
    {
        \Testo\Assert::true(RegistryModifier::isSnowflakeType('snowflake'));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('integerDataProvider')]
    #[\Testo\Data\DataProvider('datetimeDataProvider')]
    #[\Testo\Data\DataProvider('invalidDataProvider')]
    #[\Testo\Data\DataProvider('stringDataProvider')]
    public function testIsSnowflakeTypeFalse(mixed $type): void
    {
        \Testo\Assert::false(RegistryModifier::isSnowflakeType($type));
    }

    #[\Testo\Test]
    public function testIsUlidTypeTrue(): void
    {
        \Testo\Assert::true(RegistryModifier::isUlidType('ulid'));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('integerDataProvider')]
    #[\Testo\Data\DataProvider('datetimeDataProvider')]
    #[\Testo\Data\DataProvider('invalidDataProvider')]
    #[\Testo\Data\DataProvider('stringDataProvider')]
    public function testIsUlidTypeFalse(mixed $type): void
    {
        \Testo\Assert::false(RegistryModifier::isUlidType($type));
    }

    #[\Testo\Test]
    public function testIsUuidTypeTrue(): void
    {
        \Testo\Assert::true(RegistryModifier::isUuidType('uuid'));
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('integerDataProvider')]
    #[\Testo\Data\DataProvider('datetimeDataProvider')]
    #[\Testo\Data\DataProvider('invalidDataProvider')]
    #[\Testo\Data\DataProvider('stringDataProvider')]
    public function testIsUuidTypeFalse(mixed $type): void
    {
        \Testo\Assert::false(RegistryModifier::isUuidType($type));
    }
}
