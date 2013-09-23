<?php

class MdlNode extends model {

    protected $statuses = ['normal', 'deleted'];


    protected function pack($rawNode) {
        $mdlPerson = new MdlPerson();
        return $rawNode ? [
            'id'       => (int) $rawNode['id'],
            'when'     => Core::dbTimeToIsoTime($rawNode['when']),
            'what'     => $rawNode['what'],
            'who'      => $mdlPerson->getById($rawNode['created_by']),
            'reply_to' => null,
            'caring'   => [],
            'class'    => 'node',
        ] : null;
    }


    public function validate($node) {
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
        $result['what'] = Dbio::escape($node['what']);

        $node['when_timestamp'] = strtotime($node['when']);
        if ($env['now'] > $node['when_timestamp']) {
            return ['error' => 'invalid_when'];
        }
        $result['when_timestamp'] = $node['when_timestamp'];

        return ['node' => $result];
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
            return $this->pack($rawNode);
        }
        return null;
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
        return $this->packInserted($rawResult, 'node');
    }


    public function getByPersonId($person_id) {
        $person_id = (int) $person_id;
        if ($person_id) {
            $statusIdx = $this->getStatusIdxByStatus('normal');
            $rawNodes  = Dbio::query(
                "SELECT * FROM `nodes`
                 WHERE `created_by` = {$person_id}
                 AND   `status`     = {$statusIdx}
                 ORDER BY `when` DESC;"
            );
            return $this->multiPack($rawNodes);
        }
        return null;
    }

}
