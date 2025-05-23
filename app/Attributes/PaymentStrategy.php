<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class PaymentStrategy
{
    public function __construct(
        public string $method
    ) {}
}
