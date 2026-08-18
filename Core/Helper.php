<?php

namespace Core;

use PDO;
use Exception;
/**
 * 
 * Helper functions for repetitive tasks
 * 
 *  PHP version 8.2.12
 */

class Helper extends \Core\Model
{
    /**
     * Return new filename for all usecases
     * 
     * @param mixed $file_name 
     * @return string 
     */
    public static function newFilename($file_name)
    {
        $year = date("Y-m-d");
        $standardId = uniqid("", true);
        $uniqueId = str_replace('.', '', $standardId);
        $file_nameWithoutExtension = pathinfo($file_name, PATHINFO_FILENAME);
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $newFileName = $file_nameWithoutExtension . $year . $uniqueId . "." . $ext;
        return $newFileName;
    }
    /**
     * 
     * @param mixed $filePath 
     * 
     * @return void 
     */
    public static function deleteFile($filePath)
    {
        $logDirectory = dirname(__DIR__) . '\logs\\';
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                // Log that the file was successfully deleted
                $logMessage = "\nDeleted file: $filePath";
                error_log($logMessage, 3, $logDirectory . 'deletion.log');
            } else {
                // Log an error if the file couldn't be deleted
                $logMessage = "\nError deleting file: $filePath";
                error_log($logMessage, 3, $logDirectory . 'error.log');
            }
        } else {
            // Log a message if the file doesn't exist or targets a directory
            $logMessage = "\nFile not found or path targets a directory: $filePath";
            error_log($logMessage, 3, $logDirectory . 'not_found.log');
        }
    }

    /**
     * Summary of outputCaptchaImage
     * @return void
     */
    public static function outputCaptchaImage()
    {
       
        try {
            $width = 200;
            $height = 60;

            $im = imagecreatetruecolor($width, $height);

            $background = imagecolorallocate($im, 170, 163, 163);
            imagefill($im, 0, 0, $background);

            // generate string
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $captcha_string = '';

            for ($i = 0; $i < 5; $i++) {
                $captcha_string .= $chars[random_int(0, strlen($chars) - 1)];
            }

            // store ONLY the string
            $_SESSION['captcha'] = $captcha_string;

            $colors = [
                imagecolorallocate($im, 255, 0, 0),
                imagecolorallocate($im, 0, 255, 0),
                imagecolorallocate($im, 0, 0, 255),
                imagecolorallocate($im, 255, 255, 0),
                imagecolorallocate($im, 255, 0, 255),
            ];

            $font = "fonts/impact.ttf";

            for ($i = 0; $i < 5; $i++) {
                $x = 20 + $i * 30;
                $y = 40;
                imagettftext($im, 28, 0, $x, $y, $colors[$i], $font, $captcha_string[$i]);
            }

            imagefilter($im, IMG_FILTER_PIXELATE, 2, true);

            // 🚀 OUTPUT — not save
            header('Content-Type: image/png');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            imagepng($im);

            unset($im);
        } catch (Exception $e) {
            http_response_code(500);
        }
    }

    /**
     * 
     * @return void 
     */
    public static function deleteFileOnUpdate(string $sql, string $fileDirectory)
    {
    
        $db = static::getDB();
    
        $stmt = $db->prepare($sql); 
        $stmt->execute();
        $dbFiles = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        foreach ($dbFiles as $key => $value) {
            $dbFiles[$key] = basename($value);
        }
        
        // Get the list of all files in your directory
        $dirFiles = array_diff(scandir( $fileDirectory), array('..', '.'));
        // Loop through directory files
        
        foreach ($dirFiles as $file) {
            if (!in_array($file, $dbFiles)) {
                unlink($fileDirectory . '/' . $file);
            }
        }
    }
    /**
    * 
    * @return void 
    */
   public static function deleteFilesOnUpdate($sql, $fileDirectory)
   {
      $db = static::getDB();

      $stmt = $db->prepare($sql);
      $stmt->execute();
      $dbFiles = $stmt->fetchColumn();
      // $dbFiles are now a comma-separated string and have to be converted into an array
      $paths = explode(",", $dbFiles);
      foreach ($paths as $dbFile) {
         $dbBasenames[] = basename($dbFile);
      }

      // Get the list of all files in the files directory
      $dirFiles = array_diff(scandir($fileDirectory), array('..', '.'));

      // Iterate through $dirFiles
      foreach ($dirFiles as $key => $dirFile) {
         $basename = basename($dirFile);
         if ($basename && !in_array($basename, $dbBasenames)) {
            // Unlink the file (delete it)
            unlink($fileDirectory . '/' .$dirFile);
         }
      }
   }

      /**
     * Helper: Decode JSON input body, fallback to $_POST
     * Checks $_SERVER['JSON_INPUT'] first (set by CsrfMiddleware)
     *
     * @return array
     */
    public static function getJsonInput(): array
    {
        // Check if CsrfMiddleware already decoded the input
        if (isset($_SERVER['JSON_INPUT']) && is_array($_SERVER['JSON_INPUT'])) {
            return $_SERVER['JSON_INPUT'];
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            if (is_array($data)) {
                return $data;
            }
        }
        return $_POST ?: [];
    } 
}
