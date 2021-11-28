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
use Ramsey\Uuid\Type\Integer as IntegerObject;

use const STR_PAD_LEFT;

/**
 * DCE Security version, or version 2, UUIDs include local domain identifier,
 * local ID for the specified domain, and node values that are combined into a
 * 128-bit unsigned integer
 *
 * @link https://publications.opengroup.org/c311 DCE 1.1: Authentication and Security Services
 * @link https://publications.opengroup.org/c706 DCE 1.1: Remote Procedure Call
 * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap5.htm#tagcjh_08_02_01_01 DCE 1.1: Auth & Sec, §5.2.1.1
 * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap11.htm#tagcjh_14_05_01_01 DCE 1.1: Auth & Sec, §11.5.1.1
 * @link https://pubs.opengroup.org/onlinepubs/9629399/apdxa.htm DCE 1.1: RPC, Appendix A
 * @link https://github.com/google/uuid Go package for UUIDs (includes DCE implementation)
 *
 * @psalm-immutable
 */
final class UuidV2 extends Uuid
{
    /**
     * Creates a version 2 (DCE Security) UUID
     */
    public function __construct(
        Rfc4122FieldsInterface $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter
    ) {
        if ($fields->getVersion() !== Uuid::UUID_TYPE_DCE_SECURITY) {
            throw new InvalidArgumentException(
                'Fields used to create a UuidV2 must represent a '
                . 'version 2 (DCE Security) UUID'
            );
        }

        parent::__construct($fields, $numberConverter, $codec, $timeConverter);
    }

    /**
     * Returns a DateTimeInterface object representing the timestamp associated
     * with the UUID
     */
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
        } catch (\Throwable $e) {
            throw new DateTimeException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Returns the local domain used to create this version 2 UUID
     */
    public function getLocalDomain(): int
    {
        /** @var Rfc4122FieldsInterface $fields */
        $fields = $this->getFields();

        return (int) \hexdec($fields->getClockSeqLow()->toString());
    }

    /**
     * Returns the string name of the local domain
     */
    public function getLocalDomainName(): string
    {
        return Uuid::DCE_DOMAIN_NAMES[$this->getLocalDomain()];
    }

    /**
     * Returns the local identifier for the domain used to create this version 2 UUID
     */
    public function getLocalIdentifier(): IntegerObject
    {
        /** @var Rfc4122FieldsInterface $fields */
        $fields = $this->getFields();

        return new IntegerObject(
            $this->numberConverter->fromHex($fields->getTimeLow()->toString())
        );
    }
}
