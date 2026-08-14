<?php

namespace App\Security;

use DateTimeImmutable;

/**
 * A readonly DTO to hold secure token data.
 * Being readonly ensures the validator cannot be changed once generated.
 */
readonly class SecurityToken 
{
    public function __construct(
        public string $selector,
        public string $validator,
        public DateTimeImmutable $expiresAt
    ) {}
}