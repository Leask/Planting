<?php

class ctlPeople extends Controller {

    public function actSignin() {
        $inputs = $this->getInputs([
            'user_name' => ['get', 'int'],
        ]);
        print_r($inputs);
    }


    public function actSignup() {
        $inputs = $this->getInputs([
            'user_name' => ['get', 'int'],
        ]);
    }

}
