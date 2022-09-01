<?php

require APPPATH . '/libraries/BaseController.php';
class Category extends BaseController
{
    public function addCategory()
    {
        $data['category'] = True;
        $data['addCategory'] = True;
        $this->loadViews("category/add", $data);
    }
}
