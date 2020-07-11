<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Offer_calculator extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $user_outlets;
    public $user_outlet_ids;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());

        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->load->helper('offer');
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        else
        {
            $this->system_list($id);
        }
    }
    private function system_list($id)
    {
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $farmer_type_id=$id;
            }
            else
            {
                $farmer_type_id=$this->input->post('farmer_type_id');
            }
            $data['farmer_types']=Query_helper::get_info($this->config->item('table_pos_setup_farmer_type'),array('id value,name text'),array('status ="'.$this->config->item('system_status_active').'"','id >1','allow_offer ="'.$this->config->item('system_status_yes').'"'),0,0,array('ordering ASC'));
            if(sizeof($data['farmer_types'])==0)
            {
                $ajax['status']=false;
                $ajax['system_message']="No dealer is allowed for offer.<br>".$this->lang->line('MSG_CONTACT_ADMIN');
                $this->json_return($ajax);
            }
            if(!($farmer_type_id>1))
            {
                $farmer_type_id=$data['farmer_types'][0]['value'];
            }
            $data['farmer_type_id']=$farmer_type_id;
            $result=Query_helper::get_info($this->config->item('table_pos_setup_farmer_type'),'*',array('status ="'.$this->config->item('system_status_active').'"','id ='.$farmer_type_id,'allow_offer ="'.$this->config->item('system_status_yes').'"'),1);
            if(!$result)
            {
                $ajax['status']=false;
                $ajax['system_message']="Invalid access";
                $this->json_return($ajax);
            }
            $price_multiplier=$result['price_multiplier'];

            //variety price and offers
            $this->db->from($this->config->item('table_login_setup_classification_variety_price').' price');
            $this->db->select('price.id,price.variety_id,price.pack_size_id,ROUND(price.price *'.$price_multiplier.',2) price_unit_pack');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=price.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
            $this->db->select('crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=price.pack_size_id','INNER');
            $this->db->select('pack.name pack_size');

            $this->db->join($this->config->item('table_login_offer_setup_variety').' offer','offer.variety_id=price.variety_id AND offer.pack_size_id=price.pack_size_id AND offer.revision=1 AND offer.status="'.$this->config->item('system_status_active').'"','LEFT');
            $this->db->select('offer.status,offer.quantity_minimum,offer.amount_per_kg');

            $this->db->where('v.status',$this->config->item('system_status_active'));

            $this->db->order_by('crop.ordering ASC');
            $this->db->order_by('crop.id ASC');
            $this->db->order_by('crop_type.ordering ASC');
            $this->db->order_by('crop_type.id ASC');
            $this->db->order_by('v.ordering ASC');
            $this->db->order_by('v.id ASC');
            $results=$this->db->get()->result_array();

            $data['sale_varieties_info']=array();
            foreach($results as $result)
            {
                $data['sale_varieties_info'][Barcode_helper::get_barcode_variety('000',$result['variety_id'],$result['pack_size_id'])]=$result;
            }
            //assign outlets
            $this->db->from($this->config->item('table_login_csetup_customer').' cus');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = cus.id','INNER');
            $this->db->select('cus.id value, cus_info.name text');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            if($this->locations['division_id']>0)
            {
                $this->db->where('division.id',$this->locations['division_id']);
                if($this->locations['zone_id']>0)
                {
                    $this->db->where('zone.id',$this->locations['zone_id']);
                    if($this->locations['territory_id']>0)
                    {
                        $this->db->where('t.id',$this->locations['territory_id']);
                        if($this->locations['district_id']>0)
                        {
                            $this->db->where('cus_info.district_id',$this->locations['district_id']);
                        }
                    }

                }
            }
            $this->db->where('cus_info.revision',1);
            $this->db->where('cus.status !=',$this->config->item('system_status_delete'));

            $this->db->where('cus.status',$this->config->item('system_status_active'));
            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('cus_info.revision',1);

            $this->db->order_by('division.ordering','ASC');
            $this->db->order_by('zone.ordering','ASC');
            $this->db->order_by('t.ordering','ASC');
            $this->db->order_by('d.ordering','ASC');
            $this->db->order_by('cus_info.ordering','ASC');
            $data['outlets']=$this->db->get()->result_array();



            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/calculator",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list/'.$farmer_type_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    public function get_dropdown_farmers_by_outlet_farmer_type_id()
    {
        $html_container_id='#farmer_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }

        $farmer_type_id = $this->input->post('farmer_type_id');
        $outlet_id = $this->input->post('outlet_id');

        $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
        $this->db->where('farmer_outlet.outlet_id',$outlet_id);

        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = farmer_outlet.farmer_id','INNER');

        $this->db->select('farmer.mobile_no,farmer.name,farmer.id');

        $this->db->where('farmer.farmer_type_id',$farmer_type_id);
        $this->db->where('farmer.status',$this->config->item('system_status_active'));
        $this->db->where('farmer_outlet.revision',1);

        $this->db->order_by('farmer.ordering DESC');
        $this->db->order_by('farmer.id DESC');
        $farmers=$this->db->get()->result_array();
        $farmer_ids=array();
        $farmer_ids[0]=0;
        foreach($farmers as $farmer)
        {
            $farmer_ids[$farmer['id']]=$farmer['id'];
        }
        $offers=Offer_helper::get_offer_stats($farmer_ids);

        $data['items']=array();
        foreach($farmers as $farmer)
        {
            $item=array();
            $item['text']=$farmer['name'];
            $item['value']=$farmer['id'].'/'.$offers[$farmer['id']]['offer_balance'];
            $data['items'][]=$item;
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
        $this->json_return($ajax);
    }

}
