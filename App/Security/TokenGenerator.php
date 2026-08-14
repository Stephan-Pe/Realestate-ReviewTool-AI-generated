<?php


namespace App\Security;

use Random\Randomizer;
// Import your custom class
use App\Security\SecurityToken;

class TokenGenerator
{
    /**
     * Generates a secure token for "remember me" functionality.
     * Returns a SecurityToken DTO containing the selector, validator, and expiration time.
     * For future usage
     */
    public static function generate(int $length = 32): SecurityToken
    {
        $randomizer = new Randomizer();

        $selector  = bin2hex($randomizer->getBytes(8));
        $validator = bin2hex($randomizer->getBytes($length));

        return new SecurityToken(
            $selector,
            $validator,
            new \DateTimeImmutable('+30 days')
        );
    }
    /** Generates a random string of the specified length.
     * This can be used for various purposes, such as generating unique identifiers or tokens.
     */
    public static function generateRandomString(int $length = 32): string
    {
        $randomizer = new Randomizer();
        return bin2hex($randomizer->getBytes($length));
    }
}
