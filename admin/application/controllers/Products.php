<?php

require APPPATH . '/libraries/BaseController.php';
class Products extends BaseController
{
    public function productList()
    {
        $data['products'] =True;
        $data['productList'] =True;
        $this->loadViews("products/productlist", $data);
    }
    public function addProduct()
    {
        $data['products'] =True;
        $data['addProduct'] =True;
        $this->loadViews("products/addproduct", $data);
    }
}
