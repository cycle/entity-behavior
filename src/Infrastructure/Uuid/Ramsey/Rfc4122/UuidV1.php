<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Rfc4122;

use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Uuid;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\DateTimeException;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Throwable;

use const STR_PAD_LEFT;

/**
 * Time-based, or version 1, UUIDs include timestamp, clock sequence, and node
 * values that are combined into a 128-bit unsigned integer
 *
 * @psalm-immutable
 */
final class UuidV1 extends Uuid
{
    public function __construct(
        Rfc4122FieldsInterface $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter
    ) {
        if ($fields->getVersion() !== Uuid::UUID_TYPE_TIME) {
            throw new InvalidArgumentException(
                'Fields used to create a UuidV1 must represent a '
                . 'version 1 (time-based) UUID'
            );
        }

        parent::__construct($fields, $numberConverter, $codec, $timeConverter);
    }

    public function getDateTime(): \DateTimeInterface
    {
        $time = $this->timeConverter->convertTime($this->fields->getTimestamp());

        try {
            return new \DateTimeImmutable(
                '@'
                . $time->getSeconds()->toString()
                . '.'
                . \str_pad($time->getMicroseconds()->toString(), 6, '0', STR_PAD_LEFT)
            );
        } catch (Throwable $e) {
            throw new DateTimeException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
