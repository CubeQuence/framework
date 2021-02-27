<?php

declare(strict_types=1);

namespace CQ\Helpers;

use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid as UuidBase;

class UUID
{
    private static string $namespace = '4addcce9-7218-4fd4-97c8-28fd71b227dd';
    private static string $hex_namespace = '63756265';

    /**
     * Return V4 Random UUID.
     */
    public static function v4(): string
    {
        return UuidBase::uuid4()->toString();
    }

    /**
     * Return V5 Name-Based UUID.
     */
    public static function v5(string $name): string
    {
        return UuidBase::uuid5(
            ns: self::$namespace,
            name: $name
        )->toString();
    }

    /**
     * Return V6 Ordered-Time UUID.
     */
    public static function v6(): string
    {
        $node = new Hexadecimal(value: self::$hex_namespace);
        $nodeProvider = new StaticNodeProvider(node: $node);

        return UuidBase::uuid6(
            node: $nodeProvider->getNode()
        )->toString();
    }
}
