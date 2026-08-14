<?php

namespace Core;

use \App\Flash;


/**
 * 
 * Validation handler
 * 
 * PHP version 8.0.22
 */

class Validate
{
    /**
     * 
     * @param string $image file  
     */

    public static function validateImages($data)
    {
        $allowed_ext = array('jpg', 'png', 'jpeg', 'webp');

        if (is_array($data)) {
            $dataCount = count($data);
            if ($dataCount >= 10) {
                foreach ($data['tmp_name'] as $key => $item) {
                    $value = trim($item);
                    if ($value === '') {
                        # code...
                        return true;
                    } else {
                        $name = $data['name'][$key];
                        $size = $data['size'][$key];
                        $img_ext = pathinfo($name, PATHINFO_EXTENSION);

                        if (!$name) {
                            Flash::addMessage('Kein Bild ausgewählt!');
                        }
                        if ($size > 480000) {
                            Flash::addMessage('Bild zu gross, nicht grösser als 500KB!');
                        }
                        if (!in_array($img_ext, $allowed_ext)) {
                            Flash::addMessage('Erlaubte Formate .jpg, .gif, .png, .webp');
                        }
                    }
                }
            }
        } elseif (is_string($data)) {
            // Handle the case where $data is a string (single file)

            $name = $data;
            $size = filesize($name);
            $img_ext = pathinfo($name, PATHINFO_EXTENSION);

            if (!$name) {
                Flash::addMessage('Kein Bild ausgewählt!');
            }
            if ($size > 480000) {
                Flash::addMessage('Bild zu gross, nicht grösser als 500KB!');
            }
            if (!in_array($img_ext, $allowed_ext)) {
                Flash::addMessage('Erlaubte Formate .jpg, .gif, .png, .webp');
            }
        }
    }


    public static function validateDocs($data)
    {
        $data = $_FILES['pdffile'];
        $name = $data['name'];
        $allowed_ext = array('pdf');
        $size = $data['size'];
        $img_ext = pathinfo($name, PATHINFO_EXTENSION);
        // $error = $data['error'];

        if (!$name) {
            Flash::addMessage('Please choose a file!');
        }
        if ($size > 8500000) {
            Flash::addMessage('File is too large, should be less then 8MB!');
        }
        if (!in_array($img_ext, $allowed_ext)) {
            Flash::addMessage('Allowed file formats .pdf');
        }
    }

    public static function validateVideo()
    {
        $data = $_FILES['video'];
        $name = $data['name'];
        $allowed_ext = array('avi', 'mov', 'mp4', 'MP4');
        $size = $data['size'];
        $img_ext = pathinfo($name, PATHINFO_EXTENSION);
        // $error = $data['error'];

        if ($name == null) {
            Flash::addMessage('Please choose a file!');
            return;
        }
        if ($size > 300000000) {
            Flash::addMessage('File is too large, should be less then 300MB!');
        }
        if (!in_array($img_ext, $allowed_ext)) {
            Flash::addMessage('Allowed file formats .avi, .mov and .mp4');
        }
    }
}
