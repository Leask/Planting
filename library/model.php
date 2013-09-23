<?php

abstract class Model {

    static function getStatusByStatusIdx($staticIdx) {
        return @$statuses[(int) $staticIdx];
    }


    static function getStatusIdxByStatus($status) {
        $statusIdx = array_search(strtolower(trim($status)), self::$statuses);
        return $statusIdx === false ? null : $statusIdx;
    }

}
