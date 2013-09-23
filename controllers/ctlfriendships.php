<?php

class CtlFriendships extends Controller {

    public function actCreate() {
        if (!($inputs = $this->getInputs(['person_id' => ['json']]))) {
            return;
        }
        $mdlFriendship = new MdlFriendship();
        $rawResult = $mdlFriendship->create(
            $this->token['person_id'], $inputs['person_id']
        );
        if (@$rawResult['error']) {
            $this->jsonError(
                $rawResult['error'] === 'server_error' ? 500 : 400,
                $rawResult['error']
            );
            return;
        } else if (@$rawResult['warning']) {
            $this->jsonResponse($rawResult['friendship'], 206, [
                'code' => 206, 'type' => $rawResult['warning'], 'message' => ''
            ]);
            return;
        }
        $this->jsonResponse($rawResult['friendship']);
    }


    public function actDestroy() {
        if (!($inputs = $this->getInputs(['person_id' => ['json']]))) {
            return;
        }
        $mdlFriendship = new MdlFriendship();
        $rawResult = $mdlFriendship->destroy(
            $this->token['person_id'], $inputs['person_id']
        );
        if (@$rawResult['error']) {
            $code = 400;
            switch ($rawResult['error']) {
                case 'server_error':
                    $code = 500;
                    break;
                case 'friendship_not_found':
                    $code = 404;
            }
            $this->jsonError($code, $rawResult['error']);
            return;
        }
        $this->jsonResponse($rawResult['friendship']);
    }

}
