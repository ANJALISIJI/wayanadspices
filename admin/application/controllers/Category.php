<?php

require APPPATH . '/libraries/BaseController.php';
class Category extends BaseController
{
    public function addCategory()
    {
        $data['addcategory'] = True;
        $this->loadViews("category/add", $data);
    }
}
