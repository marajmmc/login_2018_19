<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Root_controller
{
    public function index()
    {
        $this->login();
    }
    public function login()
    {
        $user=User_helper::get_user();
        if($user)
        {
            $this->dashboard_page();
        }
        else
        {
            if(($this->input->post('username'))&&($this->input->post('password')))
            {
                $info=User_helper::login($this->input->post('username'),$this->input->post('password'));
                if($info['status_code']=='111')
                {
                    $this->dashboard_page($info['message']);
                }
                //elseif(($info['status_code']=='0')||($info['status_code']=='10')||($info['status_code']=='1100'))

                else
                {
                    $this->login_page($info['message'],$info['message_warning']);
                }
            }
            else
            {
                $this->login_page();
            }
        }
    }
    public function logout()
    {
        $this->session->set_userdata('user_id','');
        $this->login_page($this->lang->line('MSG_LOGOUT_SUCCESS'));
    }
}
