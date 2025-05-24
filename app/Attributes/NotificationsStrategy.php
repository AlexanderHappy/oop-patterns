<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class NotificationsStrategy
{
    public function __construct(
        public string $method,
    ) {}
}
