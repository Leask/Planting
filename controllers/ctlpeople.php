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


    public function actMe() {
        $person = MdlPerson::getById($this->token['person_id']);
        if ($person) {
            $this->jsonResponse($person);
            return;
        }
        $this->jsonError(404, 'person_not_found');
    }


    public function actGet() {

    }

}
