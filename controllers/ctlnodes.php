<?php

class CtlNodes extends Controller {

    public function actPlant() {
        if (!($inputs = $this->getInputs([
            'node' => ['json', '#']
        ]))) {
            return;
        }
        @$inputs['node']['who_id'] = $this->token['person_id'];
        $mdlNode = new MdlNode();
        $pltResult = $mdlNode->create($inputs['node']);
        if (@$pltResult['error']) {
            $this->jsonError(
                $pltResult['error'] === 'server_error' ? 500 : 400,
                $pltResult['error']
            );
            return;
        }
        $this->jsonResponse($pltResult['node']);
    }


    public function actHome() {
        $mdlNode = new MdlNode();
        $nodes = $mdlNode->getByPersonId($this->token['person_id']);
        if ($nodes === null) {
            $this->jsonError(500);
        }
        $this->jsonResponse($nodes);
    }

}

