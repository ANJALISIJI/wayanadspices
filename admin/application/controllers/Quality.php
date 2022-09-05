<?php

require APPPATH . '/libraries/BaseController.php';
class Quality extends BaseController
{
    public function addQuality()
    {
        $data['addquality'] = True;
        $this->loadViews("quality/quality", $data);
    }
}
