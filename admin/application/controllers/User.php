<?php

require APPPATH . '/libraries/BaseController.php';
class User extends BaseController
{
    public function listUsers()
    {
        $data['userlist'] = True;
        $this->loadViews("user/userlist", $data);
    }
}
