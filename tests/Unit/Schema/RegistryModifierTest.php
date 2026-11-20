<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Unit\Schema;

use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

#[Test]
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

    #[DataProvider('integerDataProvider')]
    public function testIsIntegerTypeTrue(mixed $type): void
    {
        Assert::true(RegistryModifier::isIntegerType($type));
    }

    #[DataProvider('datetimeDataProvider')]
    #[DataProvider('stringDataProvider')]
    #[DataProvider('invalidDataProvider')]
    public function testIsIntegerTypeFalse(mixed $type): void
    {
        Assert::false(RegistryModifier::isIntegerType($type));
    }

    #[DataProvider('bigIntegerDataProvider')]
    public function testIsBigIntegerTypeTrue(mixed $type): void
    {
        Assert::true(RegistryModifier::isBigIntegerType($type));
    }

    #[DataProvider('datetimeDataProvider')]
    #[DataProvider('stringDataProvider')]
    #[DataProvider('invalidDataProvider')]
    public function testIsBigIntegerTypeFalse(mixed $type): void
    {
        Assert::false(RegistryModifier::isBigIntegerType($type));
    }

    #[DataProvider('datetimeDataProvider')]
    public function testIsDatetimeTypeTrue(mixed $type): void
    {
        Assert::true(RegistryModifier::isDatetimeType($type));
    }

    #[DataProvider('integerDataProvider')]
    #[DataProvider('stringDataProvider')]
    #[DataProvider('invalidDataProvider')]
    public function testIsDatetimeTypeFalse(mixed $type): void
    {
        Assert::false(RegistryModifier::isDatetimeType($type));
    }

    #[DataProvider('stringDataProvider')]
    public function testIsStringTypeTrue(mixed $type): void
    {
        Assert::true(RegistryModifier::isStringType($type));
    }

    #[DataProvider('integerDataProvider')]
    #[DataProvider('datetimeDataProvider')]
    #[DataProvider('invalidDataProvider')]
    public function testIsStringTypeFalse(mixed $type): void
    {
        Assert::false(RegistryModifier::isStringType($type));
    }

    public function testIsSnowflakeTypeTrue(): void
    {
        Assert::true(RegistryModifier::isSnowflakeType('snowflake'));
    }

    #[DataProvider('integerDataProvider')]
    #[DataProvider('datetimeDataProvider')]
    #[DataProvider('invalidDataProvider')]
    #[DataProvider('stringDataProvider')]
    public function testIsSnowflakeTypeFalse(mixed $type): void
    {
        Assert::false(RegistryModifier::isSnowflakeType($type));
    }

    public function testIsUlidTypeTrue(): void
    {
        Assert::true(RegistryModifier::isUlidType('ulid'));
    }

    #[DataProvider('integerDataProvider')]
    #[DataProvider('datetimeDataProvider')]
    #[DataProvider('invalidDataProvider')]
    #[DataProvider('stringDataProvider')]
    public function testIsUlidTypeFalse(mixed $type): void
    {
        Assert::false(RegistryModifier::isUlidType($type));
    }

    public function testIsUuidTypeTrue(): void
    {
        Assert::true(RegistryModifier::isUuidType('uuid'));
    }

    #[DataProvider('integerDataProvider')]
    #[DataProvider('datetimeDataProvider')]
    #[DataProvider('invalidDataProvider')]
    #[DataProvider('stringDataProvider')]
    public function testIsUuidTypeFalse(mixed $type): void
    {
        Assert::false(RegistryModifier::isUuidType($type));
    }
}
