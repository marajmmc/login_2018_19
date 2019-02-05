<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_user_app extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        //$this->permissions=User_helper::get_permission('Profile_password');
        $this->controller_url=strtolower(get_class());

    }

    public function index($action="edit")
    {
        if($action=="edit")
        {
            $this->system_edit();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_edit();
        }
    }
    private function system_edit()
    {
        $user=User_helper::get_user();
        $user_id=$user->user_id;
        $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user_app'),array('*'),array('user_id ='.$user_id),1);
        if(!$data['user'])
        {
            $ajax['status']=false;
            $ajax['system_message']='User not login in mobile apps.';
            $this->json_return($ajax);
        }
        if($data['user']['status']!=$this->config->item('system_status_yes'))
        {
            $ajax['status']=false;
            $ajax['system_message']='User logged out in mobile apps.';
            $this->json_return($ajax);
        }
        //EMS all Assign Task
        $this->db->from($this->config->item('table_system_ems_assigned_group').' assigned_group');
        $this->db->join($this->config->item('table_system_ems_user_group_role').' role','role.user_group_id=assigned_group.user_group','INNER');
        $this->db->join($this->config->item('table_system_ems_task').' task','task.id=role.task_id','INNER');
        $this->db->select('task.id, task.name');
        $this->db->where('assigned_group.user_id',$user_id);
        $this->db->where('role.revision',1);
        $this->db->where('task.type','TASK');
        $data['task_ems']=$this->db->get()->result_array();

        //SMS all Assign Task
        $this->db->from($this->config->item('table_system_sms_assigned_group').' assigned_group');
        $this->db->join($this->config->item('table_system_sms_user_group_role').' role','role.user_group_id=assigned_group.user_group','INNER');
        $this->db->join($this->config->item('table_system_sms_task').' task','task.id=role.task_id','INNER');
        $this->db->select('task.id, task.name');
        $this->db->where('assigned_group.user_id',$user_id);
        $this->db->where('role.revision',1);
        $this->db->where('task.type','TASK');
        $data['task_sms']=$this->db->get()->result_array();

        //BMS all Assign Task
        $this->db->from($this->config->item('table_system_bms_assigned_group').' assigned_group');
        $this->db->join($this->config->item('table_system_bms_user_group_role').' role','role.user_group_id=assigned_group.user_group','INNER');
        $this->db->join($this->config->item('table_system_bms_task').' task','task.id=role.task_id','INNER');
        $this->db->select('task.id, task.name');
        $this->db->where('assigned_group.user_id',$user_id);
        $this->db->where('role.revision',1);
        $this->db->where('task.type','TASK');
        $data['task_bms']=$this->db->get()->result_array();

        $result=Query_helper::get_info($this->config->item('table_login_setup_user_app'),'*',array('user_id ='.$user_id),1);
        $data['notify_task_ems_ids']=array();
        $data['notify_task_sms_ids']=array();
        $data['notify_task_bms_ids']=array();
        if($result['ems'])
        {
            $notify_task_ems_ids=explode(',',$result['ems']);
            unset($notify_task_ems_ids[0]);
            unset($notify_task_ems_ids[sizeof($notify_task_ems_ids)]);
            $data['notify_task_ems_ids']=array_flip($notify_task_ems_ids);
        }
        if($result['sms'])
        {
            $notify_task_sms_ids=explode(',',$result['sms']);
            unset($notify_task_sms_ids[0]);
            unset($notify_task_sms_ids[sizeof($notify_task_sms_ids)]);
            $data['notify_task_sms_ids']=array_flip($notify_task_sms_ids);
        }
        if($result['bms'])
        {
            $notify_task_bms_ids=explode(',',$result['bms']);
            unset($notify_task_bms_ids[0]);
            unset($notify_task_bms_ids[sizeof($notify_task_bms_ids)]);
            $data['notify_task_bms_ids']=array_flip($notify_task_bms_ids);
        }

        $data['item']['id']=$user->user_id;
        $data['title']="App Notification Preference";
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $ajax['system_page_url']=site_url($this->controller_url.'/index/edit');
        $this->json_return($ajax);
    }
    private function system_save()
    {
        $time=time();
        $ems=$this->input->post('ems');
        $sms=$this->input->post('sms');
        $bms=$this->input->post('bms');
        $user = User_helper::get_user();
        $user_id = $user->user_id;

        //old items
        $item_old=Query_helper::get_info($this->config->item('table_login_setup_user_app'),'*',array('user_id ='.$user_id),1);
        if(!$item_old)
        {
            $ajax['status']=false;
            $ajax['system_message']='User not login in mobile apps.';
            $this->json_return($ajax);
        }
        if($item_old['status']!=$this->config->item('system_status_yes'))
        {
            $ajax['status']=false;
            $ajax['system_message']='User logged out in mobile apps.';
            $this->json_return($ajax);
        }

        //EMS all Assign Task
        $task_ems_ids='';
        $task_sms_ids='';
        $task_bms_ids='';
        if($ems)
        {
            $this->db->from($this->config->item('table_system_ems_assigned_group').' assigned_group');
            $this->db->join($this->config->item('table_system_ems_user_group_role').' role','role.user_group_id=assigned_group.user_group','INNER');
            $this->db->join($this->config->item('table_system_ems_task').' task','task.id=role.task_id','INNER');
            $this->db->select('task.id, task.name');
            $this->db->where('assigned_group.user_id',$user_id);
            $this->db->where('role.revision',1);
            $this->db->where('task.type','TASK');
            $results=$this->db->get()->result_array();
            $task_ems_ids=',';
            foreach($results as $result)
            {
                if(isset($ems[$result['id']]))
                {
                    $task_ems_ids.=$result['id'].',';
                }
            }
        }
        if($sms)
        {
            $this->db->from($this->config->item('table_system_sms_assigned_group').' assigned_group');
            $this->db->join($this->config->item('table_system_sms_user_group_role').' role','role.user_group_id=assigned_group.user_group','INNER');
            $this->db->join($this->config->item('table_system_sms_task').' task','task.id=role.task_id','INNER');
            $this->db->select('task.id, task.name');
            $this->db->where('assigned_group.user_id',$user_id);
            $this->db->where('role.revision',1);
            $this->db->where('task.type','TASK');
            $results=$this->db->get()->result_array();
            $task_sms_ids=',';
            foreach($results as $result)
            {
                if(isset($sms[$result['id']]))
                {
                    $task_sms_ids.=$result['id'].',';
                }
            }
        }
        if($bms)
        {
            $this->db->from($this->config->item('table_system_bms_assigned_group').' assigned_group');
            $this->db->join($this->config->item('table_system_bms_user_group_role').' role','role.user_group_id=assigned_group.user_group','INNER');
            $this->db->join($this->config->item('table_system_bms_task').' task','task.id=role.task_id','INNER');
            $this->db->select('task.id, task.name');
            $this->db->where('assigned_group.user_id',$user_id);
            $this->db->where('role.revision',1);
            $this->db->where('task.type','TASK');
            $results=$this->db->get()->result_array();
            $task_bms_ids=',';
            foreach($results as $result)
            {
                if(isset($bms[$result['id']]))
                {
                    $task_bms_ids.=$result['id'].',';
                }
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        // old item
        $data=array();
        $data['ems']=$task_ems_ids;
        $data['sms']=$task_sms_ids;
        $data['bms']=$task_bms_ids;
        $data['user_updated']=$user->user_id;
        $data['date_updated']=$time;
        Query_helper::update($this->config->item('table_login_setup_user_app'),$data,array('user_id='.$user_id),false);

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_edit();
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

}
