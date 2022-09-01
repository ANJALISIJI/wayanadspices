<?php

require APPPATH . '/libraries/BaseController.php';
class Home extends BaseController
{
    public function index()
    {
        $data['dashboard'] = 'True';
        $this->loadViews("home", $data);

        
    }
}
