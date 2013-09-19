<?php

class CtlNodes extends Controller {

    public function actPlant() {
        if (!($inputs = $this->getInputs([
            'node' => ['json', '#']
        ]))) {
            return;
        }
        @$inputs['node']['who_id'] = $this->token['person_id'];
        $libNode = new LibNode();
        $pltResult = $libNode->create($inputs['node']);
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
        $libNode = new LibNode();
        $nodes = $libNode->getByPersonId($this->token['person_id']);
        if ($nodes === null) {
            $this->jsonError(500);
        }
        $this->jsonResponse($nodes);
    }

}

