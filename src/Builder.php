<?php

declare(strict_types=1);

namespace JustSteveKing\GraphQL;

final class Builder
{
    private string $query;

    /**
     * @param class-string<GraphObject> $className
     * @param array<string,mixed>|null $arguments
     */
    public function __construct(
        string $className,
        string $operationName,
        ?array $arguments = null,
        bool $mutate = false,
    ) {
        $this->query = $mutate
            ? $className::buildMutation($operationName, $arguments)
            : $className::buildQuery($operationName, $arguments);
    }

    /**
     * @param class-string<GraphObject> $className
     * @param array<string,mixed>|null $arguments
     */
    public static function query(
        string $className,
        string $operationName,
        ?array $arguments = null,
    ): Builder {
        return new Builder(
            className: $className,
            operationName: $operationName,
            arguments: $arguments,
            mutate: false,
        );
    }

    /**
     * @param class-string<GraphObject> $className
     * @param array<string,mixed>|null $arguments
     */
    public static function mutate(
        string $className,
        string $operationName,
        ?array $arguments = null,
    ): Builder {
        return new Builder(
            className: $className,
            operationName: $operationName,
            arguments: $arguments,
            mutate: true,
        );
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
