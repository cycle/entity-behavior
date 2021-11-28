<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Rfc4122;

use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Uuid;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;

/**
 * Random, or version 4, UUIDs are randomly or pseudo-randomly generated 128-bit
 * integers
 *
 * @psalm-immutable
 */
final class UuidV4 extends Uuid
{
    /**
     * Creates a version 4 (random) UUID
     */
    public function __construct(
        Rfc4122FieldsInterface $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter
    ) {
        if ($fields->getVersion() !== Uuid::UUID_TYPE_RANDOM) {
            throw new InvalidArgumentException(
                'Fields used to create a UuidV4 must represent a '
                . 'version 4 (random) UUID'
            );
        }

        parent::__construct($fields, $numberConverter, $codec, $timeConverter);
    }
}
