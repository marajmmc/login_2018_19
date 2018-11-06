<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_helper
{
    public static $logged_user = null;
    function __construct($id)
    {
        $CI = & get_instance();
        $this->username_password_same=false;
        //user
        $result=Query_helper::get_info($CI->config->item('table_login_setup_user'),'*',array('id ='.$id),1);
        if($result && (md5($result['user_name'])==$result['password']))
        {
            $this->username_password_same=true;
        }
        //user info
        $result=Query_helper::get_info($CI->config->item('table_login_setup_user_info'),'*',array('user_id ='.$id,'revision =1'),1);
        if ($result)
        {
            foreach ($result as $key => $value)
            {
                $this->$key = $value;
            }
        }
    }
    public static function login($username, $password)
    {
        $CI = & get_instance();
        $time=time();
        $user=Query_helper::get_info($CI->config->item('table_login_setup_user'),'*',array('user_name ="'.$username.'"', 'status ="'.$CI->config->item('system_status_active').'"'),1);
        //1st digit 0=>!user 1=>user   0
            //2nd digit 0=>!password
                //3rd digit 0=>wrong password(100) 1 =>suspend account(101)
            //2nd digit 1=>password
                //3rd digit(1) 0=>otp 1=>!otp direct login  111

            //4th digit 0=>!mobile 1=>view opt login 1100 1101
        if($user)//first digit 1
        {
            if($user['password']==md5($password))//2nd digit 1
            {
                if($user['password_wrong_consecutive']>0)
                {
                    $data=array();
                    $data['password_wrong_consecutive']=0;
                    Query_helper::update($CI->config->item('table_login_setup_user'),$data,array("id = ".$user['id']),false);
                }
                //direct login
                //reset cookie
                //    set_cookie('otp_'.$user['id'],$cookie_info,$cookie_expire_time);
                $CI->session->set_userdata("user_id", $user['id']);
                return array('status_code'=>'111','message'=>$CI->lang->line('MSG_LOGIN_SUCCESS'),'message_warning'=>'');
            }
            else//2nd digit 0
            {
                $result=Query_helper::get_info($CI->config->item('table_login_setup_system_configures'),array('config_value'),array('purpose ="' .$CI->config->item('system_purpose_login_max_wrong_password').'"','status ="'.$CI->config->item('system_status_active').'"'),1);

                $data=array();
                $data['password_wrong_consecutive']=$user['password_wrong_consecutive']+1;
                $data['password_wrong_total']=$user['password_wrong_total']+1;

                if($data['password_wrong_consecutive']<=$result['config_value'])//3ed digit 0
                {
                    $message_warning=sprintf($CI->lang->line('WARNING_LOGIN_FAIL_100'),$result['config_value']-$data['password_wrong_consecutive']+1);
                    Query_helper::update($CI->config->item('table_login_setup_user'),$data,array("id = ".$user['id']),false);
                    return array('status_code'=>'100','message'=>$CI->lang->line('MSG_LOGIN_FAIL_100'),'message_warning'=>$message_warning);
                }
                else//3rd digit 1
                {
                    $data['status']=$CI->config->item('system_status_inactive');
                    $data['remarks_status_change']=sprintf($CI->lang->line('REMARKS_USER_SUSPEND_WRONG_PASSWORD'),$data['password_wrong_consecutive']);
                    $data['date_status_changed'] = $time;
                    $data['user_status_changed'] = -1;
                    Query_helper::update($CI->config->item('table_login_setup_user'),$data,array("id = ".$user['id']),false);
                    return array('status_code'=>'101','message'=>$CI->lang->line('MSG_LOGIN_FAIL_101'),'message_warning'=>$CI->lang->line('WARNING_LOGIN_FAIL_101'));
                }
            }
        }
        else//first digit 0
        {
            return array('status_code'=>'0','message'=>$CI->lang->line('MSG_LOGIN_FAIL_0'),'message_warning'=>'');
        }

        /*$user = $CI->db->get_where($CI->config->item('table_login_setup_user'), array('user_name' => $username, 'password' =>(md5($password)),'status'=>$CI->config->item('system_status_active')))->row();
        if ($user)
        {
            $CI->session->set_userdata("user_id", $user->id);
            return TRUE;
        }
        else
        {
            return FALSE;
        }*/
    }
    public static function get_user()
    {
        $CI = & get_instance();
        if (User_helper::$logged_user) {
            return User_helper::$logged_user;
        }
        else
        {
            if($CI->session->userdata("user_id")!="")
            {
                $user = $CI->db->get_where($CI->config->item('table_login_setup_user'), array('id' => $CI->session->userdata('user_id'),'status'=>$CI->config->item('system_status_active')))->row();
                if($user)
                {
                    User_helper::$logged_user = new User_helper($CI->session->userdata('user_id'));
                    return User_helper::$logged_user;
                }
                return null;
            }
            else
            {
                return null;
            }

        }
    }
    public static function get_html_menu()
    {
        $user=User_helper::get_user();
        $CI = & get_instance();
        $CI->db->order_by('ordering');
        $tasks=$CI->db->get($CI->config->item('table_system_task'))->result_array();

        $roles=Query_helper::get_info($CI->config->item('table_system_user_group_role'),'*',array('revision =1','action0 =1','user_group_id ='.$user->user_group));
        $role_data=array();
        foreach($roles as $role)
        {
            $role_data[]=$role['task_id'];

        }
        $menu_data=array();
        foreach($tasks as $task)
        {
            if($task['type']=='TASK')
            {
                if(in_array($task['id'],$role_data))
                {
                    $menu_data['items'][$task['id']]=$task;
                    $menu_data['children'][$task['parent']][]=$task['id'];
                }
            }
            else
            {
                $menu_data['items'][$task['id']]=$task;
                $menu_data['children'][$task['parent']][]=$task['id'];
            }
        }

        $html='';
        if(isset($menu_data['children'][0]))
        {
            foreach($menu_data['children'][0] as $child)
            {
                $html.=User_helper::get_html_submenu($child,$menu_data,1);
            }
        }
        return $html;



        //return User_helper::get_html_submenu(0,$menu_data,1);

    }
    public static function get_html_submenu($parent,$menu_data,$level)
    {
        if(isset($menu_data['children'][$parent]))
        {
            $sub_html='';
            foreach($menu_data['children'][$parent] as $child)
            {
                $sub_html.=User_helper::get_html_submenu($child,$menu_data,$level+1);

            }
            $html='';
            if($sub_html)
            {
                if($level==1)
                {
                    $html.='<li class="menu-item dropdown">';
                    $html.='<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$menu_data['items'][$parent]['name'].'<b class="caret"></b></a>';
                }
                else
                {
                    $html.='<li class="menu-item dropdown dropdown-submenu">';
                    $html.='<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$menu_data['items'][$parent]['name'].'</a>';
                }

                $html.='<ul class="dropdown-menu">';
                $html.=$sub_html;
                $html.='</ul></li>';
            }

            return $html;

        }
        else
        {
            if($menu_data['items'][$parent]['type']=='TASK')
            {
                return '<li><a href="'.site_url(strtolower($menu_data['items'][$parent]['controller'])).'">'.$menu_data['items'][$parent]['name'].'</a></li>';
            }
            else
            {
                return '';
            }

        }
    }
    public static function get_permission($controller_name)
    {
        $CI = & get_instance();
        $user=User_helper::get_user();
        $CI->db->from($CI->config->item('table_system_user_group_role').' ugr');
        $CI->db->select('ugr.*');

        $CI->db->join($CI->config->item('table_system_task').' task','task.id = ugr.task_id','INNER');
        $CI->db->where("controller",$controller_name,"after");
        $CI->db->where("user_group_id",$user->user_group);
        $CI->db->where("revision",1);
        $result=$CI->db->get()->row_array();
        return $result;
    }
    public static function get_accessed_sites()
    {
        $CI = & get_instance();
        $user=User_helper::get_user();
        $CI->db->from($CI->config->item('table_login_setup_users_other_sites').' uos');
        $CI->db->select('os.id,os.short_name,os.full_name,os.site_url');
        $CI->db->join($CI->config->item('table_login_system_other_sites').' os','os.id = uos.site_id','INNER');
        $CI->db->where('uos.revision',1);
        $CI->db->where('uos.user_id',$user->user_id);
        $CI->db->where('os.status',$CI->config->item('system_status_active'));
        $CI->db->order_by('os.ordering');
        $result=$CI->db->get()->result_array();
        return $result;
    }
    public static function get_locations()
    {
        $CI = & get_instance();
        $user=User_helper::get_user();
        $CI->db->from($CI->config->item('table_login_setup_user_area').' aa');
        $CI->db->select('aa.*');
        $CI->db->select('union.name union_name');
        $CI->db->select('u.name upazilla_name');
        $CI->db->select('d.name district_name');
        $CI->db->select('t.name territory_name');
        $CI->db->select('zone.name zone_name');
        $CI->db->select('division.name division_name');
        $CI->db->join($CI->config->item('table_login_setup_location_unions').' union','union.id = aa.union_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_upazillas').' u','u.id = aa.upazilla_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_districts').' d','d.id = aa.district_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_territories').' t','t.id = aa.territory_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_zones').' zone','zone.id = aa.zone_id','LEFT');
        $CI->db->join($CI->config->item('table_login_setup_location_divisions').' division','division.id = aa.division_id','LEFT');
        $CI->db->where('aa.revision',1);
        $CI->db->where('aa.user_id',$user->user_id);
        $assigned_area=$CI->db->get()->row_array();
        if($assigned_area)
        {
            $CI->db->from($CI->config->item('table_login_setup_user_area').' aa');
            if($assigned_area['division_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_divisions').' division','division.id = aa.division_id','INNER');
            }
            if($assigned_area['zone_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_zones').' zone','zone.division_id = division.id','INNER');
                $CI->db->where('zone.id',$assigned_area['zone_id']);
            }
            if($assigned_area['territory_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_territories').' t','t.zone_id = zone.id','INNER');
                $CI->db->where('t.id',$assigned_area['territory_id']);
            }
            if($assigned_area['district_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_districts').' d','d.territory_id = t.id','INNER');
                $CI->db->where('d.id',$assigned_area['district_id']);
            }
            if($assigned_area['upazilla_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_upazillas').' u','u.district_id = d.id','INNER');
                $CI->db->where('u.id',$assigned_area['upazilla_id']);
            }
            if($assigned_area['union_id']>0)
            {
                $CI->db->join($CI->config->item('table_login_setup_location_unions').' union','union.upazilla_id = u.id','INNER');
                $CI->db->where('union.id',$assigned_area['union_id']);
            }
            $CI->db->where('aa.revision',1);
            $CI->db->where('aa.user_id',$user->user_id);
            $info=$CI->db->get()->row_array();
            if(!$info)
            {
                return array();
            }
        }
        return $assigned_area;
    }
}