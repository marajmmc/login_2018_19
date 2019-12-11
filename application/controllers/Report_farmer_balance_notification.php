<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_farmer_balance_notification extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->locations = User_helper::get_locations();
        $this->user = User_helper::get_user();
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->language_labels();
    }

    private function language_labels()
    {
        $this->lang->language['LABEL_AMOUNT_CREDIT_LIMIT'] = 'Credit Limit';
        $this->lang->language['LABEL_AMOUNT_CREDIT_BALANCE'] = 'Available Credit';
        $this->lang->language['LABEL_AMOUNT_CREDIT_DUE'] = 'Due';
        $this->lang->language['LABEL_DATE_LAST_PAYMENT'] = 'Last Payment Date';
        $this->lang->language['LABEL_DAY_LAST_PAYMENT'] = 'Last Payment days';
    }

    public function index($action = "search", $id = 0)
    {
        if ($action == "search")
        {
            $this->system_search();
        }
        elseif ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference();
        }
        elseif ($action == "save_preference")
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
        $data = array();
        if($method == 'search'){
            $data['id'] = 1;
            $data['outlet_name'] = 1;
            $data['barcode'] = 1;
            $data['name'] = 1;
            $data['amount_credit_limit'] = 1;
            $data['amount_credit_balance'] = 1;
            $data['amount_credit_due'] = 1;
            $data['date_last_payment'] = 1;
            $data['day_last_payment'] = 1;
        }
        return $data;
    }

    private function system_set_preference()
    {
        $user = User_helper::get_user();
        $method = 'search';
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

    private function system_search()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['divisions'] = Query_helper::get_info($this->config->item('table_login_setup_location_divisions'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'));
            $data['zones'] = array();
            $data['territories'] = array();
            $data['districts'] = array();
            $data['outlets'] = array();
            if ($this->locations['division_id'] > 0)
            {
                $data['zones'] = Query_helper::get_info($this->config->item('table_login_setup_location_zones'), array('id value', 'name text'), array('division_id =' . $this->locations['division_id'], 'status ="' . $this->config->item('system_status_active') . '"'));
                if ($this->locations['zone_id'] > 0)
                {
                    $data['territories'] = Query_helper::get_info($this->config->item('table_login_setup_location_territories'), array('id value', 'name text'), array('zone_id =' . $this->locations['zone_id'], 'status ="' . $this->config->item('system_status_active') . '"'));
                    if ($this->locations['territory_id'] > 0)
                    {
                        $data['districts'] = Query_helper::get_info($this->config->item('table_login_setup_location_districts'), array('id value', 'name text'), array('territory_id =' . $this->locations['territory_id'], 'status ="' . $this->config->item('system_status_active') . '"'));
                        if ($this->locations['district_id'] > 0)
                        {
                            $data['outlets'] = Query_helper::get_info($this->config->item('table_login_csetup_cus_info'), array('customer_id value', 'name text'), array('type =1', 'revision =1', 'district_id =' . $this->locations['district_id']), 0, 0, array('ordering ASC'));
                        }
                    }

                }
            }

            $data['title'] = "Dealers Balance Report Notification Search";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/search", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'search';

            $reports = $this->input->post('report');
            $data['options'] = $reports;

            $data['title'] = "Dealers Balance Report Notification";
            $ajax['status'] = true;
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['system_content'][] = array("id" => "#system_report_container", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $time=time();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');

        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');

        $this->db->where('outlet_info.revision',1);
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));
        if($division_id>0)
        {
            $this->db->where('zones.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zones.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('territories.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('districts.id',$district_id);
                        if($outlet_id>0)
                        {
                            $this->db->where('outlet_info.customer_id',$outlet_id);
                        }
                    }
                }
            }
        }
        $this->db->order_by('outlet_info.ordering');
        $results=$this->db->get()->result_array();

        $outlets=array();
        $outlet_ids=array(0);
        foreach($results as $result)
        {
            $outlets[$result['outlet_id']]=$result['outlet_name'];
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
        }
        $this->db->from($this->config->item('table_pos_farmer_credit_payment') . ' payment');
        $this->db->select('MAX( payment.date_payment ) AS date_last_payment');
        $this->db->select('payment.farmer_id');
        $this->db->where_in('payment.outlet_id', $outlet_ids);
        $this->db->group_by('payment.farmer_id');
        $results=$this->db->get()->result_array();
        $payment=array();
        foreach($results as $result)
        {
            $payment[$result['farmer_id']]=$result['date_last_payment'];
        }



        $this->db->from($this->config->item('table_pos_setup_farmer_farmer') . ' farmer');
        $this->db->select('farmer.id, farmer.name, farmer.amount_credit_limit, farmer.amount_credit_balance');

        $this->db->join($this->config->item('table_pos_setup_farmer_outlet') . ' farmer_outlet', 'farmer_outlet.farmer_id = farmer.id AND farmer_outlet.revision =1', 'INNER');
        $this->db->select('farmer_outlet.outlet_id');

        //$this->db->join($this->config->item('table_pos_farmer_credit_payment').' payment', 'payment.farmer_id = farmer.id AND payment.outlet_id = farmer_outlet.outlet_id', 'INNER');
        //$this->db->select('MAX( payment.date_payment ) AS date_last_payment');

        $this->db->where('farmer.amount_credit_limit > ', 0);
        $this->db->where_in('farmer_outlet.outlet_id', $outlet_ids);
        //$this->db->group_by('farmer.id');
        $this->db->order_by('farmer_outlet.id');
        $this->db->order_by('farmer.id DESC');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['outlet_name'] = $outlets[$item['outlet_id']];
            $item['barcode'] = Barcode_helper::get_barcode_farmer($item['id']);
            $item['amount_credit_due'] = $item['amount_credit_limit'] - $item['amount_credit_balance'];
            if(isset($payment[$item['id']]))
            {
                $item['date_last_payment'] = System_helper::display_date($payment[$item['id']]);
                $item['day_last_payment'] = intval(($time-$payment[$item['id']])/(3600*24));
            }
            else
            {
                $item['date_last_payment']=0;
                $item['day_last_payment']=0;
            }

        }

        $this->json_return($items);
    }
}
