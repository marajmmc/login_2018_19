<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Csv_setup_users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $user=User_helper::get_user();
        if(!$user)
        {
            echo 'Please login and try again';
            die();
        }
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_STATUS_APPS']='Apps Status';
    }
    public function system_list()
    {
        $user=User_helper::get_user();
        $preference_headers=array();
        $preference_headers['id']= 1;
        $preference_headers['employee_id']= 1;
        $preference_headers['username']= 1;
        $preference_headers['name']= 1;
        $preference_headers['user_group']= 1;
        $preference_headers['user_area']= 1;
        $preference_headers['company_name']= 1;
        $preference_headers['other_sites']= 1;
        $preference_headers['designation_name']= 1;
        $preference_headers['department_name']= 1;
        $preference_headers['mobile_no']= 1;
        $preference_headers['email']= 1;
        $preference_headers['blood_group']= 1;
        $preference_headers['status_apps']= 1;
        $preference_headers['status']= 1;

        $preference= System_helper::get_preference($user->user_id,'setup_users','list',$preference_headers);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=users_list.csv');
        $handle=fopen('php://output', 'w');
        $row=array();
        foreach($preference as $column=>$value)
        {
            if($value==1)
            {
                $row[]=$this->lang->line('LABEL_'.strtoupper($column));
            }
        }
        fputcsv($handle,$row);
//assigned sites
        $this->db->from($this->config->item('table_login_setup_users_other_sites').' users_other_sites');
        $this->db->select('users_other_sites.user_id');
        $this->db->join($this->config->item('table_login_system_other_sites').' other_sites','other_sites.id=users_other_sites.site_id','INNER');
        $this->db->select('other_sites.short_name');
        $this->db->where('users_other_sites.revision',1);
        $this->db->order_by('other_sites.ordering','ASC');
        $this->db->order_by('other_sites.id','ASC');
        $results=$this->db->get()->result_array();
        $users_other_site=array();
        foreach($results as $result)
        {
            if(isset($users_other_site[$result['user_id']]['sites']))
            {
                $users_other_site[$result['user_id']]['sites'].=', '.$result['short_name'];
            }
            else
            {
                $users_other_site[$result['user_id']]['sites']=$result['short_name'];
            }
        }
        //assigned company
        $this->db->from($this->config->item('table_login_setup_users_company').' users_company');
        $this->db->select('users_company.user_id');
        $this->db->join($this->config->item('table_login_setup_company').' company','company.id=users_company.company_id','INNER');
        $this->db->select('company.short_name');
        $this->db->where('users_company.revision',1);
        $this->db->order_by('company.ordering','ASC');
        $this->db->order_by('company.id','ASC');
        $results=$this->db->get()->result_array();
        $users_company=array();
        foreach($results as $result)
        {
            if(isset($users_company[$result['user_id']]['companies']))
            {
                $users_company[$result['user_id']]['companies'].=', '.$result['short_name'];
            }
            else
            {
                $users_company[$result['user_id']]['companies']=$result['short_name'];
            }
        }
        //assigned area
        $this->db->from($this->config->item('table_login_setup_user_area').' user_area');
        $this->db->select('user_area.*');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id=user_area.division_id','LEFT');
        $this->db->select('divisions.name division_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id=user_area.zone_id','LEFT');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id=user_area.territory_id','LEFT');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id=user_area.district_id','LEFT');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_upazillas').' upazillas','upazillas.id=user_area.upazilla_id','LEFT');
        $this->db->select('upazillas.name upazilla_name');
        $this->db->join($this->config->item('table_login_setup_location_unions').' unions','unions.id=user_area.union_id','LEFT');
        $this->db->select('unions.name union_name');
        $this->db->where('user_area.revision',1);
        $results=$this->db->get()->result_array();
        $users_areas=array();
        foreach($results as $result)
        {
            if($result['division_id']==0)
            {
                $users_areas[$result['user_id']]='All Area';
            }
            else if($result['division_id']>0)
            {
                $users_areas[$result['user_id']]='Division - '.$result['division_name'];
                if($result['zone_id']>0)
                {
                    $users_areas[$result['user_id']]='Zone - '.$result['zone_name'];
                    if($result['territory_id']>0)
                    {
                        $users_areas[$result['user_id']]='Territory - '.$result['territory_name'];
                        if($result['district_id']>0)
                        {
                            $users_areas[$result['user_id']]='District - '.$result['district_name'];
                            if($result['upazilla_id']>0)
                            {
                                $users_areas[$result['user_id']]='Upazilla - '.$result['upazilla_name'];
                                if($result['union_id']>0)
                                {
                                    $users_areas[$result['user_id']]='Union - '.$result['union_name'];
                                }
                            }
                        }
                    }
                }
            }
        }
        // app status
        $this->db->from($this->config->item('table_login_setup_user_app').' preference');
        $results=$this->db->get()->result_array();
        $app_users=array();
        foreach($results as $result)
        {
            $app_users[$result['user_id']]=$result['status'];
        }
        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.id,user.employee_id,user.user_name username,user.status');
        $this->db->select('user_info.name,user_info.email,user_info.ordering,user_info.blood_group,user_info.mobile_no');
        $this->db->select('ug.name user_group');
        $this->db->select('designation.name designation_name');
        $this->db->select('department.name department_name');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $this->db->join($this->config->item('table_system_user_group').' ug','ug.id = user_info.user_group','LEFT');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->join($this->config->item('table_login_setup_department').' department','department.id = user_info.department_id','LEFT');
        $this->db->where('user_info.revision',1);
        $this->db->order_by('user_info.ordering','ASC');
        if($user->user_group!=1)
        {
            $this->db->where('user_info.user_group !=',1);
        }

        $items=$this->db->get()->result_array();
        foreach($items as $item)
        {
            $row=array();
            if($preference['id']==1)
            {
                $row[]=$item['id'];
            }
            if($preference['employee_id']==1)
            {
                $row[]=$item['employee_id'];
            }
            if($preference['username']==1)
            {
                $row[]=$item['username'];
            }
            if($preference['name']==1)
            {
                $row[]=$item['name'];
            }
            if($preference['user_group']==1)
            {
                if($item['user_group']==null)
                {
                    $row[]='--';
                }
                else
                {
                    $row[]=$item['user_group'];
                }
            }
            if($preference['user_area']==1)
            {
                if(isset($users_areas[$item['id']]))
                {
                    $row[]=$users_areas[$item['id']];
                }
                else
                {
                    $row[]="Not Assigned";
                }
            }
            if($preference['company_name']==1)
            {
                if(isset($users_company[$item['id']]['companies']))
                {
                    $row[]=$users_company[$item['id']]['companies'];
                }
                else
                {
                    $row[]="N/A";
                }
            }
            if($preference['other_sites']==1)
            {
                if(isset($users_other_site[$item['id']]['sites']))
                {
                    $row[]=$users_other_site[$item['id']]['sites'];
                }
                else
                {
                    $row[]="N/A";
                }
            }
            if($preference['designation_name']==1)
            {
                $row[]=$item['designation_name'];
            }
            if($preference['department_name']==1)
            {
                $row[]=$item['department_name'];
            }
            if($preference['mobile_no']==1)
            {
                $row[]=$item['mobile_no'];
            }
            if($preference['email']==1)
            {
                $row[]=$item['email'];
            }
            if($preference['blood_group']==1)
            {
                if($item['blood_group']==null)
                {
                    $row[]='--';
                }
                else
                {
                    $row[]=$item['blood_group'];
                }
            }
            if($preference['status_apps']==1)
            {
                if(isset($app_users[$item['id']]))
                {
                    $row[]=$app_users[$item['id']];
                }
                else
                {
                    $row[]="N/A";
                }
            }
            if($preference['status']==1)
            {
                $row[]=$item['status'];
            }
            fputcsv($handle,$row);
        }
        fclose($handle);
    }
}
