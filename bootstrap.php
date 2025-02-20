<?php

// Load Composer's autoloader
require_once 'vendor/autoload.php';

if (!isset($_SESSION)) {
    // Tell client to only send cookie(s) over HTTPS
    ini_set('session.gc_maxlifetime', 2592000); // 30 days
    ini_set('session.cookie_lifetime', 2592000); // 30 days
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.cookie_secure', true);
    ini_set('session.use_strict_mode', true);

    session_start();
}

// Initialize other dependencies or services here
// For example, a logger or cache system

require_once 'includes/functions/functions.php';

// Your application is now bootstrapped and ready to run
