<?php
// anytime functions

// Automatic load class
function __autoload($className) {
    include_once ROOT . 'library/' . strtolower($className) . '.php';
}

function readJson($fileName, $assoc = false) {
    return json_decode(file_get_contents(ROOT . $fileName), $assoc);
}

function getRequestBody() {
    return file_get_contents('php://input');
}

function writeLog($data) {
    return error_log($data);
}

function load($fileName) {
    require_once ROOT . $fileName;
}

function aca($webUrl) {
    header("Access-Control-Allow-Origin: {$webUrl}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Accept-Timezone');
}

function route($method, $uri, $routes) {
    foreach ($routes as $route) {
        if (!$route['methods'] || in_array($method, $route['methods'])) {
            foreach ($route['patterns'] as $pattern) {
                if (preg_match($pattern, $uri)) {
                    return $route;
                }
            }
        }
    }
    return null;
}

function randString() {
    return md5(rand().rand().rand());
}

function length($string) {
    return mb_strlen($string, 'utf8');
}

function lenLimit($string, $min, $max) {
    $length = length($string);
    return $length >= $min && $length <= $max;
}

function isoTime($timestamp) {
    return date(DateTime::ISO8601, $timestamp);
}

function dbTimeToIsoTime($strTime) {
    return isoTime(strtotime($strTime));
}
