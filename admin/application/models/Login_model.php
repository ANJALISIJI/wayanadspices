<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Login_model (Login Model)
 * Login model class to get to authenticate user credentials 
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Login_model extends CI_Model
{

    /**
     * This function used to check the login credentials of the user
     * @param string $email : This is email of the user
     * @param string $password : This is encrypted password of the user
     */
    function loginClient($email, $password)
    {

        $this->db->select('customer_id as userId,email,password,first_name as name')->from($this->db->dbprefix('customers'));
        $this->db->where('email', strtolower($email));
        $this->db->where('status', 1);
        $this->db->where('is_deleted', 0);
        $user =   $this->db->where('password', 'SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1("' . $password . '")))))', FALSE)->get()->row();

        if (!empty($user)) {
            return $user;
        } else {

            return array();
        }
    }
    function loginMe($email, $password)
    {
        $this->db->select('trainer_id  as userId,email,password,first_name as name,status');
        $this->db->from($this->db->dbprefix('trainers'));
        $this->db->where('email', strtolower($email));
        $trainer =  $this->db->where('password', 'SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1("' . $password . '")))))', FALSE)->get()->row();
        if (!empty($trainer)) {
            //checking HQ approved or not
            if ($trainer->status == 0) {
                $this->session->set_flashdata('danger', 'Registration under approval');
                return  redirect('/');
            }
            return $trainer;
        } else {
            return array();
        }
    }
    public function checkClientEmailExist($email)
    {
        return $this->db
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->where('email', $email)
            ->get($this->db->dbprefix('ptclients'))
            ->row();
    }
    // adding new trainer 
    public function addNewTrainer($data)
    {
        if ($this->check_trainer_email_exists($data['email']) > 0) {
            $this->session->set_flashdata('danger', 'Failed to signup , email already registered please login.');
            redirect('trainersignup');
        } else {
            $salt = substr(md5(uniqid(rand(), TRUE)), 0, 9);
            $password = sha1($salt . sha1($salt . sha1($data['password'])));
            $data['salt'] = $salt;
            $data['password'] = $password;
            $this->db->insert($this->db->dbprefix('trainers'), $data);
            $this->session->set_flashdata('success', 'Sign up successful');
            redirect('/');
        }
    }
    protected function check_trainer_email_exists($email)
    {
        $this->db->select('trainer_id');
        $this->db->from($this->db->dbprefix('trainers'));
        $this->db->where('email', $email);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function addNewClient($data)
    {

        //checking email exist if exist do update else create

        if ($this->checkClientEmailExist($data['email'])) {
            $clientRow = $this->checkClientEmailExist($data['email']);
            if ($clientRow->customer_id > 0) {
                $this->session->set_flashdata('danger', 'Please login , email already registered with us');
                return  redirect('clientsignup');
                return true;
            }
            $customer = $this->db->select('email,customer_id')->where('email', $data['email'])->get($this->db->dbprefix('customers'))->row();
            if ($customer) {
                $this->db->set('user_role', 'PTCLIENT')
                    ->where('customer_id ', $customer->customer_id)
                    ->update($this->db->dbprefix('customers'));
                $data['customer_id'] = $customer->customer_id;
                $this->db->set('customer_id', $customer->customer_id)->where('email', $data['email'])->update($this->db->dbprefix('ptclients'), $data);
                $this->session->set_flashdata('success', 'Sign up successful');
                redirect('clientlogin');
            } else {
                $data['user_role'] = strtoupper('PTCLIENT');
                $data['status'] = 1;
                $data['profile_picture'] = 'null';
                $data['cart'] = 0;
                $data['reward_points'] = 0;
                $data['telephone'] = 0;
                $data['newsletter'] = 0;
                $data['address_id'] = 0;
                $data['security_question_id'] = 0;
                $data['security_answer'] = 0;
                $data['profile_image'] = 0;
                $data['customer_group_id'] = 0;
                $salt = substr(md5(uniqid(rand(), TRUE)), 0, 9);
                $password = sha1($salt . sha1($salt . sha1($data['password'])));
                $data['salt'] = $salt;
                $data['password'] = $password;
                $data['ip_address'] = $this->input->ip_address();
                $data['date_added'] = date('Y-m-d h:i:s');
                $data['added_by'] = 0;
                $this->db->insert($this->db->dbprefix('customers'), $data);
                $this->db->set('customer_id', $this->db->insert_id())->where('email', $data['email'])->update($this->db->dbprefix('ptclients'));
                $this->session->set_flashdata('success', 'Sign up successful');
                redirect('clientlogin');
            }
        } else {
            $this->session->set_flashdata('danger', 'Failed to signup , email not registered with us');
            redirect('clientsignup');
        }
    }

    /**
     * This function used to check email exists or not
     * @param {string} $email : This is users email id
     * @return {boolean} $result : TRUE/FALSE
     */
    function checkEmailExist($email, $role = 'trainer')
    {
        if ($role == strtoupper('trainer')) {
            $exist = $this->db->select('trainer_id as Id,first_name as first_name,last_name as last_name,email as email')->where('email', $email)->get($this->db->dbprefix('trainers'))->row_array();
        } else {
            $exist = $this->db->select('ptclients.customer_id as Id,first_name as first_name,last_name as last_name,email as email')
                ->where('is_deleted', 0)
                ->where('status', 1)
                ->where('ptclients.email', $email)
                ->where('ptclients.customer_id >', 0)
                ->get($this->db->dbprefix('ptclients'))->row_array();
        }

        if (!$exist) {
            return False;
        } else {
            return $exist;
        }
        // $this->db->select('userId');
        // $this->db->where('email', $email);
        // $this->db->where('isDeleted', 0);
        // $query = $this->db->get($this->db->dbprefix('tbl_users'));

        // if ($query->num_rows() > 0) {
        //     return true;
        // } else {
        //     return false;
        // }
    }
    public function updateResetPasswordLog($reset)
    {
        $exist = $this->db->select('activation_code')
            ->where('activation_code', $reset['activation_code'])
            ->where('role', strtoupper($reset['role']))
            ->get($this->db->dbprefix('reset_password_role_wise'))->row();
        if (!$exist) {
            $this->db->insert($this->db->dbprefix('reset_password_role_wise'), $reset);
        } else {
            $this->db->set('date_added', date('Y-m-d h:i:s'))
                ->where('activation_code', $reset['activation_code'])
                ->update($this->db->dbprefix('reset_password_role_wise'));
        }
    }
    public function getResultByRoleId($role, $id)
    {
        if ($role == strtoupper('trainer')) {

            $exist = $this->db->select('trainer_id as Id,first_name as first_name,last_name as last_name,email as email,reset_password_role_wise.role,reset_password_role_wise.activation_code,reset_password_role_wise.page_code,reset_password_role_wise.date_added')
                ->join('reset_password_role_wise', $this->db->dbprefix('reset_password_role_wise') . '.activation_code = ' . $this->db->dbprefix('trainers') . '.trainer_id ', 'inner')
                ->where('trainers.trainer_id', $id)
                ->where('reset_password_role_wise.role', strtoupper($role))
                ->get($this->db->dbprefix('trainers'))->row_array();
        } else {
            $exist =
                $this->db->select('ptclients.customer_id as Id,first_name as first_name,last_name as last_name,email as email,reset_password_role_wise.role,reset_password_role_wise.activation_code,reset_password_role_wise.page_code,reset_password_role_wise.date_added')
                ->join('reset_password_role_wise', $this->db->dbprefix('reset_password_role_wise') . '.activation_code = ' . $this->db->dbprefix('ptclients') . '.customer_id ', 'inner')
                ->where('ptclients.customer_id', $id)
                ->get($this->db->dbprefix('ptclients'))->row_array();
        }

        if (!$exist) {
            return False;
        } else {
            return $exist;
        }
    }
    public function updatePassword($role, $activation_code, $password)
    {
        if ($role == strtoupper('trainer')) {

            $this->db->set('salt', $salt = substr(md5(uniqid(rand(), TRUE)), 0, 9));
            $this->db->set('password', sha1($salt . sha1($salt . sha1($password))));
            $this->db->where('trainer_id', $activation_code);
            $query = $this->db->update($this->db->dbprefix('trainers'));
        } else {
            $this->db->set('salt', $salt = substr(md5(uniqid(rand(), TRUE)), 0, 9));
            $this->db->set('password', sha1($salt . sha1($salt . sha1($password))));
            $this->db->where('customer_id', $activation_code);
            $query = $this->db->update($this->db->dbprefix('customers'));
        }
        $this->db
            ->where('activation_code', $activation_code)
            ->where('role', strtoupper($role))
            ->delete($this->db->dbprefix('reset_password_role_wise'));
    }

    /**
     * This function used to insert reset password data
     * @param {array} $data : This is reset password data
     * @return {boolean} $result : TRUE/FALSE
     */
    function resetPasswordUser($data)
    {
        $result = $this->db->insert($this->db->dbprefix('tbl_reset_password'), $data);

        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * This function is used to get customer information by email-id for forget password email
     * @param string $email : Email id of customer
     * @return object $result : Information of customer
     */
    function getCustomerInfoByEmail($email)
    {
        $this->db->select('userId, email, name');
        $this->db->from($this->db->dbprefix('tbl_users'));
        $this->db->where('isDeleted', 0);
        $this->db->where('email', $email);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function used to check correct activation deatails for forget password.
     * @param string $email : Email id of user
     * @param string $activation_id : This is activation string
     */
    function checkActivationDetails($email, $activation_id)
    {
        $this->db->select('id');
        $this->db->from($this->db->dbprefix('tbl_reset_password'));
        $this->db->where('email', $email);
        $this->db->where('activation_id', $activation_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    // This function used to create new password by reset link
    function createPasswordUser($email, $password)
    {
        $this->db->where('email', $email);
        $this->db->where('isDeleted', 0);
        $this->db->update($this->db->dbprefix('tbl_users'), array('password' => getHashedPassword($password)));
        $this->db->delete($this->db->dbprefix('tbl_reset_password'), array('email' => $email));
    }

    /**
     * This function used to save login information of user
     * @param array $loginInfo : This is users login information
     */
    function lastLogin($loginInfo)
    {
        $this->db->trans_start();
        $this->db->insert($this->db->dbprefix('tbl_last_login'), $loginInfo);
        $this->db->trans_complete();
    }

    /**
     * This function is used to get last login info by user id
     * @param number $userId : This is user id
     * @return number $result : This is query result
     */
    function lastLoginInfo($userId)
    {
        $this->db->select('BaseTbl.createdDtm');
        $this->db->where('BaseTbl.userId', $userId);
        $this->db->order_by('BaseTbl.id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->db->dbprefix('tbl_last_login as BaseTbl'));

        return $query->row();
    }

    /**
     * This function used to get access matrix of a role by roleId
     * @param number $roleId : This is roleId of user
     */
    function getRoleAccessMatrix($roleId)
    {
        $this->db->select('roleId, access');
        $this->db->from($this->db->dbprefix('tbl_access_matrix'));
        $this->db->where('roleId', $roleId);
        $query = $this->db->get();

        $result = $query->row();
        return $result;
    }
    function getChildMenus($role)
    {
        if ($role == 'PTCLIENT') {
            $user_role = 'PTCLIENT';
        } else {
            $user_role = 'TRAINER';
        }

        return  $this->db->select('menu_name')->where('user_roles', $user_role)->order_by('childmenu_id', 'asc')->get($this->db->dbprefix('child_menus'))->result();
    }
}
