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
        $this->lang->language['LABEL_STATUS_OFFER']='Offer Status';
        $this->lang->language['LABEL_STATUS_VARIETY']='Variety Status';
    }

    public function index($action="list",$id=0,$id1=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=='edit_offer')
        {
            $this->system_edit_offer($id);
        }
        elseif($action=="save_offer")
        {
            $this->system_save_offer();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="save_preference")
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
            $data['variety_name']= 1;
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['quantity_minimum']= 1;
            $data['amount_per_kg']= 1;
            $data['status_offer']= 1;
            $data['status_variety']= 1;

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
            $data['title']="Varieties";
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
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id,v.name variety_name,v.status status_variety,v.ordering,v.ordering order');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->select('type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_dealer_purchase_offer').' offer','offer.variety_id = v.id','LEFT');
        $this->db->select('offer.quantity_minimum,offer.amount_per_kg,offer.status status_offer');

        $this->db->where('v.status !=',$this->config->item('system_status_delete'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('type.ordering','ASC');
        $this->db->order_by('type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }


    private function system_edit_offer($id)
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
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->select('v.*');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->join($this->config->item('table_login_setup_dealer_purchase_offer').' offer','offer.variety_id = v.id','LEFT');
            $this->db->select('offer.quantity_minimum,offer.amount_per_kg,offer.status');



            $this->db->where('v.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }

            $data['title']='Edit Price in KG of Variety ('.$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_offer",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_offer/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }


    private function system_save_offer()
    {
        $variety_id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $offer_info=$result=Query_helper::get_info($this->config->item('table_login_setup_dealer_purchase_offer'),'*',array('variety_id='.$variety_id),1);

        {
            $data=$this->input->post('item');
            $this->db->trans_start();  //DB Transaction Handle START
            if($offer_info)
            {
                $this->db->where('id',$offer_info['id']);
                $this->db->set('quantity_minimum',$data['quantity_minimum']);
                $this->db->set('amount_per_kg',$data['amount_per_kg']);
                $this->db->set('status',$data['status']);
                if($data['status']==$this->config->item('system_status_active'))
                {
                    $this->db->set('revision_count','revision_count+1',FALSE);
                }

                $this->db->set('user_updated',$user->user_id);
                $this->db->set('date_updated',$time);

                $this->db->update($this->config->item('table_login_setup_dealer_purchase_offer'));
            }
            else
            {
                $data['variety_id']=$variety_id;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                Query_helper::add($this->config->item('table_login_setup_dealer_purchase_offer'),$data,false);
            }

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
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
    }
}
