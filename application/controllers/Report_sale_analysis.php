<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_sale_analysis extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $common_view_location;

    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->common_view_location='report_sale_analysis';
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
        if($method=='list_lc_wise')
        {
            $data['id']= 1;
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['pack_size']= 1;
            $data['barcode']= 1;
            $data['fiscal_year']= 1;
            $data['month']= 1;
            $data['date_opening']= 1;
            $data['principal_name']= 1;
            $data['lc_number']= 1;
            $data['date_expected']= 1;
            $data['date_awb']= 1;
            $data['date_forwarded_time']= 1;
            $data['date_release']= 1;
            $data['date_released_time']= 1;
            $data['date_receive']= 1;
            $data['date_received_time']= 1;
            $data['date_completed_time']= 1;
            $data['currency_name']= 1;
            $data['quantity_open_kg']= 1;
            $data['status_open_forward']= 1;
            $data['status_release']= 1;
            $data['status_received']= 1;
            $data['status_open']= 1;
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
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $data['date_start']='';
            $data['date_end']='';

            $data['title']="Previous Sales Analysis Report";
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
            $reports['date_end']=System_helper::get_time($reports['date_end'])+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->json_return($ajax);
            }

            $data['options']=$reports;

            $method='list';
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['title']="Previous Sales Analysis Report";
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
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');

        $date_type=$this->input->post('date_type');

        $date_start=$this->input->post('date_start');
        $date_end=$this->input->post('date_end');

        $principal_id=$this->input->post('principal_id');

        $status_open_forward=$this->input->post('status_open_forward');
        $status_release=$this->input->post('status_release');
        $status_received=$this->input->post('status_received');
        $status_open=$this->input->post('status_open');

        /*get variety */
        $varieties=Lc_helper::get_crop_type_varieties($crop_id,$crop_type_id,$variety_id);
        $variety_ids=array();
        $variety_ids[0]=0;
        foreach($varieties as $result)
        {
            $variety_ids[$result['variety_id']]=$result['variety_id'];
        }

        /*get LC information*/
        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');

        $this->db->join($this->config->item('table_sms_lc_details').' lcd','lcd.lc_id=lc.id','INNER');
        $this->db->select('lcd.*');

        $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lc.currency_id','INNER');
        $this->db->select('currency.name currency_name');

        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lc.date_opening AND fy.date_end>lc.date_opening','INNER');
        $this->db->select('fy.name fiscal_year');

        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lc.principal_id','INNER');
        $this->db->select('principal.name principal_name');

        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
        $this->db->select('pack.name pack_size');

        $this->db->group_by('lc.id');
        $this->db->order_by('lc.id','DESC');
        $this->db->where_in('lcd.variety_id',$variety_ids);
        if(is_numeric($pack_size_id))
        {
            $this->db->where('lcd.pack_size_id',$pack_size_id);
        }
        $this->db->where('lc.'.$date_type.'>='.$date_start.' and lc.'.$date_type.'<='.$date_end);

        if($status_open_forward)
        {
            $this->db->where('lc.status_open_forward',$status_open_forward);
        }
        if($status_release)
        {
            $this->db->where('lc.status_release',$status_release);
        }
        if($status_received)
        {
            $this->db->where('lc.status_received',$status_received);
        }
        if($status_open)
        {
            $this->db->where('lc.status_open',$status_open);
        }
        else
        {
            $this->db->where('lc.status_open !=',$this->config->item('system_status_delete'));
        }

        if($principal_id)
        {
            $this->db->where('lc.principal_id',$principal_id);
        }

        $results=$this->db->get()->result_array();
        $lc_info=array();
        foreach($results as $result)
        {
            $lc_info[$result['variety_id']][]=$result;
        }

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $items=array();
        foreach($varieties as $result)
        {
            if(isset($lc_info[$result['variety_id']]))
            {
                $v=0;
                for($i=0; $i<sizeof($lc_info[$result['variety_id']]); $i++)
                {
                    $info=$this->initialize_row_lc($result['crop_name'],$result['crop_type_name'],$result['variety_name'],$lc_info[$result['variety_id']][$i]['pack_size']);
                    if(!$first_row)
                    {
                        if($prev_crop_name!=$result['crop_name'])
                        {
                            //$info['crop_name']=$result['crop_name'];
                            $prev_crop_name=$result['crop_name'];
                            $prev_type_name=$result['crop_type_name'];

                        }
                        elseif($prev_type_name!=$result['crop_type_name'])
                        {
                            $info['crop_name']='';
                            $prev_type_name=$result['crop_type_name'];
                        }
                        else
                        {
                            $info['crop_name']='';
                            $info['crop_type_name']='';
                        }
                    }
                    else
                    {
                        $prev_crop_name=$result['crop_name'];
                        $prev_type_name=$result['crop_type_name'];
                        $first_row=false;
                    }
                    if($v>0)
                    {
                        $info['variety_name']='';
                        $info['pack_size']='';
                    }
                    $v++;

                    $info['id']=$lc_info[$result['variety_id']][$i]['lc_id'];
                    $info['barcode']=Barcode_helper::get_barcode_lc($lc_info[$result['variety_id']][$i]['lc_id']);
                    $info['lc_number']=$lc_info[$result['variety_id']][$i]['lc_number'];
                    $info['fiscal_year']=$lc_info[$result['variety_id']][$i]['fiscal_year'];
                    $info['month']=$this->lang->line("LABEL_MONTH_".$lc_info[$result['variety_id']][$i]['month_id']);
                    $info['date_opening']=System_helper::display_date($lc_info[$result['variety_id']][$i]['date_opening']);
                    $info['date_expected']=System_helper::display_date($lc_info[$result['variety_id']][$i]['date_expected']);
                    $info['date_awb']=System_helper::display_date($lc_info[$result['variety_id']][$i]['date_awb']);
                    $info['date_forwarded_time']=System_helper::display_date_time($lc_info[$result['variety_id']][$i]['date_open_forward']);
                    $info['date_release']=System_helper::display_date($lc_info[$result['variety_id']][$i]['date_release']);
                    $info['date_released_time']=System_helper::display_date_time($lc_info[$result['variety_id']][$i]['date_release_completed']);
                    $info['date_receive']=System_helper::display_date($lc_info[$result['variety_id']][$i]['date_receive']);
                    $info['date_received_time']=System_helper::display_date_time($lc_info[$result['variety_id']][$i]['date_receive_completed']);
                    $info['date_completed_time']=System_helper::display_date_time($lc_info[$result['variety_id']][$i]['date_receive_completed']);
                    $info['principal_name']=$lc_info[$result['variety_id']][$i]['principal_name'];
                    $info['currency_name']=$lc_info[$result['variety_id']][$i]['currency_name'];
                    $info['quantity_open_kg']=$lc_info[$result['variety_id']][$i]['quantity_open_kg'];
                    $info['status_open_forward']=$lc_info[$result['variety_id']][$i]['status_open_forward'];
                    $info['status_release']=$lc_info[$result['variety_id']][$i]['status_release'];
                    $info['status_received']=$lc_info[$result['variety_id']][$i]['status_receive'];
                    $info['status_open']=$lc_info[$result['variety_id']][$i]['status_open'];

                    $items[]=$info;
                }
            }
        }
        $this->json_return($items);
    }
    private function initialize_row_lc($crop_name,$crop_type_name,$variety_name,$pack_size)
    {
        $row=$this->get_preference_headers('list_lc_wise');
        foreach($row  as $key=>$r)
        {
            $row[$key]='';
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $row['pack_size']=$pack_size;
        return $row;
    }
    private function reset_row($info)
    {
        foreach($info as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')))
            {
                $info[$key]='';
            }
        }
        return $info;
    }
}
