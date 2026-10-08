<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 8.2.12
 */
class Config
{
        /**
     * Summary of get
     * @param mixed $key
     */
    public static function get($key)
    {
        $config = [
            // ... other configurations ...
        ];

        return isset($config[$key]) ? $config[$key] : null;
    }

    /**
     * Application
     * @var string
     */

     const HTTP_HOST = 'reviewtool.feritel.swiss';
    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'xixadoka.mysql.db.internal';
    // const DB_HOST = 'mysql2.webland.ch';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'xixadoka_reviewtool';
    // const DB_NAME = 'ferit_review_tool';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'xixadoka_immo';
    // const DB_USER = 'ferit_review_tool';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'EsEbbpf8q934gL7X';
    // const DB_PASSWORD = '999^b%N3%NkQKC';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = false;

    /**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = 'z1kZBN28apBfMJk1RhJ2pQuxXNabJBzU';

    // /**
    //  * Mail API pwd
    //  * @var string
    //  */
    // const SMTP_HOST = 'localhost';

    // const SMTP_PASSWORD = '';
    // const SMTP_USERNAME = 'admin@projectpage.test';

    // const SMTP_PORT = 1025;

    // const FROM_EMAIL = 'noreply@reviewtool.test';

    // const FROM_NAME = 'Reviewtool';

    /**
     * Google recapcha Site Key
     * 
     * @var string
     * 
     */
    const CAPTCHA_SITE_KEY = '6LehoSYpAAAAAAQGzDkOfrC4gsSe4167wVjYZOt9';

    /**
     * Google secret key
     * 
     * @var string
     */

    const CAPTCHA__SECRET = '6LehoSYpAAAAAA1c271GMFgsJGE6qgshtzI3q5Jt';
       /**
     * Two-factor authentication secret key
     * 
     * @var string
     */
    const CompanyName = 'Reviewtool';
}
