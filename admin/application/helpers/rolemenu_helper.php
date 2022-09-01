<?php
defined('BASEPATH') or exit('No direct script access allowed');


function get_role_base_menu()
{

    $ci = &get_instance();
    $ci->load->model('Login_model');
    return $ci->Login_model->getChildMenus($ci->session->userdata('roleText'));
}
