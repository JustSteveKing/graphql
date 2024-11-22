<?php

declare(strict_types=1);

namespace JustSteveKing\GraphQL\Tests;

use JustSteveKing\GraphQL\Tests\Stubs\Employee;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TypeError;

class GraphObjectTest extends TestCase
{
    #[Test]
    public function from_array_creates_object_successfully(): void
    {
        $obj = Employee::fromArray([
            'id' => '123',
            'name' => 'John Doe',
            'position' => 'Milk man',
            'salary' => 100_000,
        ]);

        $this->assertEquals('John Doe', $obj->name);
        $this->assertEquals(100_000, $obj->salary);
    }

    #[Test]
    public function type_error_is_thrown_for_null_field(): void
    {
        $this->expectException(TypeError::class);

        Employee::fromArray([
            'id' => '123',
            'name' => null,
            'position' => 'Milk man',
            'salary' => 100_000,
        ]);
    }

    #[Test]
    public function type_error_is_thrown_for_invalid_type(): void
    {
        $this->expectException(TypeError::class);

        Employee::fromArray([
            'id' => '123',
            'name' => 'John Doe',
            'position' => 'Milk man',
            'salary' => '100_000',
        ]);
    }

    #[Test]
    public function get_fields_method_returns_correct_fields(): void
    {
        $fields = Employee::getFields();

        $this->assertArrayHasKey('id', $fields);
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('position', $fields);
        $this->assertArrayHasKey('salary', $fields);
    }
}
