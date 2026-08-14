<?php

namespace App;

use \App\Config;
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


/**
 * Mail
 * 
 * PHP version 8.0.22
 */
class Mail
{
    /**
     * Send mail
     * 
     * @param string $to
     * @param string $subject
     * @param string $text
     * @param string|null $html
     * 
     * @return boolean
     */

    public static function send($to, $subject, $text, $html = null)
    {
          $mail = new PHPMailer(true);
        try {
          
            
            // MailDev Configuration
            $mail->isSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                         //Disable debug in production
            $mail->CharSet = "UTF-8"; 
            $mail->Host = Config::SMTP_HOST;
            $mail->Port = Config::SMTP_PORT;
            $mail->SMTPAuth = false; // MailDev doesn't require auth
            $mail->SMTPAutoTLS = false; // Disable TLS for MailDev
            
            $mail->setFrom(Config::FROM_EMAIL, Config::FROM_NAME);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $html ?? $text;
            $mail->AltBody = $text;
            
            if ($html) {
                $mail->isHTML(true);
            }
            
            return $mail->send();
            
        } catch (\Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }
}
