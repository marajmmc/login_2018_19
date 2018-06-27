<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    public function index()
    {
        $time_start = microtime(true);
        //echo System_helper::display_date_time(1524766939);
        $CI=$this;
        $system_crops=Query_helper::get_info($CI->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $results=Query_helper::get_info($CI->config->item('table_login_setup_classification_crop_types'),array('id value','name text','crop_id'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $system_types=array();
        foreach($results as $result)
        {
            $system_types[$result['crop_id']][]=$result;
        }
        $results=Query_helper::get_info($CI->config->item('table_login_setup_classification_varieties'),array('id value','name text','crop_type_id'),array('status ="'.$CI->config->item('system_status_active').'"','whose ="ARM"'),0,0,array('ordering'));
        $system_varieties=array();
        foreach($results as $result)
        {
            $system_varieties[$result['crop_type_id']][]=$result;
        }

        $system_divisions=Query_helper::get_info($CI->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$CI->config->item('system_status_active').'"'));

        $results=Query_helper::get_info($CI->config->item('table_login_setup_location_zones'),array('id value','name text','division_id'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $system_zones=array();
        foreach($results as $result)
        {
            $system_zones[$result['division_id']][]=$result;
        }
        $results=Query_helper::get_info($CI->config->item('table_login_setup_location_territories'),array('id value','name text','zone_id'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $system_territories=array();
        foreach($results as $result)
        {
            $system_territories[$result['zone_id']][]=$result;
        }
        $results=Query_helper::get_info($CI->config->item('table_login_setup_location_districts'),array('id value','name text','territory_id'),array('status ="'.$CI->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $system_districts=array();
        foreach($results as $result)
        {
            $system_districts[$result['territory_id']][]=$result;
        }

        $CI->db->from($CI->config->item('table_login_csetup_customer').' customer');
        $CI->db->join($CI->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $CI->db->select('customer.id');
        $CI->db->select('cus_info.type, cus_info.district_id, cus_info.customer_id value, cus_info.name text');
        $CI->db->where('customer.status',$CI->config->item('system_status_active'));
        $this->db->where('cus_info.revision',1);
        $results=$CI->db->get()->result_array();
        $system_customers=array();
        $system_outlets=array();
        $system_all_customers=array();
        foreach($results as $result)
        {
            if($result['type']==$CI->config->item('system_customer_type_customer_id'))
            {
                $system_customers[$result['district_id']][]=$result;
            }
            elseif($result['type']==$CI->config->item('system_customer_type_outlet_id'))
            {
                $system_outlets[$result['district_id']][]=$result;
            }
            $system_all_customers[]=$result;
        }
        $menu_odd_color='#fee3b4';
        $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('config_value'),array('purpose ="' .$CI->config->item('system_purpose_ems_menu_odd_color').'"','status ="'.$CI->config->item('system_status_active').'"'),1);
        if($result)
        {
            $menu_odd_color=$result['config_value'];
        }
        $menu_even_color='#e0dff6';
        $result=Query_helper::get_info($this->config->item('table_login_setup_system_configures'),array('config_value'),array('purpose ="' .$CI->config->item('system_purpose_ems_menu_even_color').'"','status ="'.$CI->config->item('system_status_active').'"'),1);
        if($result)
        {
            $menu_even_color=$result['config_value'];
        }
        $time_end = microtime(true);

//dividing with 60 will give the execution time in minutes otherwise seconds
        $execution_time = ($time_end - $time_start)/60;

//execution time of the script
        echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';

    }
	private function user_order()
	{
        $user = User_helper::get_user();

        $this->db->from($this->config->item('table_setup_user').' user');
        $this->db->select('user.employee_id,user.user_name');
        $this->db->select('user_info.user_id,user_info.id');
        $this->db->join($this->config->item('table_setup_user_info').' user_info','user.id = user_info.user_id','INNER');

        $this->db->where('user_info.revision',1);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $data=array();
            $data['ordering']=intval($result['employee_id']);
            $this->db->where('id',$result['id']);
            $this->db->update($this->config->item('table_setup_user_info'), $data);
            echo '<PRE>';
            print_r($data);
            print_r($result['employee_id']);
            echo '</PRE>';
        }

	}
}
