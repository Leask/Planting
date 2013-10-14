<?php

class CtlCaring extends Controller {

    public function actCreate() {
        if (!($inputs = $this->getInputs([
            'node_id' => ['json']
        ]))) {
            return;
        }
        $mdlCaring = new MdlCaring();
        $rawResult = $mdlCaring->create(
            $this->token['person_id'], $inputs['node_id']
        );
        if (@$rawResult['error']) {
            $this->jsonError(
                $rawResult['error'] === 'server_error' ? 500 : 400,
                $rawResult['error']
            );
            return;
        } else if (@$rawResult['warning']) {
            $this->jsonResponse($rawResult['caring'], 206, [
                'code' => 206, 'type' => $rawResult['warning'], 'message' => ''
            ]);
            return;
        }
        $this->jsonResponse($rawResult['caring']);
    }


    public function actDestroy() {
        if (!($inputs = $this->getInputs([
            'node_id' => ['json']
        ]))) {
            return;
        }
        $mdlCaring = new MdlCaring();
        $rawResult = $mdlCaring->destroy(
            $this->token['person_id'], $inputs['node_id']
        );
        if (@$rawResult['error']) {
            $code = 400;
            switch ($rawResult['error']) {
                case 'server_error':
                    $code = 500;
                    break;
                case 'caring_not_found':
                    $code = 404;
            }
            $this->jsonError($code, $rawResult['error']);
            return;
        }
        $this->jsonResponse($rawResult['caring']);
    }


    public function actCaring() {
        if (!($inputs = $this->getInputs([
            'person_id' => ['get', '']
        ]))) {
            return;
        }
        $person_id = strtolower(trim($inputs['person_id']));
        if ($person_id === 'me') {
            if (!$this->token) {
                $this->jsonError(401, 'authentication_required');
                return;
            }
            $person_id = $this->token['person_id'];
        }
        $mdlCaring = new MdlCaring();
        $nodes = $mdlCaring->getCaringByPersonId($person_id);
        if ($nodes === null) {
            $this->jsonError(500);
            return;
        }
        $this->jsonResponse($nodes);
    }


    public function actPeople() {
        if (!($inputs = $this->getInputs([
            'node_id' => ['get', 'int']
        ]))) {
            return;
        }
        $mdlCaring = new MdlCaring();
        $people = $mdlCaring->getCaringPeopleByNodeId($inputs['node_id']);
        if ($people === null) {
            $this->jsonError('400', 'invalid_node_id');
            return;
        }
        $this->jsonResponse($people);
    }


    // public function actFollowing() {
    //     if (!($inputs = $this->getInputs([
    //         'person_id' => ['get', '']
    //     ]))) {
    //         return;
    //     }
    //     $person_id = strtolower(trim($inputs['person_id']));
    //     if ($person_id === 'me') {
    //         if (!$this->token) {
    //             $this->jsonError(401, 'authentication_required');
    //             return;
    //         }
    //         $person_id = $this->token['person_id'];
    //     }
    //     $mdlFriendship = new MdlFriendship();
    //     $rawResult = $mdlFriendship->getFollowingByPersonId($person_id);
    //     if (@$rawResult['error']) {
    //         $this->jsonError(400, $rawResult['error']);
    //         return;
    //     }
    //     $this->jsonResponse($rawResult['following']);
    // }


    // public function actFollowers() {
    //     if (!($inputs = $this->getInputs([
    //         'person_id' => ['get', '']
    //     ]))) {
    //         return;
    //     }
    //     $person_id = strtolower(trim($inputs['person_id']));
    //     if ($person_id === 'me') {
    //         if (!$this->token) {
    //             $this->jsonError(401, 'authentication_required');
    //             return;
    //         }
    //         $person_id = $this->token['person_id'];
    //     }
    //     $mdlFriendship = new MdlFriendship();
    //     $rawResult = $mdlFriendship->getFollowersByPersonId($person_id);
    //     if (@$rawResult['error']) {
    //         $this->jsonError(400, $rawResult['error']);
    //         return;
    //     }
    //     $this->jsonResponse($rawResult['followers']);
    // }

}
