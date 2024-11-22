<?php

declare(strict_types=1);

namespace JustSteveKing\GraphQL\Tests\Stubs;

use JustSteveKing\GraphQL\Attributes\Field;
use JustSteveKing\GraphQL\Attributes\Type;
use JustSteveKing\GraphQL\GraphObject;

#[Type(name: 'Employee')]
final class Employee extends GraphObject
{
    #[Field('id', 'String')]
    public string $id;

    #[Field('name', 'String')]
    public string $name;

    #[Field('position', 'String', true)]
    public ?string $position;

    #[Field('salary', 'Float')]
    public float $salary;
}
