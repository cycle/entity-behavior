<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Rfc4122;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Rfc4122\UuidBuilder as RamseyUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\UuidInterface;
use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Nonstandard\UuidV6;

/**
 * @psalm-immutable
 */
class UuidBuilder extends RamseyUuidBuilder
{
    public function __construct(
        private NumberConverterInterface $numberConverter,
        private TimeConverterInterface $timeConverter
    ) {
        parent::__construct($numberConverter, $timeConverter);
    }

    /** @psalm-suppress InvalidReturnType */
    public function build(CodecInterface $codec, string $bytes): UuidInterface
    {
        try {
            $fields = $this->buildFields($bytes);

            /** @psalm-suppress InvalidReturnStatement */
            if ($fields->isNil()) {
                return new NilUuid($fields, $this->numberConverter, $codec, $this->timeConverter);
            }

            /** @psalm-suppress InvalidReturnStatement */
            return match ($fields->getVersion()) {
                1 => new UuidV1($fields, $this->numberConverter, $codec, $this->timeConverter),
                2 => new UuidV2($fields, $this->numberConverter, $codec, $this->timeConverter),
                3 => new UuidV3($fields, $this->numberConverter, $codec, $this->timeConverter),
                4 => new UuidV4($fields, $this->numberConverter, $codec, $this->timeConverter),
                5 => new UuidV5($fields, $this->numberConverter, $codec, $this->timeConverter),
                6 => new UuidV6($fields, $this->numberConverter, $codec, $this->timeConverter),
                default => throw new UnsupportedOperationException(
                    'The UUID version in the given fields is not supported '
                    . 'by this UUID builder'
                )
            };
        } catch (\Throwable $e) {
            throw new UnableToBuildUuidException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
