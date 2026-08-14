<?php


namespace App\Middleware;

use App\Auth;



class AccessControl
{
    // Define the hierarchy: higher index = more power
 private static $levels = [
        'guest'   => 0,
        'user'    => 1,
        'manager' => 2,
        'admin'   => 3,
        'super'   => 4
    ];
    /**
     * 
     */
    public static function hasClearance($minRole)
    {
        $user = Auth::getUser();

        // 1. Get current user's role string, default to 'guest'
        $currentRole = $user ? $user->user_role : 'guest';

        // 2. Safety check: does the requested role even exist in our map?
        if (!isset(self::$levels[$minRole]) || !isset(self::$levels[$currentRole])) {
            return false;
        }

        // 3. The user's level higher or equal to the requirement?
        return self::$levels[$currentRole] >= self::$levels[$minRole];
    }
    
    /**
     * @param string $minRole The minimum role required to access the page
     */
    public static function requireRole($minRole)
    {

        // Check if the user's role exists and meets the minimum level
        if (!self::hasClearance($minRole)) {

            if (!Auth::getUser()) {
                // Not logged in? Send to login
               self::redirect('/login');
            } else {
                // Logged in but weak permissions? Send to 403
               self::redirect('/403');
            }
            exit;
        }
    }
    private static function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}
