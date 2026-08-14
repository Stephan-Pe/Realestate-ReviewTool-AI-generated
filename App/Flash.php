<?php 

namespace App;

/**
 * Flash notification messages for one-time using the session
 * for storage between requests
 * 
 * PHP version 8.0.22
 */
class Flash
{
   /**
     * Success message type
     * 
     * @var string
     */
    const SUCCESS = 'success';

   /**
     * Info message type
     * 
     * @var string
     */
    const INFO = 'info';

   /**
     * Warning message type
     * 
     * @var string
     */
    const WARNING = 'warning';

    /**
     * Add a message
     * 
     * @param string $message The message content
     * 
     * @return void
     */

     public static function addMessage(string $message, string $type = 'success')
     {
        if (! isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }

        // Append the message to the array
        $_SESSION['flash_notifications'][] = [
            'body' => $message,
            'type' => $type
        ];
     }

     /**
      * Get all the messages
      *
      *@return mixed An array with all the messages or null if none is set
      *
      */
      public static function getMessages()
      {
        if (isset($_SESSION['flash_notifications'])) {
           $messages = $_SESSION['flash_notifications'];
           unset($_SESSION['flash_notifications']);

           return $messages;
        }
      }
}