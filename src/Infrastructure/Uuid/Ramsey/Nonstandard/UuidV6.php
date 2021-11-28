<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Nonstandard;

use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Uuid;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;

/**
 * Version 5 UUIDs are named-based, using combination of a namespace and name
 * that are hashed into a 128-bit unsigned integer using SHA1
 *
 * @psalm-immutable
 */
final class UuidV6 extends Uuid
{
    /**
     * Creates a version 5 (name-based, SHA1-hashed) UUID
     */
    public function __construct(
        Rfc4122FieldsInterface $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter
    ) {
        if ($fields->getVersion() !== Uuid::UUID_TYPE_HASH_SHA1) {
            throw new InvalidArgumentException(
                'Fields used to create a UuidV5 must represent a '
                . 'version 5 (named-based, SHA1-hashed) UUID'
            );
        }

        parent::__construct($fields, $numberConverter, $codec, $timeConverter);
    }
}
