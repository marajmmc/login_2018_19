<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_acres extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message='';
        $this->permissions=User_helper::get_permission('Reports_acres');
        $this->controller_url='reports_acres';
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
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
        }elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        else
        {
            $this->system_search();
        }
    }

    private function system_search()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']="Acres Report";
            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),array('id value','name text','date_start','date_end'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets']=array();
            $data['upazillas']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id']));
                        if($this->locations['district_id']>0)
                        {
                            $data['outlets']=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('type =1','revision =1','district_id ='.$this->locations['district_id']),0,0,array('ordering ASC'));
                        }
                    }

                }
            }

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
            $reports=$this->input->post('report');
            $data['options']=$reports;
            $data['title']="Acres Report";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
        $this->db->from($this->config->item('table_login_setup_location_upazillas').' upazillas');
        $this->db->select('upazillas.*');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = upazillas.district_id','LEFT');
        $this->db->select('territories.id territory_id');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','LEFT');
        $this->db->select('zones.id zone_id');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','LEFT');
        $this->db->select('divisions.id division_id');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','LEFT');
        $results=$this->db->get()->result_array();

        $all_upazillas_ids=array();
        $division_wise_upazilla_ids=array();
        $zone_wise_upazilla_ids=array();
        $territory_wise_upazilla_ids=array();
        $district_wise_upazilla_ids=array();

        foreach($results as $result)
        {
            $all_upazillas_ids[$result['id']]=$result['id'];
            $division_wise_upazilla_ids[$result['division_id']][]=$result['id'];
            $zone_wise_upazilla_ids[$result['zone_id']][]=$result['id'];
            $territory_wise_upazilla_ids[$result['territory_id']][]=$result['id'];
            $district_wise_upazilla_ids[$result['district_id']][]=$result['id'];
        }

        $outlet_id=$this->input->post('outlet_id');
        $outlet_wise_upazilla_ids=array();
        if($outlet_id)
        {
            $results_upazillas=Query_helper::get_info($this->config->item('table_login_csetup_cus_assign_upazillas'),array('upazilla_id'),array('customer_id ='.$outlet_id,'revision ='.'1'));
            foreach($results_upazillas as $results_upazilla)
            {
                $outlet_wise_upazilla_ids[$results_upazilla['upazilla_id']]=$results_upazilla['upazilla_id'];
            }
        }

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');
        $upazilla_id=$this->input->post('upazilla_id');

        $items=array();

        $this->db->from($this->config->item('table_login_setup_classification_type_acres').' acres');
        $this->db->select('acres.*');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' croptype','croptype.id=acres.type_id','INNER');
        $this->db->select('croptype.id crop_type_id, croptype.name crop_type');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=croptype.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->order_by('crop.id, croptype.id');
        if($crop_type_id>0 && is_numeric($crop_type_id))
        {
            $this->db->where('acres.crop_type_id',$crop_type_id);
        }
        if($crop_id>0 && is_numeric($crop_id))
        {
            $this->db->where('crop.crop_id',$crop_id);
        }
        if($division_id>0)
        {
            $this->db->where_in('acres.upazilla_id',$division_wise_upazilla_ids[$division_id]);
            if($zone_id>0)
            {
                $this->db->where_in('acres.upazilla_id',$zone_wise_upazilla_ids[$zone_id]);
                if($territory_id>0)
                {
                    $this->db->where_in('acres.upazilla_id',$territory_wise_upazilla_ids[$territory_id]);
                    if($district_id>0)
                    {
                        $this->db->where_in('acres.upazilla_id',$district_wise_upazilla_ids[$district_id]);
                        if($outlet_id>0)
                        {
                            $this->db->where_in('acres.upazilla_id',$outlet_wise_upazilla_ids[$outlet_id]);
                            if($upazilla_id>0)
                            {
                                $this->db->where_in('acres.upazilla_id',$upazilla_id);
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $this->db->where_in('acres.upazilla_id',$all_upazillas_ids);
        }

        $results=$this->db->get()->result_array();



        $this->json_return($items);
        die();
    }
}
