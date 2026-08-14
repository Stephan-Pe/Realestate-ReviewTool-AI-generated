<?php

namespace App\Models;

use PDO;
use App\Token;
use App\Mail;
use Core\View;
use App\Services\TwoFactorService;
use App\Security\UserData;

/**
 * Example user model
 *
 * PHP version 8.0.22
 */
class User extends \Core\Model
{
  public UserData $data;
  public $otp_secret;
  /**
   * Error messages
   * 
   * @var array
   */
  public $errors = [];

  /**
   * Class constructor
   *
   * @param array $data  Initial property values
   *
   * @return void
   */
  final public function __construct($input = [])
  {
    // 1. If we have input, fill the DTO immediately
    $this->data = UserData::fromArray($input);

    // 2. Legacy support: If your core Model or other parts still expect 
    // properties to exist directly on $this, we map them here:
    foreach ($input as $key => $value) {
      if (property_exists($this, $key)) {
        $this->$key = $value;
      }
    }
  }
  /**
   * Magic getter to access properties from the UserData DTO
   * This allows to access properties like $user->user_email, $user->otp_secret, etc. directly on the User model, while the actual data is stored in the UserData DTO.
   * If a property is not found in the UserData DTO, it will return null.
   */
  public function __get($name)
  {
    return $this->data->$name ?? null;
  }
  /**
   * Helper to update the DTO since it's readonly
   */
  protected function updateDto(array $changes): void
  {
    // Convert current DTO to array, merge changes, and recreate
    $currentData = (array) $this->data;
    $this->data = UserData::fromArray(array_merge($currentData, $changes));
  }

  /**
   * Save the user model with the current property values
   *
   * @return void
   */
  public function save()
  {
    $remote_addr = $_SERVER['REMOTE_ADDR'];

    $this->validate();
    if (!empty($this->errors)) return false;

    $token = new Token();
    // $hashed_token = $token->getHash();
    // Update the DTO with the generated security values
    $this->updateDto([
      'user_name' => $this->data->user_name,
      'user_email' => $this->data->user_email,
      'password_hash' => password_hash($this->data->user_password, PASSWORD_DEFAULT),
      'activation_hash' => $token->getHash()
    ]);
 
    $sql = 'INSERT INTO users (user_name, user_email, password_hash, activation_hash, remote_addr)
            VALUES (:user_name, :user_email, :password_hash, :activation_hash, :remote_addr)';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_name', $this->data->user_name, PDO::PARAM_STR);
    $stmt->bindValue(':user_email', $this->data->user_email, PDO::PARAM_STR);
    $stmt->bindValue(':password_hash', $this->data->password_hash, PDO::PARAM_STR);
    $stmt->bindValue(':activation_hash', $this->data->activation_hash, PDO::PARAM_STR);
    $stmt->bindValue(':remote_addr', $remote_addr, PDO::PARAM_STR);
  

