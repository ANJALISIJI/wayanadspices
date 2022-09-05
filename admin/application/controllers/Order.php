<?php

require APPPATH . '/libraries/BaseController.php';
class Order extends BaseController
{
    public function listOrder()
    {
        $data['orderlist'] = True;
        $this->loadViews("order/orderlist", $data);
    }
}
