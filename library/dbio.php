<?php

class Dbio {

    static $dbRes = null;


    static function escape($string) {
        return str_replace(
            ["\\"  , "\0" , "\n" , "\r" , "\x1a", "'" , '"' ],
            ["\\\\", "\\0", "\\n", "\\r", "\Z"  , "\'", '\"'],
            $string
        );
    }


    static function connect() {
        if (self::$dbRes) {
            return self::$dbRes;
        }
        global $env;
        $res = new mysqli('p:'
          . $env['mysql']['server_host'],
            $env['mysql']['user_name'],
            $env['mysql']['password'],
            $env['mysql']['database'],
            $env['mysql']['port']
        );
        if (!($error = mysqli_connect_error())) {
            $res->set_charset('utf8mb4');
            return (self::$dbRes = $res);
        }
        Core::log("Database error: {$error}");
        return null;
    }


    static function execute($sql) {
        $dbRes = self::connect();
        if ((@$rawResult = $dbRes->query($sql))) {
            return [
                'native'        => $rawResult,
                'affected_rows' => $dbRes->affected_rows,
                'insert_id'     => $dbRes->insert_id,
            ];
        }
        Core::log("Database error: {$dbRes->error} / SQL statement: {$sql}");
        return null;
    }


    static function query($sql) {
        if ((@$rawResult = self::execute($sql))) {
            $result = [];
            while (($row = $rawResult['native']->fetch_assoc())) {
                $result[] = $row;
            }
            return $result;
        }
        return null;
    }


    static function queryRow($sql) {
        if ((@$rawResult = self::execute($sql))) {
            return $rawResult['native']->fetch_assoc();
        }
        return null;
    }


    static function queryColumn($sql) {
        if ((@$rawResult = self::execute($sql))) {
            $result = [];
            while (($row = $rawResult['native']->fetch_row())) {
                $result[] = $row[0];
            }
            return $result;
        }
        return null;
    }


    static function queryCell($str) {
        if ((@$rawResult = self::execute($sql))) {
            return ($row = $rawResult['native']->fetch_row()) ? $row[0] : null;
        }
        return null;
    }

}
