<?php
/**
 * CareNodes
 * @author Leask Huang <i@leaskh.com>
 * @copyright Copyright (C) 2013 CareNodes
 */

// Initialization
define('ROOT', "{$_SERVER['DOCUMENT_ROOT']}../");
// Automatic load class
function __autoload($className) {
    include_once ROOT . 'library/' . strtolower($className) . '.php';
}
$env = Core::readJson('env.json', true);
if (!$env) {
    Core::log('Error env.json!');
    exit();
}
$env['now'] = time();
$env['uri'] = strtolower(trim($_SERVER['REQUEST_URI']));


// {
# magic_quotes_gpc = Off
# zlib.output_compression = On
# zlib.output_handler = On
# ini_set('error_reporting', E_ALL);
# ini_set('date.timezone', 'UTC');
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('UTC');
set_time_limit(3);
ini_set('post_max_size', '15M');
ini_set('upload_max_filesize', '15M');
ini_set('session.cookie_domain', '.carenodes.com');
ini_set('log_errors', 'On');
ini_set('error_log', '/var/log/php.log');
// }


// Access log
Core::log("+ {$env['uri']}");

// Access Control Allow
Core::aca($env['web_url']);

// Dispatch
$routes = Core::readJson('routes.json', true);
if (!$routes) {
    Core::log('Error routes.json!');
    exit();
}
$path = Core::route($_SERVER['REQUEST_METHOD'], $env['uri'], $routes);
if ($path) {
    Core::dispatch($path);
} else {
    echo 404;
    // 404;
}

// Access log
Core::log("- {$env['uri']}");
