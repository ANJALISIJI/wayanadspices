<?php

require APPPATH . '/libraries/BaseController.php';
class Wholesaler extends BaseController
{
    public function listWholesaler()
    {
        $data['wholesalerlist'] = True;
        $this->loadViews("wholesaler/wholesalerlist", $data);
    }
}
