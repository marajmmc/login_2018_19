<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_cclassification_type extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_cclassification_type');
        $this->controller_url='setup_cclassification_type';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="acres")
        {
            $this->system_acres($id);
        }
        elseif($action=="edit_acres")
        {
            $this->system_edit_acres($id);
        }
        elseif($action=="assign_acres")
        {
            $this->system_assign_acres($id);
        }
        elseif($action=="get_acres_items")
        {
            $this->system_get_acres_items();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="save_amount_acres")
        {
            $this->system_save_amount_acres();
        }
        else
        {
            $this->system_list();
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']="Crop Types";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

    }
    private function system_get_items()
    {
        $this->db->from($this->config->item('table_login_setup_classification_crop_types').' ct');
        //$this->db->select('ct.id,ct.name,ct.status,ct.ordering');
        $this->db->select('ct.id,ct.name,ct.quantity_kg_acre,ct.status,ct.ordering');
        $this->db->select('crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = ct.crop_id','INNER');
        $this->db->where('ct.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('ct.ordering','ASC');
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {

            $data['title']="Create New Crop Type";
            $data["item"] = array(
                'id' => 0,
                'crop_id'=>0,
                'name' => '',
                'description' => '',
                'quantity_kg_acre' => '',
                'ordering' => 99,
                'status' => $this->config->item('system_status_active')
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }

            $data['item']=Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'),'*',array('id ='.$item_id),1);
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['title']="Edit Type (".$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }


    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data=$this->input->post('item');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();

                Query_helper::update($this->config->item('table_login_setup_classification_crop_types'),$data,array("id = ".$id));
            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_login_setup_classification_crop_types'),$data);
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }

    private function system_acres($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_login_setup_classification_crop_types').' t');
            $this->db->select('t.*');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = t.crop_id','INNER');
            $this->db->where('t.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Type.';
                $this->json_return($ajax);
            }

            $data['title']="Acres List of (".$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_acres",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/acres/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_assign_acres($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_login_setup_classification_crop_types').' t');

            $this->db->select('t.*');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = t.crop_id','INNER');
            $this->db->where('t.id',$item_id);
            $data['info']=$this->db->get()->row_array();
            if(!$data['info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Type.';
                $this->json_return($ajax);
            }
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['upazillas']=array();


            $data['item']=array(
                'division_id'=>'',
                'zone_id'=>'',
                'territory_id'=>'',
                'district_id'=>'',
                'upazillas'=>'',
                'id'=>'',
                'quantity_acres'=>''
            );

            $data['title']="Assign Acres of Type (".$data['info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_acres",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_acres/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_amount_acres()
    {
        $type_id = $this->input->post("type_id");
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation_acres_amount())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data=$this->input->post('item');
            $data['type_id']=$type_id;
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data=$this->input->post('item');
                if(!($data['upazilla_id']>0))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Please Select a Upazilla';
                    $this->json_return($ajax);
                }
                $data['type_id']=$type_id;
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();
                $this->db->set('revision','revision+1',FALSE);
                Query_helper::update($this->config->item('table_login_setup_classification_type_acres'),$data,array("id = ".$id));
            }
            else
            {
                if(!($data['upazilla_id']>0))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Please Select a Upazilla';
                    $this->json_return($ajax);
                }
                $result=Query_helper::get_info($this->config->item('table_login_setup_classification_type_acres'),'*',array('type_id='.$data['type_id'],'upazilla_id='.$data['upazilla_id'],'revision=1'),1);
                if($result)
                {
                    $ajax['status']=false;
                    $ajax['system_message']="Quantity Acres of this upazilla is added. You can edit it.";
                    $this->json_return($ajax);
                }
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                $data['revision']=1;
                Query_helper::add($this->config->item('table_login_setup_classification_type_acres'),$data,false);
            }


            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_acres($data['type_id']);
                }
                else
                {
                    $this->system_acres($data['type_id']);
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function system_get_acres_items()
    {
        $type_id=$this->input->post('id');
        $this->db->select('t.name type_name');
        $this->db->select('c.name crop_name');
        $this->db->select('u.name upazila_name');
        $this->db->select('d.name district_name');
        $this->db->select('tr.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('acres.*');
        $this->db->from($this->config->item('table_login_setup_classification_type_acres').' acres');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' t','t.id=acres.type_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' c','c.id=t.crop_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_upazillas').' u','u.id = acres.upazilla_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = u.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' tr','tr.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = tr.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->where('acres.type_id',$type_id);
        $this->db->order_by('division.ordering','ASC');
        $this->db->order_by('zone.ordering','ASC');
        $this->db->order_by('tr.ordering','ASC');
        $this->db->order_by('d.ordering','ASC');
        $this->db->order_by('u.ordering','ASC');
        $results=$this->db->get()->result_array();
        $this->json_return($results);
    }

    private function system_edit_acres($type_id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            $id=$this->input->post('id');
            $this->db->from($this->config->item('table_login_setup_classification_crop_types').' t');
            $this->db->select('t.*');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = t.crop_id','INNER');
            $this->db->where('t.id',$type_id);
            $data['info']=$this->db->get()->row_array();
            if(!$data['info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Type.';
                $this->json_return($ajax);
            }
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $this->db->select('t.name type_name');
            $this->db->select('c.name crop_name');
            $this->db->select('u.name upazila_name, u.id upazilla_id');
            $this->db->select('d.name district_name, d.id district_id');
            $this->db->select('tr.name territory_name, tr.id territory_id');
            $this->db->select('zone.name zone_name, zone.id zone_id');
            $this->db->select('division.name division_name, division.id division_id');
            $this->db->select('acres.*');
            $this->db->from($this->config->item('table_login_setup_classification_type_acres').' acres');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' t','t.id=acres.type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' c','c.id=t.crop_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_upazillas').' u','u.id = acres.upazilla_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' tr','tr.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = tr.zone_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->where('acres.id',$id);
            $data['item']=$this->db->get()->row_array();
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['item']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['item']['zone_id']));
            $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['item']['territory_id']));
            $data['upazillas']=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$data['item']['district_id']));
            $data['title']='Change Price to Pack Size ';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_acres",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/acres/'.$type_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }


    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('item[crop_id]',$this->lang->line('LABEL_CROP_NAME'),'required');
        $this->form_validation->set_rules('item[status]',$this->lang->line('STATUS'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }

    public function check_validation_acres_amount()
    {
        return true;
    }
}
