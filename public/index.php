<?php
/**
 * CareNodes
 * @author Leask Huang <i@leaskh.com>
 * @copyright Copyright (C) 2013 CareNodes
 */

// Init Core
define('ROOT', "{$_SERVER['DOCUMENT_ROOT']}/../");
include_once ROOT . 'core.php';
$env = readJson('env.json', true);
if (!$env) {
    writeLog('Error env.json!');
    exit();
}

// Access log
writeLog("+ {$_SERVER['REQUEST_URI']}");

// Access Control Allow
aca($env['web_url']);

// Dispatch
$routes = readJson('routes.json', true);
if (!$routes) {
    writeLog('Error routes.json!');
    exit();
}
$path = route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $routes);
if ($path) {
    $ctlName = "ctl{$path['controller']}";
    $actName = "act{$path['action']}";
    load("controllers/{$ctlName}.php");
    $controller = new $ctlName();
    $controller->$actName();
} else {
    echo 404;
    // 404;
}


// Access log
writeLog("- {$_SERVER['REQUEST_URI']}");
