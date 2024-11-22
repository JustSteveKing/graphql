<?php

declare(strict_types=1);

namespace JustSteveKing\GraphQL\Tests\Stubs;

use JustSteveKing\GraphQL\Attributes\Field;
use JustSteveKing\GraphQL\Attributes\Type;
use JustSteveKing\GraphQL\Exceptions\ValidationException;
use JustSteveKing\GraphQL\GraphObject;

#[Type(name: 'Company')]
final class Company extends GraphObject
{
    #[Field('id', 'String')]
    public string $id;

    #[Field('name', 'String')]
    public string $name;

    #[Field('employees', 'EmployeeDTO[]')]
    public array $employees;

    /** @throws ValidationException */
    public function validate(): void
    {
        parent::validate();

        foreach ($this->employees as $employee) {
            if (!$employee instanceof Employee) {
                throw new ValidationException("Each employee must be an instance of EmployeeDTO");
            }
            $employee->validate();
        }
    }
}
