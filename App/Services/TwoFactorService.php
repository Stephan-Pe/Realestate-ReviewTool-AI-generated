<?php

namespace App\Services;


use App\Security\TwoFactorAuth;
use PragmaRX\Google2FA\Google2FA;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use \App\Config;
use \App\Models\User;
use App\Security\UserData;

/**
 * Two Factor Authentication Service
 * 
 * 
 * PHP version 8.2.12
 */
class TwoFactorService
{
  protected TwoFactorAuth $twoFactorAuth;
  private Google2FA $google2fa;

  /**
   * The Constructor injects the Google2FA and TwoFactorAuth dependencies for use in the service methods.
   * @param Google2FA $google2fa - The Google2FA library instance for generating secrets and verifying codes.
   * @param TwoFactorAuth $twoFactorAuth - The TwoFactorAuth service for handling 2FA-related logic such as generating QR code URLs.
   */
  public function __construct(Google2FA $google2fa, TwoFactorAuth $twoFactorAuth)
  {
    $this->google2fa = $google2fa;
    $this->twoFactorAuth = $twoFactorAuth;
  }

  /**
   * Generate a QR code for the user's 2FA setup
   * @param UserData $user - The user data containing email and secret
   * @return string - Data URI of the generated QR code image
   */
  public function createQRCode($userData, $secret): string
  {

    $url = $this->twoFactorAuth->getQrUrl(
      $userData->user_email,
      $secret
    );

    // build QR image here (your existing code)
    $writer = new PngWriter();


    $qrCode = new QrCode(
      data: $url,
      encoding: new Encoding('UTF-8'),
      errorCorrectionLevel: ErrorCorrectionLevel::Low,
      size: 200,
      margin: 10,
      roundBlockSizeMode: RoundBlockSizeMode::Margin,
      foregroundColor: new Color(0, 0, 0),
      backgroundColor: new Color(255, 255, 255)
    );

    // Path logic - make sure this points to your actual logo file
    $upOne = dirname(__DIR__, 2); // Adjusted based on your folder structure
    $logoPath = $upOne . 'Assets/siteart_maskable_x128.png';

    $logo = null;
    if (file_exists($logoPath)) {
      $logo = new Logo(
        path: $logoPath,
        resizeToWidth: 50,
        punchoutBackground: true
      );
    }
    // Create generic label
    $label = new Label(
      text: Config::CompanyName,
      textColor: new Color(255, 0, 0)
    );

    // Write the result (logo is optional if file doesn't exist)
    $result = $writer->write($qrCode, $logo, $label);

    return $result->getDataUri();
  }

  /**
   * Enable 2FA for the current user
   * @return bool - True if successful, false otherwise
   */
  public function enableTwoFactor()
  {
    // 1. Get the User object
    $user = User::findByEmail($_SESSION['user_email']);

    if ($user) {
      // 2. Generate the secret using your working static method
      $secret = new TwoFactorAuth(new Google2FA());
      $secret = $secret->generateSecret();

      // 3. Update the DTO (This replaces the old immutable data with new data)
      $user->updateDto([
        'otp_secret' => $secret,
        'is_2fa_activated' => true // Or false if you want them to verify first!
      ]);

      // 4. Save to Database
      return $user->save();
    }
    return false;
  }

  /**
   * Get the current user's 2FA secret
   * @return string|null - The 2FA secret or null if not found
   */
  
  public function getTwoFactorSecret()
  {
    $user = User::findByEmail($_SESSION['user_email']);

    return $user ? $user->otp_secret : null;
  }

  /**
   * Disable 2FA for the current user
   * @return bool - True if successful, false otherwise
   */

  public function disableTwoFactor()
  {
    $user = User::findByEmail($_SESSION['user_email']);

    if ($user) {
      // Clear the secret and the flag
      $user->updateDto([
        'otp_secret' => '',
        'is_2fa_activated' => false
      ]);

      return $user->save();
    }
    return false;
  }


  /**
   * @param string $secret The 16/32 char secret from session/DB
   * @param string $code The 6-digit code from the user
   * @param int $window The time drift allowance (1 = 30 seconds)
   */

  public function verifyCode(string $secret, string $code, int $window = 1): bool
  {
    // Ensure the code is a string to preserve leading zeros (e.g., "012345")
    $code = (string)$code;

    return $this->google2fa->verifyKey($secret, $code, $window);
  }
}
