<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Uuid;

interface UuidFactoryInterface
{
    /**
     * A v1 UUID contains a 60-bit timestamp and 62 extra unique bits.
     */
    public function v1(mixed $node = null, ?int $clockSeq = null): UuidInterface;

    /**
     * Returns a version 2 (DCE Security) UUID from a local domain, local
     * identifier, host ID, clock sequence, and the current time
     */
    public function v2(
        int $localDomain,
        mixed $localIdentifier = null,
        mixed $node = null,
        ?int $clockSeq = null
    ): UuidInterface;

    /**
     * Returns a version 3 (name-based) UUID based on the MD5 hash of a
     * namespace ID and a name
     */
    public function v3(mixed $namespace, string $name): UuidInterface;

    /**
     * Returns a version 4 (random) UUID
     */
    public function v4(): UuidInterface;

    /**
     * Returns a version 5 (name-based) UUID based on the SHA-1 hash of a
     * namespace ID and a name
     */
    public function v5(mixed $namespace, string $name): UuidInterface;

    /**
     * Returns a version 6 (ordered-time) UUID from a host ID, sequence number,
     * and the current time
     */
    public function v6(mixed $node = null, ?int $clockSeq = null): UuidInterface;
}
