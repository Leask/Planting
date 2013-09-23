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


    static function rawCheckToken($token, $authItem) {
        global $env;
        switch ($authItem) {
            case 'fresh_person':
                if ($env['now'] - strtotime($token['created_at']) > 15) {
                    return ['error' => 'token_staled'];
                }
            case 'person':
                if ($token['category'] !== 'person') {
                    return null;
                }
                break;
            case 'verification':
                if ($token['category'] !== 'verification') {
                    return null;
                }
        }
        return ['error' => ''];
    }


    static function checkToken($code, $authentication) {
        if ($authentication) {
            if (!$code) {
                return ['error' => 'authentication_required', 'code' => 401];
            }
            if (!($token = MdlToken::getByCode($code))) {
                return ['error' => 'authentication_required', 'code' => 401];
            }
            foreach ($authentication ?: [] as $authItem) {
                $chkResult = self::rawCheckToken($token, $authItem);
                if ($chkResult === null) {
                } else if (@$chkResult['error']) {
                    return ['error' => $chkResult['error'],   'code' => 403];
                }
                return ['token' => $token];
            }
            return ['error' => 'token_category_not_matched',  'code' => 403];
        }
        return ['error' => ''];
    }


    static function dispatch($path) {
        global $env;
        $parameters = ['uri' => $env['uri']];
        if ($path['authentication']) {
            $chkResult = self::checkToken(
                @$_GET['token'], $path['authentication']
            );
            if (@$chkResult['error']) {
                // @todo: chkResult['error']
                // @todo: chkResult['code']
                return;
            } else if (@$chkResult['token']) {
                $parameters['token'] = $chkResult['token'];
            }
        }
        $ctlName = "ctl{$path['controller']}";
        $actName = "act{$path['action']}";
        self::load("controllers/{$ctlName}.php");
        $controller = new $ctlName($parameters);
        $controller->$actName();
    }

}
