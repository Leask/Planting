<?php

class Core {

    static function readJson($fileName, $assoc = false) {
        return json_decode(file_get_contents(ROOT . $fileName), $assoc);
    }


    static function getRequestBody() {
        return file_get_contents('php://input');
    }


    static function log($data) {
        return error_log($data);
    }


    static function load($fileName) {
        require_once ROOT . $fileName;
    }


    static function aca($webUrl) {
        header("Access-Control-Allow-Origin: {$webUrl}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Accept-Timezone');
    }


    static function route($method, $uri, $routes) {
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


    static function randString() {
        return md5(rand().rand().rand());
    }


    static function length($string) {
        return mb_strlen($string, 'utf8');
    }


    static function lenLimit($string, $min, $max) {
        $length = self::length($string);
        return $length >= $min && $length <= $max;
    }


    static function isoTime($timestamp) {
        return date(DateTime::ISO8601, $timestamp);
    }


    static function dbTimeToIsoTime($strTime) {
        return self::isoTime(strtotime($strTime));
    }

}
