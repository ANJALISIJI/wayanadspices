<?php

require APPPATH . '/libraries/BaseController.php';
class Subcategory extends BaseController
{
    public function addSubcategory()
    {
        $data['addsubcategory'] = True;
        $this->loadViews("subcategory/addsub", $data);
    }
}
