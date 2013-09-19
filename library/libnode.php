<?php

class LibNode {

    static $statuses = ['normal', 'deleted'];


    static function getStatusByStatusIdx($staticIdx) {
        return @$statuses[(int) $staticIdx];
    }


    static function getStatusIdxByStatus($status) {
        $statusIdx = array_search(strtolower(trim($status)), self::$statuses);
        return $statusIdx === false ? null : $statusIdx;
    }


    static function pack($rawNode) {
        return $rawNode ? [
            'id'       => (int) $rawNode['id'],
            'when'     => Core::dbTimeToIsoTime($rawNode['when']),
            'what'     => $rawNode['what'],
            'who'      => LibPerson::getById($rawNode['created_by']),
            'reply_to' => null,
            'caring'   => [],
            'class'    => 'node',
        ] : null;
    }


    static function multiPack($rawNodes) {
        if (is_array($rawNodes)) {
            $nodes = [];
            foreach ($rawNodes as $rawNode) {
                if (($node = self::pack($rawNode))) {
                    $nodes[] = $node;
                }
            }
            return $nodes;
        }
        return null;
    }


    static function validate($node) {
        global $env;
        if (!$node || !is_array($node)) {
            return ['error' => 'invalid_node'];
        }
        $result = [];

        if (!($node['who_id'] = (int) $node['who_id'])) {
            return ['error' => 'invalid_who'];
        }
        $result['who_id'] = $node['who_id'];

        $node['what'] = @trim($node['what']);
        if (!Core::lenLimit($node['what'], 1, 140)) {
            return ['error' => 'invalid_what'];
        }
        $result['what'] = DBio::escape($node['what']);

        $node['when_timestamp'] = strtotime($node['when']);
        if ($env['now'] > $node['when_timestamp']) {
            return ['error' => 'invalid_when'];
        }
        $result['when_timestamp'] = $node['when_timestamp'];

        return ['node' => $result];
    }


    public function create($node) {
        $vldResult = $this->validate($node);
        if (@$vldResult['error']) {
            return $vldResult;
        }
        $node = $vldResult['node'];
        $rawResult = Dbio::execute(
            "INSERT INTO `nodes` SET
             `when`       =  FROM_UNIXTIME({$node['when_timestamp']}),
             `what`       = '{$node['what']}',
             `created_by` =  {$node['who_id']},
             `updated_at` =  NOW();"
        );
        if ($rawResult) {
            if (($node = self::getById($rawResult['insert_id']))) {
                return ['node' => $node];
            }
        }
        return ['error' => 'server_error'];
    }


    public function getById($id) {
        $id = (int) $id;
        if ($id) {
            $statusIdx = $this->getStatusIdxByStatus('normal');
            $rawNode   = Dbio::queryRow(
                "SELECT * FROM `nodes`
                 WHERE `id`     = {$id}
                 AND   `status` = {$statusIdx};"
            );
            return self::pack($rawNode);
        }
        return null;
    }


    public function getByPersonId($person_id) {
        $person_id = (int) $person_id;
        if ($person_id) {
            $statusIdx = $this->getStatusIdxByStatus('normal');
            $rawNodes  = Dbio::query(
                "SELECT * FROM `nodes`
                 WHERE `created_by` = {$person_id}
                 AND   `status`     = {$statusIdx};"
            );
            return self::multiPack($rawNodes);
        }
        return null;
    }

}
