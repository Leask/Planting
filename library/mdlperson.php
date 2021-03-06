<?php

class MdlPerson extends model {

    private $salt = '7e32b7639cdfe0f8a92d2d76d2b9a357';


    private function encryptPassword($password, $salt) {
        global $env;
        return md5("{$env['salt']}{$password}{$this->salt}{$salt}");
    }


    private function validatePassword($password, $salt, $encryptedPassword) {
        return $this->encryptPassword($password, $salt) === $encryptedPassword;
    }


    private function checkPassword($person, $password) {
        if (!$person) {
            return ['error' => 'invalid_person'];
        }
        if (!$password) {
            return ['error' => 'invalid_password'];
        }
        if (!$person['password'] || !$person['salt']) {
            return ['error' => 'no_password'];
        }
        if (!$this->validatePassword(
            $password, $person['salt'], $person['password']
        )) {
            return ['error' => 'invalid_password'];
        }
        return ['error' => ''];
    }


    static function validateEmail($email) {
        $email   = strtolower(trim($email));
        $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
        return preg_match($pattern, $email) ? $email : null;
    }


    public function pack($rawPeople, $person_id = 0) {
        return $rawPeople ? [
            'id'          => (int) $rawPeople['id'],
            'external_id' => $rawPeople['external_id'],
            'provider'    => $rawPeople['provider'],
            'name'        => strlen($rawPeople['name']) ? $rawPeople['name'] : $rawPeople['screen_name'],
            'screen_name' => $rawPeople['screen_name'],
            'description' => strlen($rawPeople['description']) ? $rawPeople['description'] : '',
            'avatar'      => $rawPeople['avatar'] ?: '',
            'created_at'  => Core::dbTimeToIsoTime($rawPeople['created_at']),
            'updated_at'  => Core::dbTimeToIsoTime($rawPeople['updated_at']),
            'status'      => $rawPeople['status'],
            'timezone'    => $rawPeople['timezone'],
            'locale'      => $rawPeople['locale'],
            'class'       => 'person',
        ] : null;
    }


    public function getById($id, $raw = false, $person_id = 0) {
        if (($id = (int) $id)) {
            $curPerson = Dbio::queryRow(
                "SELECT * FROM `people` WHERE `id` = {$id};"
            );
            return $raw ? $curPerson : $this->pack($curPerson);
        }
        return null;
    }


    public function getByScreenName($screen_name, $raw = false) {
        $screen_name = Dbio::escape(strtolower(trim($screen_name)));
        if (Core::length($screen_name)) {
            $curPerson = Dbio::queryRow(
                "SELECT * FROM `people` WHERE `screen_name` = '{$screen_name}';"
            );
            return $raw ? $curPerson : $this->pack($curPerson);
        }
        return null;
    }


    public function getByExternalIdAndProvider(
        $external_id, $provider, $raw = true
    ) {
        $provider = Dbio::escape(strtolower(trim($provider)));
        switch ($provider) {
            default:
                $external_id = strtolower($external_id);
        }
        $external_id = Dbio::escape(trim($external_id));
        if ($external_id && $provider) {
            $curPerson = Dbio::queryRow(
                "SELECT * FROM `people`
                 WHERE `external_id` = '{$external_id}'
                 AND   `provider`    =  '{$provider}';"
            );
            return $raw ? $curPerson : $this->pack($curPerson);
        }
        return null;
    }


