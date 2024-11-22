<?php

declare(strict_types=1);

namespace JustSteveKing\GraphQL\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Type
{
    public function __construct(public string $name) {}
}
