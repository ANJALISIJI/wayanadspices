<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Send email to client
 */
class SendClientEmail
{
    // sending mail to client ,$result is an array
    public function sendClientMail($result, $link = NULL)
    {
        $CI = &get_instance();
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'developer01@technowand.com.au',
            'smtp_pass' => '$Technowand007',
            'mailtype'  => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE

        );
        $CI->load->library('email', $config);
        $CI->email->set_newline("\r\n");
        if ($link) {
            $url = base_url($link);
            $email_body = "<div>Hello " . $result['first_name'] . " " . $result['last_name'] . "<br>We have received your password reset request.if you did not make this request please ignore this mail,<br>Click below to reset your password<br>
            <a href=" . $url . "> Reset password</a>
            </div>";
            $head = 'Password Reset';
        } else {
            $url = base_url('/clientsignup');
            $email_body = "<div>Hello " . $result['first_name'] . " " . $result['last_name'] . "<br>your email is successfully registered with us please click on below link to sign up with us,<br>
            <a href=" . $url . "> Sign up</a>
            </div>";
            $head = 'Registration successful';
        }


        $CI->email->from('developer01@technowand.com.au', 'IGNITIT');

        $list = array($result['email']);
        $CI->email->to($list);
        $CI->email->subject($head);
        $CI->email->message($email_body);

        $CI->email->send();
        echo $CI->email->print_debugger();
    }
}
