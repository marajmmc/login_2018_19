<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Barcode_variety_price_history extends Root_Controller
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

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
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
            $this->system_list($id);
        }
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_PRICE']='Trade Price';
        $this->lang->language['LABEL_REVISION']='Revision';
        $this->lang->language['LABEL_DATE_CREATED']='Modify Date';
    }

    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Varieties History List";
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
        $outlet_id='000';//no outlet
        $this->db->from($this->config->item('table_login_setup_classification_variety_price_history').' price');
        $this->db->select('price.id,price.variety_id,price.pack_size_id,price.price,price.price_net,price.revision,price.date_created');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=price.variety_id','INNER');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
        $this->db->select('crop.name crop_name,crop.id crop_id');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=price.pack_size_id','INNER');
        $this->db->select('pack.name pack_size');

        $this->db->order_by('crop.ordering ASC');
        $this->db->order_by('crop.id ASC');
        $this->db->order_by('crop_type.ordering ASC');
        $this->db->order_by('crop_type.id ASC');
        $this->db->order_by('v.ordering ASC');
        $this->db->order_by('v.id ASC');
        $this->db->order_by('price.revision DESC');
        $items=$this->db->get()->result_array();
        $fiscal_years=$this->get_fiscal_years();
        foreach($items as &$item)
        {
            $item['fiscal_year']='';
            foreach($fiscal_years as $fy)
            {
                if($fy['date_start']<=$item['date_created'] && $fy['date_end']>=$item['date_created'])
                {
                    $item['fiscal_year']=$fy['text'];
                }
            }
            $item['price']= System_helper::get_string_amount($item['price']);
            $item['price_net']=System_helper::get_string_amount($item['price_net']);
            $item['date_created']=System_helper::display_date_time($item['date_created']);
        }
        $this->json_return($items);
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['preference_method_name']='list';
            $data['title']="Set Preference";
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
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['variety_name']= 1;
        $data['pack_size']= 1;
        $data['price']= 1;
        $data['price_net']= 1;
        $data['revision']= 1;
        $data['fiscal_year']= 1;
        $data['date_created']= 1;
        if($result)
        {
            if($result['preferences']!=null)
            {
                $preferences=json_decode($result['preferences'],true);
                foreach($data as $key=>$value)
                {

                    if(isset($preferences[$key]))
                    {
                        $data[$key]=$value;
                    }
                    else
                    {
                        $data[$key]=0;
                    }
                }
            }
        }
        return $data;
    }
    private function get_fiscal_years()
    {
        $this->db->from($this->config->item('table_login_basic_setup_fiscal_year').' fy');
        $this->db->select('fy.id,fy.name,fy.date_start,fy.date_end');
        $results=$this->db->get()->result_array();
        $fiscal_years=array();
        foreach($results as $result)
        {
            $data=array();
            $data['id']=$result['id'];
            $data['text']=$result['name'];
            $data['date_start']=$result['date_start'];
            $data['date_end']=$result['date_end'];
            $data['value']=System_helper::display_date($result['date_start']).'/'.System_helper::display_date($result['date_end']);
            $fiscal_years[$result['id']]=$data;
        }
        return $fiscal_years;
    }
}
