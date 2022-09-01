<?php

class ClientModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->trainerId =  $this->session->userdata('userId');
    }
    public function checkEmailExist($email)
    {
        $exist = $this->db->select('email')->where('email', $email)->get($this->db->dbprefix('ptclients'))->row();
        return isset($exist->email) ? $exist->email : '';
    }

    public function addNewClient($data)
    {
        $data['customer_id'] = 0;
        $data['status'] = 1;
        $data['is_deleted'] = 0;
        if ($this->checkEmailExist($data['email'])) {
            $this->session->set_flashdata('danger', 'Client already added.');
            redirect('manage_client');
        } else {
            $customer = $this->db->select('email,customer_id')->where('email', $data['email'])->get($this->db->dbprefix('customers'))->row();
            if ($customer) {
                $this->db->set('user_role', 'PTCLIENT')
                    ->where('customer_id ', $customer->customer_id)
                    ->update($this->db->dbprefix('customers'));
                $data['customer_id'] = $customer->customer_id;
                $this->db->insert($this->db->dbprefix('ptclients'), $data);
                return $data;
            } else {
                $data['date_added'] = date('Y-m-d h:i:s');
                $this->db->insert($this->db->dbprefix('ptclients'), $data);
                return $data;
            }
        }
    }
    public function listClients()
    {
        $clients =  $this->db->select('cl.customer_id,cl.pt_client_id as Id,cl.first_name,cl.last_name,cl.email,cl.date_added')
            ->from($this->db->dbprefix('ptclients as cl'))
            ->where('cl.trainer_id', $this->trainerId)
            ->where('cl.status', 1)
            ->where('cl.is_deleted', 0)
            ->get()->result();

        $data = [];
        if ($clients) {
            foreach ($clients as $key => $client) {
                $data[$key]['Id'] =  $client->Id;
                $data[$key]['first_name'] =  $client->first_name;
                $data[$key]['last_name'] =  $client->last_name;
                $data[$key]['email'] =  $client->email;
                $data[$key]['date_added'] =  $client->date_added;
                $data[$key]['customer_status'] = $this->checkCustomerActive($client->customer_id);
            }
        }
        return $data;
    }
    // checking client signed up or not
    protected function checkCustomerActive($id)
    {
        $status = $this->db->select('customer_id')->where('customer_id', $id)->get($this->db->dbprefix('customers'))->row();
        if ($status) {
            return True;
        } else {
            return False;
        }
    }
    public function updateClient($data, $id)
    {
        $this->db->where('pt_client_id', $id)->update($this->db->dbprefix('ptclients'), $data);
    }
    public function deleteClient($id)
    {
        $ptClient = $this->db->select('customer_id')->where('pt_client_id', $id)->get($this->db->dbprefix('ptclients'))->row();
        $this->db->where('pt_client_id', $id)->set('status', 0)->set('is_deleted', 1)->update($this->db->dbprefix('ptclients'));
        if ($ptClient->customer_id > 0) {
            $this->db->where('customer_id', $ptClient->customer_id)->set('user_role', strtoupper('user'))->update($this->db->dbprefix('customers'));
        }
    }
}
