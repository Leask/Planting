<?php

class MdlToken {

    static function create(
        $person_id, $client, $category, $resource, $scope, $data, $expires_in
    ) {
        global $env;
        $person_id = (int) $person_id;
        $client    = Dbio::escape(strtolower(trim($client)));
        $category  = Dbio::escape(strtolower(trim($category)));
        $resource  = (array) $resource;
        ksort($resource);
        $resource  = md5(json_encode($resource));
        $cScope    = [];
        foreach ($scope ?: [] as $item) {
            if (($item = strtolower(trim($item)))) {
                $cScope[] = $item;
            }
        }
        sort($cScope);
        $cScope     = Dbio::escape(json_encode($cScope));
        $data       = Dbio::escape(json_encode($data));
        $code       = Core::randString() . Core::randString();
        $expires_at = $env['now'] + (int) $expires_in;
        $rawResult  = Dbio::execute(
            "INSERT INTO `tokens` SET
            `code`          = '{$code}',
            `person_id`     =  {$person_id},
            `client`        = '{$client}',
            `category`      = '{$category}',
            `resource_hash` = '{$resource}',
            `scope`         = '{$cScope}',
            `data`          = '{$data}',
            `touched_at`    =  NOW(),
            `updated_at`    =  NOW(),
            `expires_at`    =  FROM_UNIXTIME({$expires_at});"
        );
        if ($rawResult) {
            if (($token = self::getById($rawResult['insert_id']))) {
                return ['token' => $token];
            }
        }
        return ['error' => 'server_error'];
    }


    static function pack($rawToken) {
        return $rawToken ? [
            'code'          => $rawToken['code'],
            'person_id'     => (int) $rawToken['person_id'],
            'client'        => $rawToken['client'],
            'category'      => $rawToken['category'],
            'resource_hash' => $rawToken['resource_hash'],
            'scope'         => json_decode($rawToken['scope'], true),
            'data'          => json_decode($rawToken['data'],  true),
            'created_at'    => Core::dbTimeToIsoTime($rawToken['created_at']),
            'touched_at'    => Core::dbTimeToIsoTime($rawToken['touched_at']),
            'updated_at'    => Core::dbTimeToIsoTime($rawToken['updated_at']),
            'expires_at'    => Core::dbTimeToIsoTime($rawToken['expires_at']),
            'class'         => 'token',
        ] : null;
    }


    static function touchToken($id) {
        return ($id = (int) $id) ? Dbio::execute(
            "UPDATE `tokens` SET `touched_at` = NOW() WHERE `id` = {$id};"
        ) : null;
    }


    static function getById($id) {
        $id = (int) $id;
        if ($id) {
            $rawToken = Dbio::queryRow(
                "SELECT * FROM `tokens`
                 WHERE `id` = {$id} AND `expires_at` > NOW();"
            );
            return self::pack($rawToken);
        }
        return null;
    }


    static function getByCode($code) {
        $code = Dbio::escape(trim($code));
        if ($code) {
            $rawToken = Dbio::queryRow(
                "SELECT * FROM `tokens`
                 WHERE `code` = '{$code}' AND `expires_at` > NOW();"
            );
            if ($rawToken) {
                self::touchToken($rawToken['id']);
                return self::pack($rawToken);
            }
        }
        return null;
    }


    static function getByResource($resource) {
        $resource  = (array) $resource;
        ksort($resource);
        $resource  = md5(json_encode($resource));
        if ($resource) {
            $rawToken = Dbio::queryRow(
                "SELECT * FROM `tokens`
                 WHERE `resource_hash` = '{$resource}'
                 AND   `expires_at`    > NOW();"
            );
            return self::pack($rawToken);
        }
        return null;
    }


    static function removeByCode($code) {

    }


    static function removeByResource() {

    }

}
