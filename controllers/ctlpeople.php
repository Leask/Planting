<?php

class CtlPeople extends Controller {

    public function actSignin() {
        $inputs = $this->getInputs([
            'user_name' => ['get', 'int'],
        ]);
        print_r($inputs);
    }


    public function actSignup() {
        $inputs = $this->getInputs(['person' => ['json', '#']]);
        $libPeople = new LibPeople();
        $vldResult = $libPeople->create($inputs['person']);
        if (@$vldResult['error']) {
            $this->jsonError(400, $vldResult['error']);
            return;
        }
        $this->jsonResponse($vldResult['person']);
    }

}
