<?php
// Initialisation commune aux deux points d'entrée (public/index.php et public/api.php)

const RACINE_PATH = __DIR__;

date_default_timezone_set('Europe/Brussels');
mb_internal_encoding('UTF-8');

if (file_exists(RACINE_PATH . '/config.php')) {
    require_once RACINE_PATH . '/config.php';
}
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}

// Détail des erreurs : en mode debug, ou quand le site est consulté depuis
// la machine du développeur (MAMP, php -S). Jamais pour un visiteur distant.
define('SHOW_ERROR_DETAILS', APP_DEBUG || in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true));

// Le visiteur ne voit jamais d'erreur technique (SEC-10) : tout part dans logs/
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', RACINE_PATH . '/logs/php-errors.log');
error_reporting(E_ALL);

require_once RACINE_PATH . '/autoload.php';

App\Core\Session::start();
