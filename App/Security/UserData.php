<?php

namespace App\Security;

/**
 * A simple DTO to hold user data for authentication and authorization purposes.
 * This class is not meant to be persisted or contain any business logic.
 * It serves as a structured way to pass user information around the application.
 */

readonly class UserData
{
    public function __construct(
        public ?int $id = null,
        public string $user_name = '',
        public string $user_email = '',
        public string $user_password  = '',
        public ?string $activation_token = null,
        public ?string $password_reset_token = null,
        public ?string $remember_token = null,
        public string $expiry_timestamp = '',
        public string $password_hash = '',
        public string $password_reset_hash = '',
        public string $activation_hash = '',
        public string $password_reset_expires_at = '',
        public string $otp_secret = '',
        public bool $is_2fa_activated = false,
        public bool $is_active = false,
        public string $remote_addr = '',
        public string $created_at = '',
        public string $user_role = ''
    ) {}
    /**
     * Factory method to create a UserData instance from an associative array (e.g., database row).
     * This allows for easy conversion from raw data to a structured UserData <object data="" type="" class=""></object>
     */
    public static function fromArray(array $data): self
    {

        // A helper to handle potential "string vs integer" dates
        $formatDate = function ($value) {
            if (empty($value)) return '';
            // If it's already a date string (contains a hyphen), return it as is
            if (is_string($value) && str_contains($value, '-')) return $value;
            // If it's a timestamp (numeric), format it
            return is_numeric($value) ? date('Y-m-d H:i:s', (int)$value) : $value;
        };
        return new self(
            id: (int)($data['id'] ?? 0),
            user_name: $data['user_name'] ?? '',
            user_email: $data['user_email'] ?? '',
            user_password: $data['user_password'] ?? '',
            activation_token: $data['activation_token'] ?? '',
            password_reset_token: $data['password_reset_token'] ?? '',
            remember_token: $data['remember_token'] ?? '',
            expiry_timestamp: $data['expiry_timestamp'] ?? '',
            password_hash: $data['password_hash'] ?? '',
            password_reset_hash: $data['password_reset_hash'] ?? '',
            activation_hash: $data['activation_hash'] ?? '',
            password_reset_expires_at: $formatDate($data['password_reset_expires_at'] ?? ''),
            otp_secret: $data['otp_secret'] ?? '',
            is_2fa_activated: (bool)($data['is_2fa_activated'] ?? false),
            is_active: (bool)($data['is_active'] ?? false),
            remote_addr: $data['remote_addr'] ?? ($_SERVER['REMOTE_ADDR'] ?? ''),
            created_at: $formatDate($data['created_at'] ?? ''),
            user_role: $data['user_role'] ?? 'user'
        );
    }
    /**
     * Create a new instance with updated data
     */
    public function with(array $changes): self
    {
        // This merges the current object's properties with the new changes
        return self::fromArray(array_merge((array)$this, $changes));
    }

    /**
     * Convert the UserData instance back to an associative array (e.g., for database storage).
     * This allows for easy conversion from the structured UserData object back to raw data.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'user_email' => $this->user_email,
            'user_password' => $this->user_password,
            'activation_token' => $this->activation_token,
            'password_reset_token' => $this->password_reset_token,
            'remember_token' => $this->remember_token,
            'expiry_timestamp' => $this->expiry_timestamp,
            'password_hash' => $this->password_hash,
            'password_reset_hash' => $this->password_reset_hash,
            'activation_hash' => $this->activation_hash,
            'password_reset_expires_at' => $this->password_reset_expires_at,
            'otp_secret' => $this->otp_secret,
            'is_2fa_activated' => $this->is_2fa_activated,
            'is_active' => $this->is_active,
            'remote_addr' => $this->remote_addr,
            'created_at' => $this->created_at,
            'user_role' => $this->user_role
        ];
    }
}
