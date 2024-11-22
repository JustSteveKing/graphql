<?php

declare(strict_types=1);

namespace JustSteveKing\GraphQL\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field
{
    public function __construct(
        public string $name,
        public string $type,
        public bool $nullable = false,
    ) {}
}
