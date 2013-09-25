<?php

class MdlCaring extends model {

    protected $statuses = ['normal', 'deleted'];


    protected function pack($rawCaring) {
        if ($rawCaring) {
            $mdlNode    = new MdlNode();
            $mdlPerson  = new MdlPerson();
            $node       = $mdlNode->getById($rawCaring['node_id']);
            $created_by = $mdlPerson->getById($rawCaring['created_by']);
            if ($node && $created_by) {
                return [
                    'id'         => (int) $rawCaring['id'],
                    'status'     => $this->getStatusByStatusIdx($rawCaring['status']),
                    'node'       => $node,
                    'created_by' => $created_by,
                    'created_at' => Core::dbTimeToIsoTime($rawCaring['created_at']),
                    'updated_at' => Core::dbTimeToIsoTime($rawCaring['updated_at']),
                    'class'      => 'caring',
                ];
            }
        }
        return null;
    }


    public function getById($id, $raw = false) {
        return $this->rawGetById('caring', $id, $raw);
    }


    public function getByCreatedByAndNodeId(
        $created_by, $node_id, $raw = false
    ) {
        $created_by = (int) $created_by;
        $node_id    = (int) $node_id;
        if ($created_by && $node_id) {
            $rawCaring = Dbio::queryRow(
                "SELECT * FROM `caring`
                 WHERE `created_by` = {$created_by}
                 AND   `node_id`    = {$node_id};"
            );
            return $raw ? $rawCaring : $this->pack($rawCaring);
        }
        return null;
    }


    public function create($created_by, $node_id) {
        $mdlPerson = new MdlPerson();
        $mdlNode   = new MdlNode();
        if (!($created_by = (int) $created_by)
         || !$mdlPerson->getById($created_by)) {
            return ['error' => 'invalid_created_by'];
        }
        if (!($node_id    = (int) $node_id)
         || !$mdlNode->getById($node_id)) {
            return ['error' => 'invalid_node'];
        }
        $curCaring = $this->getByCreatedByAndNodeId($created_by, $node_id);
        if ($curCaring) {
            if ($curCaring['status'] === 'normal') {
                return [
                    'warning' => 'duplicate_caring',
                    'caring'  => $curCaring,
                ];
            }
            $statusIdx = $this->getStatusIdxByStatus('normal');
            $rawResult = Dbio::execute(
                "UPDATE `caring`
                 SET    `status`     = {$statusIdx},
                        `updated_at` = NOW()
                 WHERE  `created_by` = {$created_by}
                 AND    `node_id`    = {$node_id};"
            );
            if (!$rawResult) {
                return ['error' => 'server_error'];
            }
            return ['caring' => $this->getByCreatedByAndNodeId(
                $created_by, $node_id
            )];
        }
        $rawResult = Dbio::execute(
            "INSERT INTO `caring` SET
             `created_by` = {$created_by},
             `node_id`    = {$node_id},
             `updated_at` = NOW();"
        );
        return $this->packInserted($rawResult, 'caring');
    }


    public function destroy($created_by, $node_id) {
        $mdlPerson = new MdlPerson();
        $mdlNode   = new MdlNode();
        if (!($created_by = (int) $created_by)
         || !$mdlPerson->getById($created_by)) {
            return ['error' => 'invalid_created_by'];
        }
        if (!($node_id    = (int) $node_id)
         || !$mdlNode->getById($node_id)) {
            return ['error' => 'invalid_person'];
        }
        $curCaring = $this->getByCreatedByAndNodeId($created_by, $node_id);
        if ($curCaring && $curCaring['status'] !== 'deleted') {
            $statusIdx = $this->getStatusIdxByStatus('deleted');
            $rawResult = Dbio::execute(
                "UPDATE `caring`
                 SET    `status`     = {$statusIdx},
                        `updated_at` = NOW()
                 WHERE  `created_by` = {$created_by}
                 AND    `node_id`    = {$node_id};"
            );
            if (!$rawResult) {
                return ['error' => 'server_error'];
            }
            return ['caring' => $this->getByCreatedByAndNodeId(
                $created_by, $node_id
            )];
        }
        return ['error' => 'caring_not_found'];
    }

}
