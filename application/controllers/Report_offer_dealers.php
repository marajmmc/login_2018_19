<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_offer_dealers extends Root_Controller
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
        $this->load->helper('offer');
        $this->lang->load('report_sale');
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_OFFER_OFFERED']='Calculated Reward Points';
        $this->lang->language['LABEL_OFFER_GIVEN']='RP Adjusted by sales';
        $this->lang->language['LABEL_OFFER_ADJUSTED']='RP Adjusted Cash/Credit balance';
        $this->lang->language['LABEL_OFFER_BALANCE']='Reward Points Balance';
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
        elseif($action=="set_preference_list_offer_current_dealers_balance")
        {
            $this->system_set_preference('list_offer_current_dealers_balance');
        }
        elseif($action=="get_items_offer_current_dealers_balance")
        {
            $this->system_get_items_offer_current_dealers_balance();
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
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='list_offer_current_dealers_balance')
        {
            $data['barcode']= 1;
            $data['name']= 1;
            $data['offer_offered']= 1;
            $data['offer_given']= 1;
            $data['offer_adjusted']= 1;
            $data['offer_balance']= 1;
        }
        /*else if($method=='list_offer_current_dealers_balance')
        {
            $data['farmer_id']= 1;
            $data['name']= 1;
            $data['mobile_no']= 1;
            $data['offer_offered']= 1;
            $data['offer_given']= 1;
            $data['offer_adjusted']= 1;
            $data['offer_balance']= 1;
        }*/
        return $data;
    }
    private function system_set_preference($method)
    {
        $user = User_helper::get_user();
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['preference_method_name']=$method;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_'.$method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    public function get_dropdown_dealers_by_outlet_farmer_type_id()
    {
        $outlet_id=$this->input->post('outlet_id');
        $farmer_type_id = $this->input->post('farmer_type_id');
        $dealers=$this->get_dealers($outlet_id,$farmer_type_id);


        $data['items']=array();
        foreach($dealers as $dealer)
        {
            $data['items'][]=array('text'=> $dealer['farmer_name'],'value'=>$dealer['farmer_id']);
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>'#farmer_id',"html"=>$this->load->view("dropdown_with_select",$data,true));
        $this->json_return($ajax);
    }
    private function get_dealers($outlet_id,$dealer_type=0,$farmer_id=0)
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
        if($farmer_id>0)
        {
            $this->db->where('farmer.id',$farmer_id);
        }
        $this->db->order_by('farmer.ordering DESC');
        $this->db->order_by('farmer.id DESC');
        return $this->db->get()->result_array();
    }
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Reward Points Report Search";
            $ajax['status']=true;
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
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $user = User_helper::get_user();
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_end']=$reports['date_end']+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['report_name']!='offer_current_dealers_balance')
            {
                if($reports['date_start']>=$reports['date_end'])
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Starting Date should be less than End date';
                    $this->json_return($ajax);
                }
            }

            $data['options']=$reports;

            $ajax['status']=true;
            if($reports['report_name']=='offer_current_dealers_balance')
            {
                $method='list_offer_current_dealers_balance';
                $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
                $data['title']="Dealer Current Reward Points Balance Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_offer_current_dealers_balance",$data,true));
            }
            else
            {
                $this->message='Under Construction';
            }

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
    private function system_get_items_offer_current_dealers_balance()
    {
        $outlet_id=$this->input->post('outlet_id');
        $farmer_type_id=$this->input->post('farmer_type_id');
        $farmer_id=$this->input->post('farmer_id');
        $farmers=$this->get_dealers($outlet_id,$farmer_type_id,$farmer_id);
        $farmer_ids=array();
        $farmer_ids[0]=0;
        foreach($farmers as $farmer)
        {
            $farmer_ids[$farmer['farmer_id']]=$farmer['farmer_id'];
        }
        $offers=Offer_helper::get_offer_stats($farmer_ids);

        $items=array();
        /*foreach($farmers as $farmer)
        {
            $item=array();
            $item['text']=$farmer['name'];
            $item['value']=$farmer['id'].'/'.$offers[$farmer['id']]['offer_balance'];
            $data['items'][]=$item;
        }*/
        foreach($farmers as $farmer)
        {
            $item=array();
            $item['barcode']=Barcode_helper::get_barcode_farmer($farmer['farmer_id']);
            $item['name']=$farmer['farmer_name'];
            $item['offer_offered']=$offers[$farmer['farmer_id']]['offer_offered'];
            $item['offer_given']=$offers[$farmer['farmer_id']]['offer_given'];
            $item['offer_adjusted']=$offers[$farmer['farmer_id']]['offer_adjusted'];
            $item['offer_balance']=$offers[$farmer['farmer_id']]['offer_balance'];


            $items[]=$item;
        }
        $this->json_return($items);
    }


}
