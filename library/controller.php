<?php

abstract class Controller {

    protected $uri      = '';

    protected $timezone = '';

    protected $local    = '';

    protected $httpStatus = [
        206 => 'Partial Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
    ];


    protected function jsonHeader() {
        header('Content-Type: application/json; charset=UTF-8');
    }


    protected function changeHttpStatus($code) {
        if (isset($this->httpStatus[$code])) {
            header("HTTP/1.1 {$code} {$this->httpStatus[$code]}");
            return true;
        }
        return false;
    }


    protected function jsonResponse($data, $code = 200, $warning = null) {
        $this->jsonHeader();
        if ($code !== 200 && !$this->changeHttpStatus($code)) {
            return;
        }
        $output = new stdClass;
        if ($data) {
            $output->data    = $data;
        }
        if ($warning) {
            $output->warning = $warning;
        }
        echo json_encode($output);
    }


    protected function jsonError($code, $type = '', $message = '', $data = null) {
        $this->jsonHeader();
        if (!$this->changeHttpStatus($code)) {
            return;
        }
        if ($code === 500) {
            $type = $type ?: 'server_error';
        }
        if (!$code || !$type) {
            return;
        }
        $output = new stdClass;
        $output->error = [
            'code'    => $code,
            'type'    => $type,
            'message' => $message,
        ];
        if ($data) {
            $output->data = $data;
        }
        echo json_encode($output);
    }


    protected function validateInput($value, $pattern) {

    }


    protected function getInputs($rules) {
        $json   = null;
        $inputs = [];
        foreach ($rules as $rI => $rItem) {
            switch ($rItem[0]) {
                case 'get':
                    $value = @$_GET[$rI];
                    break;
                case 'post':
                    $value = @$_POST[$rI];
                    break;
                case 'json':
                    $json = $json ?: json_decode(Core::getRequestBody());
                    if ($json === null) {
                        $this->jsonError(400, 'invalid_json');
                        return null;
                    }
                    if (@$rItem[1] === '@') {
                        $value = $json;
                    } else if (@$rItem[1] === '#') {
                        $value = (array) $json;
                    } else if (isset($json->$rI)) {
                        $value = $json->$rI;
                    } else {
                        $value = null;
                    }
            }
            switch (@$rItem[1]) {
                case 'int':
                    $value = (int) $value;
                    break;
                case 'number':
                    break;
                case 'screen_name': // 3 - 15
                    break;
                case 'name':        // 2 - 20
                    break;
                case 'email':
                    break;
                case 'password':
                    break;
                case 'string_140':
                    break;
            }
            $inputs[$rI] = $value;
        }
        return $inputs;
    }

}
