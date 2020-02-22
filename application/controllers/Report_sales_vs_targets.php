<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_sales_vs_targets extends Root_Controller
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
        $this->config->load('table_bms');
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_AMOUNT_TARGET']='Target Amount (bdt)';
        $this->lang->language['LABEL_AMOUNT_SALES']='Total Sales (bdt)';
        $this->lang->language['LABEL_AMOUNT_DEFERENCE']='Deference (bdt)';
        $this->lang->language['LABEL_AMOUNT_AVERAGE']='Average (%)';
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
        elseif($action=="set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
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
        if($method=='list')
        {
            $data['sl_no']= 1;
            $data['area']= 1;
            $data['amount_target']= 1;
            $data['amount_sales']= 1;
            $data['amount_deference']= 1;
            $data['amount_average']= 1;
        }
        else
        {

        }
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
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Sales Vs Targets Report";
            $ajax['status']=true;
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id'],'status ="'.$this->config->item('system_status_active').'"'));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id'],'status ="'.$this->config->item('system_status_active').'"'));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id'],'status ="'.$this->config->item('system_status_active').'"'));
                        if($this->locations['district_id']>0)
                        {
                            $this->db->from($this->config->item('table_login_csetup_customer').' customer');
                            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id=customer.id','INNER');
                            $this->db->select('customer.id value, cus_info.name text');
                            $this->db->where('customer.status',$this->config->item('system_status_active'));
                            $this->db->where('cus_info.district_id',$this->locations['district_id']);
                            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
                            $this->db->where('cus_info.revision',1);
                            $data['outlets']=$this->db->get()->result_array();
                        }
                    }

                }
            }
            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
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
            $method='list';
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
            $data['options']=$reports;

            $ajax['status']=true;
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            if($reports['outlet_id']>0)
            {
                $data['areas']='Outlet';
            }
            elseif($reports['district_id']>0)
            {
                $data['areas']='Outlets';
            }
            elseif($reports['territory_id']>0)
            {
                $data['areas']='Districts';
            }
            elseif($reports['zone_id']>0)
            {
                $data['areas']='Territories';
            }
            elseif($reports['division_id']>0)
            {
                $data['areas']='Zones';
            }
            else
            {
                $data['areas']='Divisions';
            }
            $data['title']="Sales Vs Achievement Report";
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
        $queries=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');
        if($outlet_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('customer_id ='.$outlet_id,'revision =1'));
            $location_type='outlet_id';
        }
        elseif($district_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('district_id ='.$district_id,'revision =1','type ="'.$this->config->item('system_customer_type_outlet_id').'"'));
            $location_type='outlet_id';
        }
        elseif($territory_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='district_id';
        }
        elseif($zone_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='territory_id';

            $this->db->from($this->config->item('table_bms_target_tsme').' items');
            $this->db->select($location_type.', items.amount_target');
            $this->db->select("TIMESTAMPDIFF(SECOND, '1970-01-01', CONCAT_WS('-', items.year, lpad(items.month,2,'0'), '01')) AS date_target ");
            $this->db->join($this->config->item('table_bms_target_ams').' zone_target','zone_target.id = items.ams_id','INNER');
            $this->db->where('zone_target.zone_id', $zone_id);
            $this->db->having(array('date_target >=' => $date_start, 'date_target <=' => $date_end));
            $queries=$this->db->get()->result_array();
        }
        elseif($division_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='zone_id';

            $this->db->from($this->config->item('table_bms_target_ams').' items');
            $this->db->select($location_type.', items.amount_target');
            $this->db->select("TIMESTAMPDIFF(SECOND, '1970-01-01', CONCAT_WS('-', items.year, lpad(items.month,2,'0'), '01')) AS date_target ");
            $this->db->join($this->config->item('table_bms_target_dsm').' division_target','division_target.id = items.dsm_id','INNER');
            $this->db->where('division_target.division_id', $division_id);
            $this->db->having(array('date_target >=' => $date_start, 'date_target <=' => $date_end));
            $queries=$this->db->get()->result_array();
        }
        else
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $location_type='division_id';

            $this->db->from($this->config->item('table_bms_target_dsm').' items');
            $this->db->select($location_type.', amount_target');
            //$this->db->select('SUM(amount_target) amount_target',false);
            $this->db->select("TIMESTAMPDIFF(SECOND, '1970-01-01', CONCAT_WS('-', items.year, lpad(items.month,2,'0'), '01')) AS date_target ");
            //$this->db->group_by(array($location_type));
            $this->db->having(array('date_target >=' => $date_start, 'date_target <=' => $date_end));
            $queries=$this->db->get()->result_array();

        }
        $area_initial=array();
        //setting 0
        foreach($areas as $area)
        {
            $area_initial[$area['value']]=$this->initialize_row_area_amount($area['text']);
        }

        foreach($queries as $result)
        {
            if(isset($area_initial[$result[$location_type]]['amount_target']))
            {
                $area_initial[$result[$location_type]]['amount_target']+=$result['amount_target'];
            }
            else
            {
                $area_initial[$result[$location_type]]['amount_target']=$result['amount_target'];
            }
        }

        $this->db->from($this->config->item('table_pos_sale').' sale');
        $this->db->select('SUM(sale.amount_payable) sale_amount, sale.sales_payment_method');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id = sale.outlet_id and outlet_info.revision =1','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');

        $this->db->select('sale.outlet_id');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');
        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zone.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('t.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('d.id',$district_id);
                        if($outlet_id>0)
                        {
                            $this->db->where('outlet_info.customer_id',$outlet_id);
                        }
                    }
                }
            }
        }
        $this->db->where('sale.date_sale >=',$date_start);
        $this->db->where('sale.date_sale <=',$date_end);
        $this->db->where('sale.status',$this->config->item('system_status_active'));
        //$this->db->group_by('sales_payment_method');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if(isset($area_initial[$result[$location_type]]['sale_amount']))
            {
                $area_initial[$result[$location_type]]['amount_sales']+=$result['sale_amount'];
            }
            else
            {
                $area_initial[$result[$location_type]]['amount_sales']=$result['sale_amount'];
            }
        }

        $grand_total=$this->initialize_row_area_amount('Grand Total');
        $method='list';
        $headers=$this->get_preference_headers($method);
        foreach($area_initial as $info)
        {
            $amount_target=isset($info['amount_target'])?$info['amount_target']:0;
            $info['amount_deference']=($info['amount_sales']-$amount_target);
            if($amount_target)
            {
                $info['amount_average']=($info['amount_sales']/$amount_target)*100;
            }
            foreach($headers  as $key=>$r)
            {
                if(!(($key=='area')||($key=='sl_no')))
                {
                    $grand_total[$key]+=$info[$key];
                }
            }

            $items[]=$info;
            //$items[]=$item;
        }
        $items[]=$grand_total;
        $this->json_return($items);

    }
    private function initialize_row_area_amount($area_name)
    {
        $method='list';
        $row=$this->get_preference_headers($method);
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['area']=$area_name;
        $row['sl_no']='';
        return $row;
    }
}
