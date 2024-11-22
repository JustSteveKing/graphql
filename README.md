# GraphQL Builder

Build GraphQL Queries and Mutations in a safe and easy way.


## Installation

You can install this package using composer:

```shell
composer require juststeveking/graphql
```

## Building Queries

To build a GraphQL query using this package, you need to create a class that extends `GraphObject` and define the fields using the `@Field` attribute. Here's an example:

```php
use JustSteveKing\GraphQL\Attributes\Field;
use JustSteveKing\GraphQL\GraphObject;

class Company extends GraphObject
{
    #[Field(type: 'String')]
    private string $name;

    #[Field(type: 'Int')]
    private int $id;

    // Getters and setters for the properties
}
```

Once you have your `GraphObject` class set up, you can use the `Builder` class to build your GraphQL query. Here's an example:

```php
use JustSteveKing\GraphQL\Builder;

$queryBuilder = Builder::query(
    Company::class,
    operationName: 'getCompany',
    arguments: ['id' => 'ID!'],
);

echo $queryBuilder->getQuery();
```

This will output the following GraphQL query:

```graphql
query getCompany($id: ID!) {
  getCompany {
    name
    id
  }
}
```

## Building Mutations

To build a GraphQL mutation using this package, you need to create a class that extends `GraphObject` and define the fields using the `@Field` attribute. Here's an example for creating an employee:

```php
use JustSteveKing\GraphQL\Attributes\Field;
use JustSteveKing\GraphQL\GraphObject;

class Employee extends GraphObject
{
    #[Field(type: 'String')]
    private string $name;

    #[Field(type: 'String')]
    private string $position;

    #[Field(type: 'Float')]
    private float $salary;

    // Getters and setters for the properties
}
```

Once you have your `GraphObject` class set up for the mutation, you can use the `Builder` class to build your GraphQL mutation. Here's an example:

```php
use JustSteveKing\GraphQL\Builder;

$mutationBuilder = Builder::mutate(
    Employee::class,
    operationName: 'createEmployee',
    arguments: ['name' => 'String!', 'position' => 'String!', 'salary' => 'Float!'],
);

echo $mutationBuilder->getQuery();
```

This will output the following GraphQL mutation:

```graphql
mutation createEmployee($name: String!, $position: String!, $salary: Float!) {
  createEmployee {
    name
    position
    salary
  }
}
```

## Additional Tips and Considerations

* Make sure to validate your `GraphObject` instances before building queries or mutations to ensure all required fields are present and of the correct type.
* Use the `validate()` method on your `GraphObject` instances to validate the data before building queries or mutations.
* You can nest `GraphObject` instances to build more complex queries or mutations.
* This package supports GraphQL fragments, interfaces, and unions, but these features are not explicitly demonstrated in this README. For more information, refer to the package's documentation or source code.

## Testing

This package has a full test suite using PHPUnit, you can run the test suite here:

```shell
composer test
```