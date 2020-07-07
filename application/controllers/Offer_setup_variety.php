<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Offer_setup_variety extends Root_Controller
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
        $this->lang->language['LABEL_CREATED_TIME']='Created Time';
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
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
        {
            $data['id']= 1;
            $data['variety_name']= 1;
            $data['pack_size']= 1;
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['quantity_minimum']= 1;
            $data['amount_per_kg']= 1;
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
    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Varieties Offer List";
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
        $this->db->from($this->config->item('table_login_setup_classification_variety_price').' price');
        $this->db->select('price.id,price.variety_id,price.pack_size_id');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=price.variety_id','INNER');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
        $this->db->select('crop.name crop_name,crop.id crop_id');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=price.pack_size_id','INNER');
        $this->db->select('pack.name pack_size');

        $this->db->join($this->config->item('table_login_offer_setup_variety').' offer','offer.variety_id=price.variety_id AND offer.pack_size_id=price.pack_size_id AND offer.revision=1','LEFT');
        $this->db->select('offer.status,offer.quantity_minimum,offer.amount_per_kg');

        $this->db->order_by('crop.ordering ASC');
        $this->db->order_by('crop.id ASC');
        $this->db->order_by('crop_type.ordering ASC');
        $this->db->order_by('crop_type.id ASC');
        $this->db->order_by('v.ordering ASC');
        $this->db->order_by('v.id ASC');
        $items=$this->db->get()->result_array();
        $this->json_return($items);

    }


    private function system_edit($id)
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
            $this->db->from($this->config->item('table_login_setup_classification_variety_price').' price');
            $this->db->select('price.id,price.variety_id,price.pack_size_id');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=price.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
            $this->db->select('crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=price.pack_size_id','INNER');
            $this->db->select('pack.name pack_size');

            $this->db->join($this->config->item('table_login_offer_setup_variety').' offer','offer.variety_id=price.variety_id AND offer.pack_size_id=price.pack_size_id AND offer.revision=1','LEFT');
            $this->db->select('offer.status,offer.quantity_minimum,offer.amount_per_kg');

            $this->db->where('price.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Item.';
                $this->json_return($ajax);
            }

            $data['title']="Edit Offer";
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
        $time=time();
        $item=$this->input->post('item');
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
        $item_info=Query_helper::get_info($this->config->item('table_login_setup_classification_variety_price'),'*',array('id ='.$id),1);
        if(!$item_info)
        {
            $ajax['status']=false;
            $ajax['system_message']='Invalid Item.';
            $this->json_return($ajax);
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START
        $this->db->where('variety_id',$item_info['variety_id']);
        $this->db->where('pack_size_id',$item_info['pack_size_id']);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_login_offer_setup_variety'));

        $item['date_created']=$time;
        $item['user_created']=$user->user_id;
        $item['revision']=1;
        $item['variety_id']=$item_info['variety_id'];
        $item['pack_size_id']=$item_info['pack_size_id'];
        Query_helper::add($this->config->item('table_login_offer_setup_variety'),$item);

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
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
    private function system_details($id)
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_login_setup_classification_variety_price').' price');
            $this->db->select('price.id,price.variety_id,price.pack_size_id,price.price,price.price_net');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=price.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
            $this->db->select('crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=price.pack_size_id','INNER');
            $this->db->select('pack.name pack_size');
            $this->db->where('price.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']="Invalid Item";
                $this->json_return($ajax);
            }
            $data['history_offer']=Query_helper::get_info($this->config->item('table_login_offer_setup_variety'),array('*'),array('variety_id ='.$data['item']['variety_id'], 'pack_size_id ='.$data['item']['pack_size_id']),0,0,array('revision ASC'));
            $data['title']='Offer History';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
}
