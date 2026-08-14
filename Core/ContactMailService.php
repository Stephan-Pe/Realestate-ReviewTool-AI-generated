<?php 

namespace Core;
use App\Mail;
use Core\View;
use \Exception;
class ContactMailService
{
    public function sendContactMessage(string $receiverEmail, array $data): bool
    {
      
        $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
        $logFile = $logDir . 'mail.log';

        // Prepare templates
        $text = View::getTemplate('Home/send_contactmail.txt', $data);
        $html = View::getTemplate('Home/send_contactmail.html', $data);

        // Log attempt
        error_log("Sending to: $receiverEmail\n", 3, $logFile);

        try {
            $ok = Mail::send($receiverEmail, $data['contact_subject'], $text, $html);

            if ($ok) {
                error_log("✓ Success: $receiverEmail\n", 3, $logFile);
                return true;
            }

            error_log("✗ Failed: $receiverEmail\n", 3, $logFile);
            return false;

        } catch (\Throwable $e) {
            // Catch exceptions so they don't freeze the controller
            error_log("Exception: ".$e->getMessage()."\n", 3, $logFile);
            return false;
        }
    }
}