    public function validate($person) {
        if (!$person || !is_array($person)) {
            return ['error' => 'invalid_person'];
        }
        $result = [];

        $person['screen_name'] = @trim($person['screen_name']);
        if (!Core::lenLimit($person['screen_name'], 3, 14)) {
            return ['error' => 'invalid_screen_name'];
        }
        $result['screen_name'] = DBio::escape($person['screen_name']);

        if (isset($person['name'])) {
            $person['name'] = @trim($person['name']);
            if (!Core::lenLimit($person['name'], 1, 21)) {
                return ['error' => 'invalid_name'];
            }
        } else {
            $result['name'] = $result['screen_name'];
        }
        $result['name'] = DBio::escape($person['name']);

        if (isset($person['description'])) {
            $person['description'] = @trim($person['description']);
            if (!Core::lenLimit($person['description'], 0, 140)) {
                return ['error' => 'invalid_name'];
            }
        } else {
            $result['description'] = '';
        }
        $result['description'] = Dbio::escape($person['description']);

        if (isset($person['avatar'])) {
            $result['avatar'] = Dbio::escape(@trim($person['avatar']));
        } else {
            $result['avatar'] = '';
        }

        // @todo: improved timezone supporting
        $result['timezone'] = 'Asia/Shanghai';

        // @todo: improved locale supporting
        $result['locale'] = 'zh_cn';

        $person['provider'] = strtolower(trim($person['provider']));
        switch ($person['provider']) {
            case 'email':
                $result['provider'] = $person['provider'];
                $person['external_id'] = $this->validateEmail($person['external_id']);
                if ($person['external_id']) {
                    $result['external_id'] = DBio::escape($person['external_id']);
                } else {
                    return ['error' => 'invalid_external_id'];
                }
                break;
            default:
                return ['error' => 'invalid_provider'];
        }

        if (@Core::lenLimit($person['password'], 4, 256)) {
            $result['password'] = $person['password'];
        } else {
            return ['error' => 'invalid_password'];
        }

        return ['person' => $result];
    }


    public function create($person) {
        $vldResult = $this->validate($person);
        if (@$vldResult['error']) {
            return $vldResult;
        }
        $person = $vldResult['person'];
        if ($this->getByScreenName($person['screen_name'], true)) {
            return ['error' => 'duplicate_screen_name'];
        }
        if ($this->getByExternalIdAndProvider(
            $person['external_id'], $person['provider'], true
        )) {
            return ['error' => 'duplicate_person'];
        }
        $salt      = Core::randString();
        $password  = $this->encryptPassword($person['password'], $salt);
        $rawResult = Dbio::execute(
            "INSERT INTO `people` SET
             `screen_name` = '{$person['screen_name']}',
             `name`        = '{$person['name']}',
             `description` = '{$person['description']}',
             `avatar`      = '{$person['avatar']}',
             `timezone`    = '{$person['timezone']}',
             `locale`      = '{$person['locale']}',
             `provider`    = '{$person['provider']}',
             `external_id` = '{$person['external_id']}',
             `password`    = '{$password}',
             `salt`        = '{$salt}',
             `updated_at`  = NOW();"
        );
        return $this->packInserted($rawResult, 'person');
    }


    public function sigininByScreenNameAndPassword($screen_name, $password) {
        $person = $this->getByScreenName($screen_name, true);
        $chkPassword = $this->checkPassword($person, $password);
        if (@$chkPassword['error']) {
            return $chkPassword;
        }
        return $this->signin($person['id']);
    }


    public function sigininByExternalIdAndProviderAndPassword(
        $external_id, $provider, $password
    ) {
        $person = $this->getByExternalIdAndProvider(
            $external_id, $provider, true
        );
        $chkPassword = $this->checkPassword($person, $password);
        if (@$chkPassword['error']) {
            return $chkPassword;
        }
        return $this->signin($person['id']);
    }


    public function signin($id) {
        global $env;
        $person = $this->getById($id, true);
        if ($person) {
            $mdlToken = new MdlToken();
            $tkResult = $mdlToken->create(
                $person['id'], '', 'person',
                ['person_id' => $person['id'], 'category' => 'person'],
                '', [], $env['person_token_expires_in']
            );
            if (@$tkResult['error']) {
                return $tkResult;
            }
            return ['authorization' => [
                'token'      => $tkResult['token']['code'],
                'person_id'  => $tkResult['token']['person_id'],
                'category'   => $tkResult['token']['category'],
                'scope'      => $tkResult['token']['scope'],
                'created_at' => $tkResult['token']['created_at'],
                'expires_at' => $tkResult['token']['expires_at'],
                'class'      => 'authorization',
            ]];
        }
        return ['error' => 'server_error'];
    }

}