    return $stmt->execute();
  }

  public function activateTwoFa($secret)
  {
    $this->updateDto([
      'otp_secret' => $secret,
      'is_2fa_activated' => true,
    ]);
    $sql = 'UPDATE users
            SET is_2fa_activated = true, otp_secret = :otp_secret
            WHERE id = :id';
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $this->data->id, PDO::PARAM_INT);
    $stmt->bindValue(':otp_secret', $secret, PDO::PARAM_STR);
    return $stmt->execute();
  }
  /**
   * Allows isset($this->user_password) to work with the DTO
   */
  public function __isset($name)
  {
    return isset($this->data->$name);
  }

  /**
   * Validate current property values, adding valiation error messages to the errors array property
   *
   * @return void
   */
  public function validate()
  {

    if (!empty($_POST['verifyEmail'])) {
      $this->errors[] = 'Unbekannter Fehler 🔥';
    }
    if (!empty($_POST['yourFullName'])) {
      $this->errors[] = 'Verarbeitungsfehler 🔥';
    }
    // Name
    if ($this->data->user_name == '') {
      $this->errors[] = 'Name muss ausgefüllt werden 🤓';
    }

    // email address
    if (filter_var($this->data->user_email, FILTER_VALIDATE_EMAIL) === false) {
      $this->errors[] = 'Ungültige Email 📫';
    }
    if (static::emailExists($this->data->user_email, $this->data->id ?? null)) {
      $this->errors[] = 'Emailadresse wird schon genutzt 📫';
    }

    // Password
    if (!empty($this->data->user_password)) {

      if (strlen($this->data->user_password) < 8) {
        $this->errors[] = 'Das Passwort muss mindestens 8 Zeichen lang sein';
      }

      if (preg_match('/.*[a-z]+.*/i', $this->data->user_password) == 0) {
        $this->errors[] = 'Das Passwort braucht mindestens einen Buchstaben';
      }

      if (preg_match('/.*\d+.*/i', $this->data->user_password) == 0) {
        $this->errors[] = 'Das Passwort braucht mindestens eine Ziffer';
      }
      if (preg_match('/[!?\-£@#$%^&+=]{1,}.*/i', $this->data->user_password) == 0) {
        $this->errors[] = 'Das Passwort braucht mindestens ein Sonderzeichen';
      }
    }
  }

  /**
   * See if a user record already exists with the specified email
   *
   * @param string $email email address to search for
   *
   * @return boolean  True if a record already exists with the specified email, false otherwise
   */
  public static function emailExists(string $user_email, $ignore_id = null): bool
  {
    $user = static::findByEmail($user_email);

    if ($user) {
      if ($user->id != $ignore_id) {
        return true;
      }
    }
    return false;
  }

  /**
   * Find a user by email address
   *
   * @param string $email email address to search for
   *
   * @return mixed User object if found, otherwise false
   */
  public static function findByEmail(string $user_email)
  {
    $sql = 'SELECT * FROM users WHERE user_email = :user_email';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_email', $user_email, PDO::PARAM_STR);

    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // return $row ? new static($row) : false;
    if (empty($row)) {
        return null;
    }

    return new static($row);
  }

  /**
   * Authenticate user by email and password
   *
   * @param string $email email address 
   * @param string $password password
   *
   * @return mixed User object if found, or false if authentication fails
   */

  public static function authenticate(string $user_email, string $user_password)
  {
    $logDirectory = dirname(dirname(__DIR__)) . '\logs\\';
    $user = static::findByEmail($user_email);

    if ($user && $user->is_active) {
      if (password_verify($user_password, $user->password_hash) && !$user->checkBrute($user_email)) {
        return $user;
      } else {
        $user->updateLogins($user_email);
      }
    }
    if (!$user) {
      $logMessage = "\nLogin error on: " . $_POST["user_email"];
      error_log($logMessage, 3, $logDirectory . 'error.log');
    }
    return false;
  }

  /**
   * Find a user by ID
   *
   * @param string $id The user ID
   *
   * @return mixed User object if found, otherwise false
   */
  public static function findByID(string $id)
  {
    $sql = 'SELECT * FROM users WHERE id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //return $row ? new static($row) : false;
    if (empty($row)) {
        return null;
    }

    return new static($row);
  }

  /**
   * Remember the login by inserting a new unique token into the remembered_logins table
   * for this user record
   *
   * @return boolean  True if the login was remembered successfully, false otherwise
   */
  public function rememberLogin()
  {
    $token = new Token();
    $hashed_token = $token->getHash();
    $expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

    $this->updateDto([
      'remember_token' => $token->getValue(),
      'expiry_timestamp' => $expiry_timestamp
    ]);

    $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $this->data->id, PDO::PARAM_INT);
    $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);

    return $stmt->execute();
  }

  /**
   * Send password reset instructions to the user specified
   * 
   * @param string $email The email address
   * 
   * @return void
   */
  public static function sendPasswordReset($user_email)
  {
    $user = static::findByEmail($user_email);

    if ($user) {

      if ($user->startPasswordReset()) {

        $user->sendPasswordResetEmail();
      }
    }
  }

  /**
   * Start the password reset process by generating a new token and expiry
   *
   * @return void
   */
  protected function startPasswordReset()
  {
    $token = new Token();


    $this->updateDto([
      'password_reset_token' => $token->getValue(),
      'password_reset_hash' => $token->getHash(),
      'password_reset_expires_at' => time() + 7200
    ]);

    $sql = 'UPDATE users
            SET password_reset_hash = :token_hash,
                password_reset_expires_at = :expires_at
            WHERE id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':token_hash', $this->data->password_reset_hash, PDO::PARAM_STR);
    // No date() function here! The DTO already has the string.
    $stmt->bindValue(':expires_at', $this->data->password_reset_expires_at, PDO::PARAM_STR);
    $stmt->bindValue(':id', $this->data->id, PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Send password reset email to the user
   * 
   * @return void
   */
  protected function sendPasswordResetEmail()
  {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->data->password_reset_token;

    $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
    $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

    Mail::send($this->data->user_email, 'Password reset', $text, $html);
  }

  /**
   * Find a user by password reset token and expiry
   * 
   * @param string $token Password reset token send to user
   * 
   * @return mixed User object if found and the token has'nt expired, null otherwise
   */
  public static function findByPasswordReset($token)
  {
    $token = new Token($token);
    $hashed_token = $token->getHash();

    $sql = 'SELECT * FROM users
            WHERE password_reset_hash = :token_hash';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

    $stmt->execute();

    // 1. Fetch as an associative array instead of FETCH_CLASS
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($row)) {
        return null;
    }
    // 2. Instantiate the User object via the constructor
    // This ensures the UserData DTO is built correctly

    if ($row) {
      // 2. Instantiate the User object via the constructor
      // This ensures the UserData DTO is built correctly
      $user = new static($row);

      // 3. Check if password reset token hasn't expired
      // strtotime works perfectly here because password_reset_expires_at is a string
      if (strtotime($user->password_reset_expires_at) > time()) {
        return $user;
      }
    }

    return null;
  }

  /**
   * Reset the user password
   * 
   * @param string $password The new password
   * 
   * @return boolean True if the password was updated successfully, otherwise false
   */
  public function resetPassword($user_password)
  {
    $this->updateDto(['user_password' => $user_password]);
    // call the validate method
    $this->validate();

    if (empty($this->errors)) {
      // 3. Since we passed validation, create the hash and clear reset fields
      $this->updateDto([
        'password_hash' => password_hash($this->data->user_password, PASSWORD_DEFAULT),
        'password_reset_hash' => null,
        'password_reset_expires_at' => null
      ]);

      $sql = 'UPDATE users
              SET password_hash = :password_hash,
              password_reset_hash = NULL,
              password_reset_expires_at = NULL
              WHERE id = :id';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':id', $this->data->id, PDO::PARAM_INT);
      $stmt->bindValue(':password_hash', $this->data->password_hash, PDO::PARAM_STR);

      return $stmt->execute();
    }
    return false;
  }


  /**
   * Send an email to the user containing the activation link
   *
   * @return void
   */
  public function sendActivationEmail()
  {
    $this->updateDto([
      'activation_hash' => $this->data->activation_hash
    ]);
    $url = 'https://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->data->activation_hash;

    $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
    $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

    Mail::send($this->data->user_email, 'Account activation', $text, $html);
  }

  public static function findByActivationToken($token_from_url)
  {
    $sql = 'SELECT * FROM users WHERE activation_hash = :hashed_token';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':hashed_token', $token_from_url, PDO::PARAM_STR);

    // 1. Fetch as an array, NOT as a class yet
    $stmt->execute();
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($row) {
      // 2. Map the array to your DTO using your helper
      $userData = UserData::fromArray($row);

      // 3. Return a User object wrapping that DTO
      $user = new static();
      $user->data = $userData;
      return $user;
    }

    return false;
  }
  /**
   * Activate the user account with the specified activation token
   * 
   * @param string $value Activation token from url
   * 
   * @return void
   */
  public function activate()
  {
    $this->updateDto([
      'is_active' => 1,
      'activation_hash' => null,
      'id' => $this->data->id
    ]);
    $sql = 'UPDATE users
            SET is_active = 1, activation_hash = null
            WHERE id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $this->data->id, PDO::PARAM_INT);
    $stmt->execute();
  }

  /**
   * Update the user's profile
   * 
   * @param array $data Data from the edit profile form
   * 
   * @return boolean True if the data was updated, otherwise false
   */
  public function updateProfile($data)
  {
    // Map array to local variables or update the DTO
    $updates = [
      'user_name' => $data['user_name'],
      'user_email' => $data['user_email']
    ];

    // Only add password to the update if the user actually typed something
    if (!empty($data['user_password'])) {
      $updates['user_password'] = $data['user_password'];
    }

    $this->updateDto($updates);

    $this->validate();

    if (empty($this->errors)) {

      $sql = 'UPDATE users
                SET user_name = :user_name,
                    user_email = :user_email';

      // Add password if it's set
      if (isset($this->data->user_password)) {
        $sql .= ', password_hash = :password_hash';
      }

      $sql .= "\nWHERE id = :id";

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':user_name', $this->data->user_name, PDO::PARAM_STR);
      $stmt->bindValue(':user_email', $this->data->user_email, PDO::PARAM_STR);
      $stmt->bindValue(':id', $this->data->id, PDO::PARAM_INT);

      // Add password if it's set
      if (isset($this->data->user_password)) {
        $password_hash = password_hash($this->data->user_password, PASSWORD_DEFAULT);
        $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
      }

      return $stmt->execute();
    }

    return false;
  }

  /**
   * monitor unsuccessfull logins and block potentional intruder
   * 
   * @param mixed $emailUser 
   * @return bool 
   */
  public static function checkBrute($emailUser)
  {
    $now = time();
    $valid_attempts = $now - (2 * 60 * 60);
    $sql = "SELECT COUNT(emailUser) FROM loginattempts WHERE emailUser = '$emailUser' AND timestamp > '$valid_attempts'";

    $db = static::getDB();
    $stmt = $db->query($sql);
    $count = $stmt->fetchColumn();
    if ($count > 4) {
      return true; // Return true if more than 3 attempts
    } else {
      return false; // Return false if 3 or fewer attempts
    }
  }

  /**
   * update the loginattempts to find potential attacks
   * 
   * @param string $emailUser 
   * @return mixed 
   */

  public static function updateLogins(String $emailUser)
  {
    $timestamp = time();
    $remote = $_SERVER['REMOTE_ADDR'];
    $sql = 'INSERT INTO loginattempts (emailUser, timestamp, remote) VALUES ( :emailUser,  :timestamp, :remote)';
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':emailUser', $emailUser, PDO::PARAM_STR);
    $stmt->bindValue(':timestamp', $timestamp, PDO::PARAM_STR);
    $stmt->bindValue(':remote', $remote, PDO::PARAM_STR);

    return $stmt->execute();
  }

  /**
   * Find user information for administration
   * 
   * @return mixed 
   */
  public static function findUsersForAdmin()
  {
    $db = static::getDB();
    $stmt = $db->query('SELECT users.id, users.user_name, users.user_email, users.user_role, users.created_at FROM users WHERE users.is_active = 1'); //ORDER BY created_at DESC
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $results;
  }

  public static function findIntrudersForAdmin()
  {
    $time_limit = date('Y-m-d H:i:s'); // Current time or adjusted for "stale" users

    $db = static::getDB();
    $sql = "SELECT * FROM users 
            WHERE is_active = 0 
            AND created_at < :time_limit 
            ORDER BY created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute(['time_limit' => $time_limit]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Turn every database row into a UserData DTO
    return array_map(fn($row) => UserData::fromArray($row), $rows);
  }

  /**
   * Find user information for administration
   * 
   * @return mixed 
   */
  public static function findUsersEmail()
  {
    $db = static::getDB();
    $stmt = $db->query('SELECT users.user_email FROM users WHERE users.is_active = 1 AND users.user_role = "user"'); //ORDER BY created_at DESC
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $results;
  }
  /**
   * Deactivate the user account with the specified activation token
   * 
   * @param string $value Activation token from url
   * 
   * @return void
   */
  public function deactivate($id)
  {
    $this->updateDto([
      'is_active' => 0,
      'id' => $id
    ]);
    $sql = 'UPDATE users
      SET is_active = 0
      WHERE id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_STR);

    $stmt->execute();
  }

  /**
   * Delete Property by user
   *
   *@return bool Returns true on successful deletion, false otherwise
   */

  public function deleteByUser($data)
  {
    $this->updateDto([
      'id' => $data['id']
    ]);

    $sql = 'DELETE FROM users WHERE id = :id';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $this->data->id, PDO::PARAM_STR);

    return $stmt->execute(); // Return the result of the execute() method
  }

}
