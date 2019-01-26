<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_sale_analysis extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $common_view_location;
    public $locations;

    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->common_view_location='report_sale_analysis';
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_FISCAL_YEARS']='Fiscal Years';
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
            $this->system_set_preference('search');
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
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['pack_size']= 1;
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
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets']=array();
            $data['upazillas']=array();
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
                            $data['outlets']=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('type =1','revision =1','district_id ='.$this->locations['district_id']),0,0,array('ordering ASC'));
                            $data['upazillas']=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
                        }
                    }

                }
            }
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),array('id value','name text'),array());

            $data['date_start']='';
            $data['date_end']='';

            $data['title']="Sales Analysis Report";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
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
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $user = User_helper::get_user();
            $reports=$this->input->post('report');
            $fiscal_year_id=$reports['fiscal_year_id'];

            if(!($reports['fiscal_year_id']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Fiscal year field is required.';
                $this->json_return($ajax);
            }
            if(!($reports['fiscal_year_number']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Number of Previous Year field is required.';
                $this->json_return($ajax);
            }

            $data['options']=$reports;
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <='.$fiscal_year_id),$reports['fiscal_year_number']+1,0,array('id DESC'));

            $method='list';
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['title']="Fiscal Year Wise Sales Analysis Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));

            $ajax['status']=true;
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
        $items=array();

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $month=$this->input->post('month');
        $fiscal_year_number=$this->input->post('fiscal_year_number');


        /*get variety*/
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id,crop.name crop_name');
        if($crop_id)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $varieties = $this->db->get()->result_array();
        $variety_ids=array();
        $variety_ids[0]=0;
        foreach($varieties as $result)
        {
            $variety_ids[$result['variety_id']]=$result['variety_id'];
        }
        /*fiscal year*/
        $fiscal_years=$this->get_fiscal_years($fiscal_year_id, $fiscal_year_number, $month);
        $sales=$this->get_sales_previous_years($fiscal_years,$variety_ids);

        $type_total=$this->initialize_row($fiscal_years,'','','Total Type','');
        $crop_total=$this->initialize_row($fiscal_years,'','Total Crop','','');
        $grand_total=$this->initialize_row($fiscal_years,'Grand Total','','','');

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        foreach($varieties as $variety)
        {
            //$info=$this->initialize_row($fiscal_years,$variety);
            if(!$first_row)
            {
                if($prev_crop_name!=$variety['crop_name'])
                {
                    $items[]=$type_total;
                    $items[]=$crop_total;
                    $type_total=$this->reset_row($type_total);
                    $crop_total=$this->reset_row($crop_total);

                    $info['crop_name']=$variety['crop_name'];
                    $info['crop_type_name']=$variety['crop_type_name'];
                    $prev_crop_name=$variety['crop_name'];
                    $prev_type_name=$variety['crop_type_name'];
                }
                elseif($prev_type_name!=$variety['crop_type_name'])
                {
                    $items[]=$type_total;
                    $type_total=$this->reset_row($type_total);
                    $info['crop_type_name']=$variety['crop_type_name'];
                    $prev_type_name=$variety['crop_type_name'];
                }
                else
                {
                    $info['crop_name']='';
                    $info['crop_type_name']='';
                }
            }
            else
            {
                $info['crop_name']=$variety['crop_name'];
                $info['crop_type_name']=$variety['crop_type_name'];

                $prev_crop_name=$variety['crop_name'];
                $prev_type_name=$variety['crop_type_name'];
                $first_row=false;
            }
            $info['variety_name']=$variety['variety_name'];
            foreach($fiscal_years as $fy)
            {
                if(isset($sales[$fy['id']][$variety['variety_id']]))
                {
                    foreach($sales[$fy['id']][$variety['variety_id']] as $pack_size_id=>$sale_detail)
                    {
                        $info['pack_size']=$sale_detail['pack_size'];
                        $info['quantity_sale_pkt_'.$fy['id']]=$sale_detail['quantity_sale_pkt'];
                        $info['quantity_sale_kg_'.$fy['id']]=$sale_detail['quantity_sale_kg'];
                        $info['amount_total_'.$fy['id']]=$sale_detail['amount_total'];
                    }
                }
            }
            foreach($info as $key=>$r)
            {
                if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
                {
                    $type_total[$key]+=$info[$key];
                    $crop_total[$key]+=$info[$key];
                    $grand_total[$key]+=$info[$key];
                }
            }
            $items[]=$info;

        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }
    private function initialize_row($fiscal_years,$crop_name,$crop_type_name,$variety_name,$pack_size)
    {
        $row=$this->get_preference_headers('list');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $row['pack_size']=$pack_size;
        foreach($fiscal_years as $fy)
        {
            $row['quantity_sale_pkt_'.$fy['id']]=0;
            $row['quantity_sale_kg_'.$fy['id']]=0;
            $row['amount_total_'.$fy['id']]=0;
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
    private function get_fiscal_years($fiscal_year_id, $fiscal_year_number, $month)
    {
        $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <='.$fiscal_year_id),$fiscal_year_number+1,0,array('id DESC'));
        if($month>0)
        {
            foreach($fiscal_years as &$fy)
            {
                $year_start=date('Y', $fy['date_start']);
                $month_start=date('m', $fy['date_start']);
                $year_end=date('Y', $fy['date_end']);
                $month_end=date('m', $fy['date_end']);
                if($month>$month_end)
                {
                    $fy['date_start']=strtotime('01-'.$month.'-'.$year_start);
                    if($month==12)
                    {
                        $fy['date_end']=strtotime('01-01-'.$year_end)-1;
                    }
                    else
                    {
                        $fy['date_end']=strtotime('01-'.($month+1).'-'.$year_start)-1;
                    }
                }
                else
                {
                    $fy['date_start']=strtotime('01-'.$month.'-'.$year_end);
                    $fy['date_end']=strtotime('01-'.($month+1).'-'.$year_end)-1;
                }
            }
        }
        return $fiscal_years;
    }
    private function get_sales_previous_years($fiscal_years,$variety_ids)
    {
        $sales=array();
        foreach($fiscal_years as $fy)
        {
            $this->db->from($this->config->item('table_pos_sale_details').' details');
            $this->db->select('details.variety_id, details.pack_size, details.pack_size_id');
            $this->db->select('SUM(details.quantity) quantity_sale_pkt');
            $this->db->select('SUM((details.pack_size*details.quantity)/1000) quantity_sale_kg');
            $this->db->select('SUM(details.amount_payable_actual-((details.amount_payable_actual*sale.discount_self_percentage)/100)) amount_total');

            $this->db->join($this->config->item('table_pos_sale').' sale','sale.id = details.sale_id','INNER');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = sale.outlet_id','INNER');
            $this->db->select('cus_info.customer_id outlet_id, cus_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');

            /*$this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');*/

            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('cus_info.revision',1);
            $this->db->where('sale.date_sale >=',$fy['date_start']);
            $this->db->where('sale.date_sale <=',$fy['date_end']);
            $this->db->where('sale.status',$this->config->item('system_status_active'));
            //$this->db->where_in('details.variety_id',$variety_ids);
            $this->db->group_by('details.variety_id, details.pack_size_id');
            $results=$this->db->get()->result_array();
            //echo $this->db->last_query();
            foreach($results as $result)
            {
                $sales[$fy['id']][$result['variety_id']][$result['pack_size_id']]=$result;
            }
        }

        return $sales;

    }
}
