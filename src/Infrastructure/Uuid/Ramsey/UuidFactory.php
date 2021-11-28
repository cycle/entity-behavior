<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey;

use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Rfc4122\UuidBuilder as Rfc4122Builder;
use Cycle\ORM\Entity\Macros\Infrastructure\Uuid\Ramsey\Nonstandard\UuidBuilder as NonstandardBuilder;
use Cycle\ORM\Entity\Macros\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\Builder\BuilderCollection;
use Ramsey\Uuid\Builder\FallbackBuilder;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\UuidFactory as RamseyUuidFactory;

class UuidFactory extends RamseyUuidFactory implements UuidFactoryInterface
{
    public function __construct(?FeatureSet $features = null)
    {
        $features = $features ?: new FeatureSet();
        $builder = $this->buildUuidBuilder($features);

        parent::__construct($features);

        $this->setUuidBuilder($builder);
        $this->setCodec($this->buildCodec($builder));
    }

    /** @psalm-suppress MoreSpecificReturnType, LessSpecificReturnStatement */
    public function v1(mixed $node = null, ?int $clockSeq = null): UuidInterface
    {
        return $this->uuid1($node, $clockSeq);
    }

    /** @psalm-suppress MoreSpecificReturnType, LessSpecificReturnStatement */
    public function v2(
        int $localDomain,
        mixed $localIdentifier = null,
        mixed $node = null,
        ?int $clockSeq = null
    ): UuidInterface {
        return $this->uuid2($localDomain, $localIdentifier, $node, $clockSeq);
    }

    /** @psalm-suppress MoreSpecificReturnType, LessSpecificReturnStatement */
    public function v3(mixed $namespace, string $name): UuidInterface
    {
        return $this->uuid3($namespace, $name);
    }

    /** @psalm-suppress MoreSpecificReturnType, LessSpecificReturnStatement */
    public function v4(): UuidInterface
    {
        return $this->uuid4();
    }

    /** @psalm-suppress MoreSpecificReturnType, LessSpecificReturnStatement */
    public function v5(mixed $namespace, string $name): UuidInterface
    {
        return $this->uuid5($namespace, $name);
    }

    /** @psalm-suppress MoreSpecificReturnType, LessSpecificReturnStatement */
    public function v6(mixed $node = null, ?int $clockSeq = null): UuidInterface
    {
        return $this->uuid6($node, $clockSeq);
    }

    private function buildUuidBuilder(FeatureSet $featureSet): UuidBuilderInterface
    {
        /** @psalm-suppress ImpureArgument */
        return new FallbackBuilder(new BuilderCollection([
            new Rfc4122Builder($featureSet->getNumberConverter(), $featureSet->getTimeConverter()),
            new NonstandardBuilder($featureSet->getNumberConverter(), $featureSet->getTimeConverter()),
        ]));
    }

    private function buildCodec(UuidBuilderInterface $builder): CodecInterface
    {
        return new StringCodec($builder);
    }
}
