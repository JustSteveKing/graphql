<?php

declare(strict_types=1);

namespace JustSteveKing\GraphQL\Tests;

use JustSteveKing\GraphQL\Builder;
use JustSteveKing\GraphQL\Tests\Stubs\Company;
use JustSteveKing\GraphQL\Tests\Stubs\Employee;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    #[Test]
    public function query_generation(): void
    {
        $builder = Builder::query(Company::class, 'getCompany', ['id' => 'ID!']);
        $query = $builder->getQuery();
        $this->assertNotEmpty($query);
        $this->assertStringContainsString('query', $query);
        $this->assertStringContainsString('getCompany', $query);
        $this->assertStringContainsString('id: ID!', $query);
    }

    #[Test]
    public function mutation_generation(): void
    {
        $builder = Builder::mutate(Employee::class, 'createEmployee', ['name' => 'String!', 'position' => 'String!']);
        $mutation = $builder->getQuery();
        $this->assertNotEmpty($mutation);
        $this->assertStringContainsString('mutation', $mutation);
        $this->assertStringContainsString('createEmployee', $mutation);
        $this->assertStringContainsString('name: String!', $mutation);
        $this->assertStringContainsString('position: String!', $mutation);
    }

    #[Test]
    public function query_without_arguments(): void
    {
        $builder = Builder::query(Company::class, 'getCompany');
        $query = $builder->getQuery();
        $this->assertNotEmpty($query);
        $this->assertStringContainsString('query', $query);
        $this->assertStringContainsString('getCompany', $query);
        $this->assertStringNotContainsString('(', $query);
    }

    #[Test]
    public function mutation_without_arguments(): void
    {
        $builder = Builder::mutate(Employee::class, 'createEmployee');
        $mutation = $builder->getQuery();
        $this->assertNotEmpty($mutation);
        $this->assertStringContainsString('mutation', $mutation);
        $this->assertStringContainsString('createEmployee', $mutation);
    }

    #[Test]
    public function query_with_multiple_arguments(): void
    {
        $builder = Builder::query(Company::class, 'getCompany', ['id' => 'ID!', 'name' => 'String!']);
        $query = $builder->getQuery();
        $this->assertNotEmpty($query);
        $this->assertStringContainsString('query', $query);
        $this->assertStringContainsString('getCompany', $query);
        $this->assertStringContainsString('id: ID!', $query);
        $this->assertStringContainsString('name: String!', $query);
    }

    #[Test]
    public function mutation_with_multiple_arguments(): void
    {
        $builder = Builder::mutate(
            Employee::class,
            'createEmployee',
            ['name' => 'String!', 'position' => 'String!', 'salary' => 'Float!'],
        );
        $mutation = $builder->getQuery();
        $this->assertNotEmpty($mutation);
        $this->assertStringContainsString('mutation', $mutation);
        $this->assertStringContainsString('createEmployee', $mutation);
        $this->assertStringContainsString('name: String!', $mutation);
        $this->assertStringContainsString('position: String!', $mutation);
        $this->assertStringContainsString('salary: Float!', $mutation);
    }
}
