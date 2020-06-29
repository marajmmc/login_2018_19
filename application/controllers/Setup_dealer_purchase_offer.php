<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_dealer_purchase_offer extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_QUANTITY_MINIMUM']='Minimum quantity(kg)';
        $this->lang->language['LABEL_AMOUNT_PER_KG']='Amount per Kg';
        $this->lang->language['LABEL_VARIETIES']='Varieties';
        $this->lang->language['LABEL_IS_FLOOR']='Floor Quantity';
    }

    public function index($action = "list", $id = 0,$id1=0)
    {
        if ($action == "list")
        {
            $this->system_list($id);
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items($id);
        }
        elseif ($action == "add")
        {
            $this->system_add($id);
        }
        elseif ($action == "edit")
        {
            $this->system_edit($id,$id1);
        }
        elseif ($action == "save")
        {
            $this->system_save();
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference();
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }
    private function get_preference_headers($method)
    {
        $data = array();
        if($method=='list')
        {   $data['id']= 1;
            $data['name']= 1;
            $data['varieties']= 1;
            $data['quantity_minimum']= 1;
            $data['amount_per_kg']= 1;
            $data['is_floor']= 1;
            $data['status']= 1;

        }
        return $data;
    }

    private function system_set_preference()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['preference_method_name']=$method;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_list($fiscal_year_id=0)
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),array('id value','name text','date_start','date_end'),array());
            if(!$fiscal_year_id)
            {
                $date=time();
                foreach($data['fiscal_years'] as $fiscal_year)
                {
                    if($fiscal_year['date_start']<$date && $fiscal_year['date_end']>=$date)
                    {
                        $fiscal_year_id=$fiscal_year['value'];
                    }
                }
            }
            $data['fiscal_year_id']=$fiscal_year_id;

            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Varieties";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

    }
    private function system_get_items($fiscal_year_id)
    {
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('id value','name text'),array());
        $varieties=array();
        foreach($results as $result)
        {
            $varieties[$result['value']]=$result['text'];
        }
        $results=Query_helper::get_info($this->config->item('table_login_setup_dealer_purchase_offer'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['name']=$result['name'];
            $item['quantity_minimum']=$result['quantity_minimum'];
            $item['amount_per_kg']=$result['amount_per_kg'];
            $item['is_floor']=$result['is_floor'];
            $item['status']=$result['status'];
            $offer_varieties=explode(',',trim($result['variety_ids'], ","));
            $item['varieties']=',';
            foreach($offer_varieties as $offer_variety_id)
            {
                $item['varieties']=$item['varieties'].$varieties[$offer_variety_id].',';
            }
            $item['varieties']=trim($item['varieties'], ",");
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function get_crops_varieties()
    {
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('crop.id crop_id,crop.name crop_name');
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('type.ordering','ASC');
        $this->db->order_by('type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $results=$this->db->get()->result_array();
        $crops=array();
        foreach($results as $result)
        {
            $crops[$result['crop_id']]['crop_name']=$result['crop_name'];
            $crops[$result['crop_id']]['varieties'][]=$result;
        }
        return $crops;
    }
    private function system_add($fiscal_year_id)
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create New Offer";
            $data['item']=array();
            $table_fields = $this->db->field_data($this->config->item('table_login_setup_dealer_purchase_offer'));

            foreach ($table_fields as $field)
            {
                $data['item'][$field->name]=$field->default;
            }
            $data['item']['fiscal_year_id']=$fiscal_year_id;
            $data['crops']=$this->get_crops_varieties();
            $data['fiscal_year_id']=$fiscal_year_id;

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit($fiscal_year_id,$id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $data['item']=Query_helper::get_info($this->config->item('table_login_setup_dealer_purchase_offer'),array('*'),array('id ='.$item_id,'status !="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Offer.';
                $this->json_return($ajax);
            }

            $data['title']="Edit Offer :: ". $data['item']['name'];
            $data['crops']=$this->get_crops_varieties();
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$fiscal_year_id.'/'.$item_id);
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
        $fiscal_year_id = $this->input->post("fiscal_year_id");
        $user = User_helper::get_user();
        $time=time();
        $item=$this->input->post('item');
        $variety_ids=$this->input->post('variety_ids');
        if(!is_array($variety_ids))
        {
            $ajax['status']=false;
            $ajax['system_message']="At Least Select One Variety";
            $this->json_return($ajax);
        }
        $item['variety_ids']=','.implode(',',$variety_ids).',';
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $result=Query_helper::get_info($this->config->item('table_login_setup_dealer_purchase_offer'),'*',array('id ='.$id, 'status != "'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Offer.';
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

        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {
            $item['date_updated']=$time;
            $item['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_login_setup_dealer_purchase_offer'),$item,array('id='.$id));
        }
        else
        {
            $item['fiscal_year_id']=$fiscal_year_id;
            $item['date_created']=$time;
            $item['user_created']=$user->user_id;
            Query_helper::add($this->config->item('table_login_setup_dealer_purchase_offer'),$item);
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add($fiscal_year_id);
            }
            else
            {
                $this->system_list($fiscal_year_id);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('item[status]',$this->lang->line('LABEL_STATUS'),'required');
        $this->form_validation->set_rules('item[quantity_minimum]',$this->lang->line('LABEL_QUANTITY_MINIMUM'),'required');
        $this->form_validation->set_rules('item[amount_per_kg]',$this->lang->line('LABEL_AMOUNT_PER_KG'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
