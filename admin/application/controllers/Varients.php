<?php

require APPPATH . '/libraries/BaseController.php';
class Varients extends BaseController
{
    public function addVarient()
    {
        $data['addvarient'] = True;
        $this->loadViews("varients/varients", $data);
    }
}
