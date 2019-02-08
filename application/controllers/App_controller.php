<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_controller extends CI_Controller
{
    private $APP_KEY='ARMLoginApp';
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        //echo $this->APP_KEY;
    }
    private function json_return($array)
    {
        header('Content-type: application/json');
        echo json_encode($array);
        exit();
    }
    public function device_login()
    {
        //$response=array();
        $time=time();
        $user_name=$this->input->post('user_name');
        $password=$this->input->post('password');
        $device_token=$this->input->post('device_token');
        $app_key=$this->input->post('app_key');
        if($app_key!=$this->APP_KEY)
        {
            $response=array
            (
                'status'=>false,
                'message'=>'Invalid Application.'
            );
            $this->json_return($response);
        }

        if(!($device_token))
        {
            $response=array
            (
                'status'=>false,
                'message'=>'Invalid Device.'
            );
            $this->json_return($response);
        }

        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.id, user.password, user.employee_id, user.status');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->select('user_info.name user_full_name, user_info.mobile_no, user_info.email, user_info.image_location');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id=user_info.designation','LEFT');
        $this->db->select('designation.name designation_name');
        //$this->db->where('user.status',$this->config->item('system_status_active'));
        $this->db->where('user_info.revision',1);
        $this->db->where('user.user_name',$user_name);
        $this->db->order_by('user_info.ordering','ASC');
        $user=$this->db->get()->row_array();
        if(!$user)
        {
            $response=array
            (
                'status'=>false,
                'message'=>'User not found.'
            );
            $this->json_return($response);
        }

        if($user['status']!=$this->config->item('system_status_active'))
        {
            $response=array
            (
                'status'=>false,
                'message'=>'Invalid User.'
            );
            $this->json_return($response);
        }

        if($user['password']!=md5($password))
        {
            $response=array
            (
                'status'=>false,
                'message'=>'Wrong password.'
            );
            $this->json_return($response);
        }

        $user_id=$user['id'];
        $user_info=array
        (
            'name'=>$user['user_full_name'],
            'designation'=>$user['designation_name'],
            'mobile_no'=>$user['mobile_no'],
            'email'=>$user['email'],
            'user_id'=>$user_id,
            'device_token'=>$device_token,
            'image_location'=>$this->config->item('system_base_url_profile_picture').$user['image_location']
        );
        $id_registered=0;
        $result=Query_helper::get_info($this->config->item('table_login_setup_user_app'),'id, user_id',array('user_id='.$user['id']),1);
        if($result)
        {
            $id_registered=$result['id'];
        }

        // get EMS User role
        $this->db->from($this->config->item('table_system_ems_assigned_group').' assigned_group');
        $this->db->join($this->config->item('table_system_ems_user_group_role').' role','role.user_group_id=assigned_group.user_group','INNER');
        $this->db->join($this->config->item('table_system_ems_task').' task','task.id=role.task_id','INNER');
        $this->db->select('task.id, task.name');
        $this->db->where('assigned_group.user_id',$user_id);
        $this->db->where('role.revision',1);
        $this->db->where('assigned_group.revision',1);
        $this->db->where('task.type','TASK');
        $this->db->where('task.status_notification',$this->config->item('system_status_yes'));
        $results=$this->db->get()->result_array();
        $task_ems_ids='';
        if($results)
        {
            $task_ems_ids=',';
            foreach($results as $result)
            {
                $task_ems_ids.=$result['id'].',';
            }
        }
        // get SMS User Role
        $this->db->from($this->config->item('table_system_sms_assigned_group').' assigned_group');
        $this->db->join($this->config->item('table_system_sms_user_group_role').' role','role.user_group_id=assigned_group.user_group','INNER');
        $this->db->join($this->config->item('table_system_sms_task').' task','task.id=role.task_id','INNER');
        $this->db->select('task.id, task.name');
        $this->db->where('assigned_group.user_id',$user_id);
        $this->db->where('role.revision',1);
        $this->db->where('assigned_group.revision',1);
        $this->db->where('task.type','TASK');
        $this->db->where('task.status_notification',$this->config->item('system_status_yes'));
        $results=$this->db->get()->result_array();
        $task_sms_ids='';
        if($results)
        {
            $task_sms_ids=',';
            foreach($results as $result)
            {
                $task_sms_ids.=$result['id'].',';
            }
        }
        // get BMS User Role
        $this->db->from($this->config->item('table_system_bms_assigned_group').' assigned_group');
        $this->db->join($this->config->item('table_system_bms_user_group_role').' role','role.user_group_id=assigned_group.user_group','INNER');
        $this->db->join($this->config->item('table_system_bms_task').' task','task.id=role.task_id','INNER');
        $this->db->select('task.id, task.name');
        $this->db->where('assigned_group.user_id',$user_id);
        $this->db->where('role.revision',1);
        $this->db->where('assigned_group.revision',1);
        $this->db->where('task.type','TASK');
        $this->db->where('task.status_notification',$this->config->item('system_status_yes'));
        $results=$this->db->get()->result_array();
        $task_bms_ids='';
        if($results)
        {
            $task_bms_ids=',';
            foreach($results as $result)
            {
                $task_bms_ids.=$result['id'].',';
            }
        }

        $data=array();
        $data['user_id']=$user_id;
        $data['device_token']=$device_token;
        $data['ems']=$task_ems_ids;
        $data['sms']=$task_sms_ids;
        $data['bms']=$task_bms_ids;
        $data['status']=$this->config->item('system_status_yes');

        $this->db->trans_start();  //DB Transaction Handle START
        if($id_registered>0)
        {
            // update query
            $data['user_updated']=$user_id;
            $data['date_updated']=$time;
            Query_helper::update($this->config->item('table_login_setup_user_app'),$data,array('id='.$id_registered),false);
        }
        else
        {
            // insert query
            $data['user_created']=$user_id;
            $data['date_created']=$time;
            Query_helper::add($this->config->item('table_login_setup_user_app'),$data,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $response=array
            (
                'status'=>true,
                'data'=>$user_info
            );
            $this->json_return($response);
        }
        else
        {
            $response=array
            (
                'status'=>false,
                'message'=>'Database Transaction Error'
            );
            $this->json_return($response);
        }

    }
    public function device_logout()
    {
        $time=time();
        $user_id=$this->input->post('user_id');
        $device_token=$this->input->post('device_token');
        $app_key=$this->input->post('app_key');
        if($app_key!=$this->APP_KEY)
        {
            $response=array
            (
                'status'=>false,
                'message'=>'Invalid Application.'
            );
            $this->json_return($response);
        }
        if(!($user_id))
        {
            $response=array
            (
                'status'=>false,
                'message'=>'Invalid User.'
            );
            $this->json_return($response);
        }
        if(!($device_token))
        {
            $response=array
            (
                'status'=>false,
                'message'=>'Invalid Device.'
            );
            $this->json_return($response);
        }

        $app_user=Query_helper::get_info($this->config->item('table_login_setup_user_app'),'id, user_id, device_token,status',array('device_token ="'.$device_token.'"','user_id='.$user_id),1);
        if(!$app_user)
        {
            $response=array
            (
                'status'=>true,
                'message'=>'Already Logged Out'
            );
            $this->json_return($response);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $data['status']=$this->config->item('system_status_no');
        $data['user_updated']=$user_id;
        $data['date_updated']=$time;
        Query_helper::update($this->config->item('table_login_setup_user_app'),$data,array('id ='.$app_user['id']),false);

        $this->db->trans_complete();   //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $response=array
            (
                'status'=>true,
                'message'=>'Logged Out Successfully'
            );
        }
        else
        {
            $response=array
            (
                'status'=>false,
                'message'=>'Database Transaction Error'
            );
        }
        $this->json_return($response);
    }
}
