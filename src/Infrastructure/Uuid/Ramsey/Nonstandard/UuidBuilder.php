<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Nonstandard;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Nonstandard\UuidBuilder as RamseyUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\UuidInterface;

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

    /**
     * Builds and returns a Nonstandard\Uuid
     *
     * @psalm-suppress ImplementedReturnTypeMismatch, InvalidReturnType, InvalidReturnStatement
     */
    public function build(CodecInterface $codec, string $bytes): UuidInterface
    {
        try {
            return new Uuid(
                $this->buildFields($bytes),
                $this->numberConverter,
                $codec,
                $this->timeConverter
            );
        } catch (\Throwable $e) {
            throw new UnableToBuildUuidException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
