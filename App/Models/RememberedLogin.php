<?php

namespace App\Models;

use PDO;
use \App\Token;

/**
 * Remembered login model
 * 
 * PHP version 8.2.12
 */
class RememberedLogin extends \Core\Model
{
    public $expires_at, $token_hash;
    /**
     * Find a remembered login model by the token
     * 
     * @param string $token The remembered login token
     * 
     * @return mixed Remembered login object if found, otherwise false
     */
    public static function findByToken(string $token)
    {
        $token = new Token($token);
        $token_hash = $token->getHash();

        $sql = 'SELECT * FROM remembered_logins WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get the user model associated with this remembered login
     * 
     * @return User The user model
     */
    public function getUser()
    {
        return User::findByID($this->user_id);
    }

    /**
     * See if remember token has expired or not, based on the current system time
     * 
     * @return boolean True if the token has expired, otherwise false
     */
    public function hasExpired(): bool
    {
        return strtotime($this->expires_at) < time();
    }

    /**
     * Delete this model
     * 
     * @return void
     */
    public function delete()
    {
        $sql = 'DELETE FROM remembered_logins WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

        $stmt->execute();
    }
}
