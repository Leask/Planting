<?php

class MdlFriendship extends model {

    protected $statuses = ['normal', 'deleted'];


    protected function pack($rawFriendship) {
        if ($rawFriendship) {
            $mdlPerson  = new MdlPerson();
            $person     = $mdlPerson->getById($rawFriendship['person_id']);
            $created_by = $mdlPerson->getById($rawFriendship['created_by']);
            if ($person && $created_by) {
                return [
                    'id'         => (int) $rawFriendship['id'],
                    'status'     => $this->getStatusByStatusIdx($rawFriendship['status']),
                    'person'     => $person,
                    'created_by' => $created_by,
                    'created_at' => Core::dbTimeToIsoTime($rawFriendship['created_at']),
                    'updated_at' => Core::dbTimeToIsoTime($rawFriendship['updated_at']),
                    'class'      => 'friendship',
                ];
            }
        }
        return null;
    }


    public function getById($id, $raw = false) {
        return $this->rawGetById('friendships', $id, $raw);
    }


    public function getFollowersByPersonId($person_id) {
        $mdlPerson = new MdlPerson();
        if (!($person_id = (int) $person_id)
         || !$mdlPerson->getById($person_id)) {
            return ['error' => 'invalid_person_id'];
        }
        $statusIdx = $this->getStatusIdxByStatus('normal');
        $rawPeople = Dbio::query(
            "SELECT `p`.*
             FROM   `friendships` AS `f`,
                    `people`      AS `p`
             WHERE  `f`.`person_id`  = {$person_id}
             AND    `f`.`created_by` = `p`.`id`
             AND    `f`.`status`     = {$statusIdx};"
        );
        return ['followers' => $mdlPerson->multiPack($rawPeople)];
    }


    public function getFollowingByPersonId($person_id) {
        $mdlPerson = new MdlPerson();
        if (!($person_id = (int) $person_id)
         || !$mdlPerson->getById($person_id)) {
            return ['error' => 'invalid_person_id'];
        }
        $statusIdx = $this->getStatusIdxByStatus('normal');
        $rawPeople = Dbio::query(
            "SELECT `p`.*
             FROM   `friendships` AS `f`,
                    `people`      AS `p`
             WHERE  `f`.`created_by` = {$person_id}
             AND    `f`.`person_id`  = `p`.`id`
             AND    `f`.`status`     = {$statusIdx};"
        );
        return ['following' => $mdlPerson->multiPack($rawPeople)];
    }


    public function getByCreatedByAndPersonId(
        $created_by, $person_id, $raw = false
    ) {
        $created_by = (int) $created_by;
        $person_id  = (int) $person_id;
        if ($created_by && $person_id) {
            $rawFriendship = Dbio::queryRow(
                "SELECT * FROM `friendships`
                 WHERE `created_by` = {$created_by}
                 AND   `person_id`  = {$person_id};"
            );
            return $raw ? $rawFriendship : $this->pack($rawFriendship);
        }
        return null;
    }


    public function create($created_by, $person_id) {
        $mdlPerson  = new MdlPerson();
        if (!($created_by = (int) $created_by)
         || !$mdlPerson->getById($created_by)) {
            return ['error' => 'invalid_created_by'];
        }
        if (!($person_id  = (int) $person_id)
         || !$mdlPerson->getById($person_id)) {
            return ['error' => 'invalid_person'];
        }
        if ($created_by === $person_id) {
            return ['error' => 'can_not_follow_yourself'];
        }
        $curFriendship = $this->getByCreatedByAndPersonId(
            $created_by, $person_id
        );
        if ($curFriendship) {
            if ($curFriendship['status'] === 'normal') {
                return [
                    'warning'    => 'duplicate_friendship',
                    'friendship' => $curFriendship,
                ];
            }
            $statusIdx = $this->getStatusIdxByStatus('normal');
            $rawResult = Dbio::execute(
                "UPDATE `friendships`
                 SET   `status`     = {$statusIdx},
                       `updated_at` = NOW()
                 WHERE `created_by` = {$created_by}
                 AND   `person_id`  = {$person_id};"
            );
            if (!$rawResult) {
                return ['error' => 'server_error'];
            }
            return ['friendship' => $this->getByCreatedByAndPersonId(
                $created_by, $person_id
            )];
        }
        $rawResult = Dbio::execute(
            "INSERT INTO `friendships` SET
             `created_by` = {$created_by},
             `person_id`  = {$person_id},
             `updated_at` = NOW();"
        );
        return $this->packInserted($rawResult, 'friendship');
    }


    public function destroy($created_by, $person_id) {
        $mdlPerson  = new MdlPerson();
        if (!($created_by = (int) $created_by)
         || !$mdlPerson->getById($created_by)) {
            return ['error' => 'invalid_created_by'];
        }
        if (!($person_id  = (int) $person_id)
         || !$mdlPerson->getById($person_id)) {
            return ['error' => 'invalid_person'];
        }
        if ($created_by === $person_id) {
            return ['error' => 'can_not_unfollow_yourself'];
        }
        $curFriendship = $this->getByCreatedByAndPersonId(
            $created_by, $person_id
        );
        if ($curFriendship && $curFriendship['status'] !== 'deleted') {
            $statusIdx = $this->getStatusIdxByStatus('deleted');
            $rawResult = Dbio::execute(
                "UPDATE `friendships`
                 SET   `status`     = {$statusIdx},
                       `updated_at` = NOW()
                 WHERE `created_by` = {$created_by}
                 AND   `person_id`  = {$person_id};"
            );
            if (!$rawResult) {
                return ['error' => 'server_error'];
            }
            return ['friendship' => $this->getByCreatedByAndPersonId(
                $created_by, $person_id
            )];
        }
        return ['error' => 'friendship_not_found'];
    }

}
