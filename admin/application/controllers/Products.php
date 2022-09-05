<?php

require APPPATH . '/libraries/BaseController.php';
class Products extends BaseController
{
    public function productList()
    {
        $data['productlist'] =True;
        $this->loadViews("products/productlist", $data);
    }
    public function addProduct()
    {
        $data['addproduct'] =True;
        $this->loadViews("products/addproduct", $data);
    }
}
