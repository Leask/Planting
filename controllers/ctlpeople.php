<?php

class CtlPeople extends Controller {

    public function actSignin() {
        if (!($inputs = $this->getInputs([
            'screen_name' => ['json'],
            'external_id' => ['json'],
            'provider'    => ['json'],
            'password'    => ['json'],
        ]))) {
            return;
        }
        $mdlPerson = new MdlPerson();
        if ($inputs['screen_name']) {
            $sinResult = $mdlPerson->sigininByScreenNameAndPassword(
                $inputs['screen_name'], $inputs['password']
            );
        } else if ($inputs['external_id'] && $inputs['provider']) {
            $sinResult = $mdlPerson->sigininByExternalIdAndProviderAndPassword(
                $inputs['external_id'],
                $inputs['provider'],
                $inputs['password']
            );
        } else {
            $this->jsonError(400, 'invalid_signin_infos');
            return;
        }
        if (@$sinResult['error']) {
            $this->jsonError(
                $sinResult['error'] === 'server_error' ? 500 : 401,
                $sinResult['error']
            );
            return;
        }
        $this->jsonResponse($sinResult['authorization']);
    }


    public function actSignup() {
        if (!($inputs = $this->getInputs([
            'person' => ['json', '#']
        ]))) {
            return;
        }
        $mdlPerson = new MdlPerson();
        $supResult = $mdlPerson->create($inputs['person']);
        if (@$supResult['error']) {
            $this->jsonError(
                $supResult['error'] === 'server_error' ? 500 : 400,
                $supResult['error']
            );
            return;
        }
        $this->jsonResponse($supResult['person']);
    }


    public function actShow() {
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
        $mdlPerson = new MdlPerson();
        if (($person = $mdlPerson->getById($person_id))) {
            $this->jsonResponse($person);
            return;
        }
        $this->jsonError(404, 'person_not_found');
    }

}
