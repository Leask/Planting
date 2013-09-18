<?php
/**
 * CareNodes
 * @author Leask Huang <i@leaskh.com>
 * @copyright Copyright (C) 2013 CareNodes
 */

// Initialization
define('ROOT', "{$_SERVER['DOCUMENT_ROOT']}/../");
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

// Access log
Core::log("+ {$_SERVER['REQUEST_URI']}");

// Access Control Allow
Core::aca($env['web_url']);

// Dispatch
$routes = Core::readJson('routes.json', true);
if (!$routes) {
    Core::log('Error routes.json!');
    exit();
}
$path = Core::route(
    $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $routes
);
if ($path) {
    Core::dispatch($path);
} else {
    echo 404;
    // 404;
}

// Access log
Core::log("- {$_SERVER['REQUEST_URI']}");
