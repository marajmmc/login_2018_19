<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_dealer_purchase_offer extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->locations=User_helper::get_locations();
        $this->user=User_helper::get_user();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url = strtolower(get_class($this));
        $this->lang->load('report_sale');
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_QUANTITY_MINIMUM_KG']='Minimum quantity(kg)';
        $this->lang->language['LABEL_AMOUNT_MINIMUM']='Amount per Kg';
    }

    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
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
            $this->system_search();
        }
    }
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Dealer Offer Report Search";
            $ajax['status']=true;
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            //$data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            //$data['zones']=array();
            //$data['territories']=array();
            //$data['districts']=array();
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

            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $data['farmer_types']=Query_helper::get_info($this->config->item('table_pos_setup_farmer_type'),array('id value,name text'),array('status ="'.$this->config->item('system_status_active').'"','id >1'),0,0,array('ordering ASC'));
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url);

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
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='search_list')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['quantity_minimum_kg']= 1;
            $data['amount_minimum']= 1;
            $data['quantity_total_kg']=1;
            $data['amount_total']=1;
            $data['quantity_kg']=1;
            $data['amount']=1;
            return $data;
        }
        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method='search_list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_end']=$reports['date_end']+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->json_return($ajax);
            }
            if(!($reports['outlet_id']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='Select a showroom';
                $this->json_return($ajax);
            }
            $data['options']=$reports;
            $data['dealers']=$this->get_dealers($reports['outlet_id'],$reports['farmer_type_id']);
            $ajax['status']=true;
            $data['title']="Dealer Offer Report";
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));

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
    private function system_get_items()
    {
        $items=array();
        $outlet_id=$this->input->post('outlet_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');

        $farmer_type_id=$this->input->post('farmer_type_id');
        $dealers=$this->get_dealers($outlet_id,$farmer_type_id);
        $dealer_ids=array();
        $dealer_ids[0]=0;
        foreach($dealers as $dealer)
        {
            $dealer_ids[$dealer['farmer_id']]=$dealer['farmer_id'];
        }

        $results=Query_helper::get_info($this->config->item('table_login_setup_dealer_purchase_offer'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
        $offers=array();
        foreach($results as $result)
        {
            $offers[$result['variety_id']]=$result;
        }

        $this->db->from($this->config->item('table_pos_sale_details').' details');
        $this->db->select('details.variety_id,details.pack_size_id,details.pack_size');

        $this->db->join($this->config->item('table_pos_sale').' sale','sale.id = details.sale_id','INNER');


        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then details.quantity*details.pack_size ELSE 0 END) quantity_total_gm',false);
        $this->db->select('SUM(CASE WHEN sale.date_cancel>='.$date_start.' and sale.date_cancel<='.$date_end.' and sale.status="'.$this->config->item('system_status_inactive').'" then details.quantity*details.pack_size ELSE 0 END) quantity_cancel_gm',false);

        $this->db->select('sale.farmer_id');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = details.variety_id','INNER');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');

        //$this->db->where('sale.outlet_id',$outlet_id);
        $this->db->where_in('sale.farmer_id',$dealer_ids);

        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        $this->db->group_by('sale.farmer_id');
        $this->db->group_by('details.variety_id');
        //$this->db->group_by('details.pack_size_id');

        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $results=$this->db->get()->result_array();

        $sales=array();
        foreach($results as $result)
        {
            $sales[$result['variety_id']]['crop_name']=$result['crop_name'];
            $sales[$result['variety_id']]['crop_type_name']=$result['crop_type_name'];
            $sales[$result['variety_id']]['variety_name']=$result['variety_name'];
            $sales[$result['variety_id']]['dealers'][$result['farmer_id']]=$result;

        }
        foreach($sales as $variety_id=>$variety_sale)
        {
            $item=$this->initialize_row($variety_sale['crop_name'],$variety_sale['crop_type_name'],$variety_sale['variety_name'],$dealers);
            if(isset($offers[$variety_id]))
            {
                $item['quantity_minimum_kg']=$offers[$variety_id]['quantity_minimum'];
                $item['amount_minimum']=$offers[$variety_id]['amount_per_kg'];
            }
            else
            {
                $item['quantity_minimum_kg']=0;
                $item['amount_minimum']=0;
            }
            foreach($variety_sale['dealers'] as $dealer_id=>$dealer_sale)
            {
                if(in_array($dealer_id,$dealer_ids))
                {
                    $item['quantity_'.$dealer_id.'_kg']=($dealer_sale['quantity_total_gm']-$dealer_sale['quantity_cancel_gm'])/1000;
                    $item['quantity_total_kg']+=$item['quantity_'.$dealer_id.'_kg'];
                    if($item['quantity_'.$dealer_id.'_kg']>=$item['quantity_minimum_kg'])
                    {
                        $item['amount_'.$dealer_id]=$item['quantity_'.$dealer_id.'_kg']*$item['amount_minimum'];
                        $item['amount_total']+=$item['amount_'.$dealer_id];
                    }
                }
            }
            if($item['quantity_total_kg']==0)
            {
                continue;
                //exclude 0 values;
            }
            $items[]=$item;
        }
        $this->json_return($items);



    }
    private function initialize_row($crop_name,$crop_type_name,$variety_name,$dealers)
    {
        $row=array();
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $row['quantity_total_kg']=0;
        $row['amount_total']=0;
        foreach($dealers  as $dealer)
        {
            $row['quantity_'.$dealer['farmer_id'].'_kg']=0;
            $row['amount_'.$dealer['farmer_id']]=0;
        }
        return $row;
    }
    private function reset_row($info)
    {
        foreach($info  as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
            {
                $info[$key]=0;
            }
        }
        return $info;
    }
    private function system_set_preference()
    {
        $user = User_helper::get_user();
        $method='search_list';
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
    private function get_dealers($outlet_id,$dealer_type)
    {
        $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
        $this->db->select('farmer_outlet.farmer_id');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id=farmer_outlet.farmer_id','INNER');
        $this->db->select('farmer.name farmer_name,farmer.mobile_no,farmer.status');
        $this->db->where('farmer.status',$this->config->item('system_status_active'));
        $this->db->where('farmer.farmer_type_id > ',1);
        $this->db->where('farmer_outlet.revision',1);
        $this->db->where('farmer_outlet.outlet_id',$outlet_id);
        if($dealer_type>1)
        {
            $this->db->where('farmer.farmer_type_id',$dealer_type);
        }
        return $this->db->get()->result_array();
    }
}
