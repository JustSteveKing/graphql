<?php

declare(strict_types=1);

namespace JustSteveKing\GraphQL;

use InvalidArgumentException;
use JustSteveKing\GraphQL\Attributes\Field;
use JustSteveKing\GraphQL\Exceptions\ValidationException;
use ReflectionClass;

abstract class GraphObject
{
    final public function __construct() {}

    /**
     * @return array<string, string>
     */
    public static function getFields(): array
    {
        $fields = [];
        $reflection = new ReflectionClass(static::class);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Field::class);
            if (empty($attributes)) {
                continue;
            }

            $field = $attributes[0]->newInstance();
            $fields[$field->name] = $field->type;
        }

        return $fields;
    }

    /**
     * @param  array<string,mixed>|null  $arguments
     */
    public static function buildQuery(string $operationName, ?array $arguments = null): string
    {
        $fields = static::getFields();
        $queryFields = self::buildQueryFields($fields);

        $args = '';
        if ($arguments) {
            $args = '(' . implode(
                ', ',
                array_map(
                    static fn($key, $value) => $key . ': ' . static::mcixedToString(value: $value),
                    array_keys($arguments),
                    $arguments,
                ),
            ) . ')';
        }

        return "query {$operationName}{$args} {\n  {$operationName} {\n{$queryFields}\n  }\n}\n";
    }

    /**
     * @param  array<string,mixed>|null  $arguments
     */
    public static function buildMutation(string $operationName, ?array $arguments = null): string
    {
        $fields = static::getFields();
        $mutationFields = self::buildQueryFields($fields);

        $args = '';
        $inputFields = '';
        if ($arguments) {
            $args = '(' . implode(
                ', ',
                array_map(
                    static fn($key, $value) => $key . ': ' . static::mixedToString(value: $value),
                    array_keys($arguments),
                    $arguments,
                ),
            ) . ')';
            $inputFields = implode(
                ', ',
                array_map(
                    static fn($key) => "{$key}: \${$key}",
                    array_keys($arguments),
                ),
            );
        }

        return "mutation {$operationName}{$args} {\n  {$operationName}({$inputFields}) {\n{$mutationFields}\n  }\n}\n";
    }

    /**
     * @param  array<string,mixed>  $data
     *
     * @throws ValidationException
     */
    public static function fromArray(array $data): self
    {
        $dto = new static();
        $reflection = new ReflectionClass($dto);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Field::class);
            if (empty($attributes)) {
                continue;
            }

            $field = $attributes[0]->newInstance();
            if (array_key_exists($field->name, $data)) {
                $property->setValue($dto, $data[$field->name]);
            }
        }

        $dto->validate();

        return $dto;
    }

    /**
     * @throws ValidationException
     */
    public function validate(): void
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Field::class);

            if (empty($attributes)) {
                continue;
            }

            $field = $attributes[0]->newInstance();
            $value = $property->getValue($this);

            if (null === $value && ! $field->nullable) {
                throw new ValidationException(
                    message: "Field {$field->name} cannot be null",
                );
            }

            if (null !== $value) {
                $this->validateType($value, $field->type, $field->name);
            }
        }
    }

    /**
     * @param  array<string,string>  $fields
     */
    protected static function buildQueryFields(array $fields, int $depth = 2): string
    {
        $queryFields = '';
        $indent = str_repeat('  ', $depth);

        foreach ($fields as $name => $type) {
            $subType = str_ends_with($type, '[]') ? mb_substr($type, 0, -2) : $type;
            if (is_subclass_of($subType, static::class)) {
                $subFields = $subType::getFields();
                $queryFields .= "{$indent}{$name} {\n" . static::buildQueryFields(
                    $subFields,
                    $depth + 1,
                ) . "{$indent}}\n";
            } else {
                $queryFields .= "{$indent}{$name}\n";
            }
        }

        return $queryFields;
    }

    protected static function mixedToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        /** Numeric values can be safely cast to string */
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        /** Explicit handling of booleans */
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (null === $value) {
            return ''; // Explicit handling of null
        }

        /** Use __toString() if the object supports it */
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        throw new InvalidArgumentException(
            message: 'Cannot convert value to string',
        );
    }

    /**
     * @throws ValidationException
     */
    private function validateType(mixed $value, string $type, string $name): void
    {
        $expectedType = match ($type) {
            'String' => 'a string',
            'Int' => 'an integer',
            'Float' => 'a float',
            'Boolean' => 'a boolean',
            default => "an instance of {$type}",
        };

        if ( ! is_string($value) && ! is_int($value) && ! is_float($value) && ! is_bool(
            $value,
        ) && ! ($value instanceof $type)) {
            throw new ValidationException("Field {$name} must be {$expectedType}");
        }
    }
}
