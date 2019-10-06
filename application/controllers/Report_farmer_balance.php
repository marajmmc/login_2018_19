<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_farmer_balance extends Root_Controller
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
        $this->language_labels();

    }
    private function language_labels()
    {
        $this->lang->language['LABEL_AMOUNT_CREDIT_LIMIT']='Credit Limit';
        $this->lang->language['LABEL_AMOUNT_CREDIT_BALANCE']='Available Credit';
        $this->lang->language['LABEL_AMOUNT_CREDIT_DUE']='Due';
        $this->lang->language['LABEL_AMOUNT_CREDIT_ADVANCE']='Advance';
        $this->lang->language['LABEL_ACTION_TRANSACTION']='Action';
        $this->lang->language['LABEL_ACTION_NO']='Invoice/Payment No';
        $this->lang->language['LABEL_AMOUNT_DEBIT']='Debit Amount';
        $this->lang->language['LABEL_AMOUNT_CREDIT']='Credit Amount';
        $this->lang->language['LABEL_AMOUNT_BALANCE']='Balance';
    }
    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="get_dealers")
        {
            $this->system_get_dealers();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        /*elseif($action=="get_items_list_dealers")
        {
            $this->system_get_items_list_dealers();
        }*/
        elseif($action=="get_items_list_outlets")
        {
            $this->system_get_items_list_outlets();
        }
        elseif($action=="get_items_list_dealers_date_end")
        {
            $this->system_get_items_list_dealers_date_end();
        }
        elseif($action=="get_items_list_dealer")
        {
            $this->system_get_items_list_dealer();
        }
        elseif($action=="set_preference_list_dealers")
        {
            $this->system_set_preference('list_dealers');
        }
        elseif($action=="set_preference_list_outlets")
        {
            $this->system_set_preference('list_outlets');
        }
        elseif($action=="set_preference_list_dealers_date_end")
        {
            $this->system_set_preference('list_dealers_date_end');
        }
        elseif($action=="set_preference_list_dealer")
        {
            $this->system_set_preference('list_dealer');
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
            $data['title']="Dealers Balance Report Search";
            $ajax['status']=true;
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

            //$this->db->order_by('division.ordering','ASC');
            //$this->db->order_by('zone.ordering','ASC');
            //$this->db->order_by('t.ordering','ASC');
            //$this->db->order_by('d.ordering','ASC');
            $this->db->order_by('cus_info.ordering','ASC');
            $data['outlets']=$this->db->get()->result_array();

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
        /*if($method=='list_dealers')
        {
            $data['barcode']= 1;
            $data['name']= 1;
            $data['amount_credit_limit']= 1;
            $data['amount_credit_balance']= 1;
            $data['amount_credit_due']= 1;
            $data['amount_credit_advance']= 1;
        }*/
        if($method=='list_outlets')
        {
            $data['name']= 1;
            $data['amount_credit_due']= 1;
            $data['amount_credit_advance']= 1;
        }
        else if($method=='list_dealers_date_end')
        {
            $data['barcode']= 1;
            $data['name']= 1;
            //$data['amount_credit_limit']= 1;
            //$data['amount_credit_balance']= 1;
            $data['amount_credit_due']= 1;
            $data['amount_credit_advance']= 1;
        }
        else if($method=='list_dealer')
        {
            $data['date']= 1;
            $data['action_transaction']= 1;
            $data['action_no']= 1;
            $data['amount_debit']= 1;
            $data['amount_credit']= 1;
            $data['amount_balance']= 1;
        }
        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();

        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_end']=$reports['date_end']+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['farmer_id']>0)
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
            if($reports['farmer_id']>0)
            {

                $result=Query_helper::get_info($this->config->item('table_pos_setup_farmer_farmer'),array('name text'),array('id ='.$reports['farmer_id']),1);
                $data['title']='Dealer ( '.$result['text'].' ) Balance Report';
                $method='list_dealer';
                $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_dealer",$data,true));
            }
            else if($reports['outlet_id']>0)
            {

                $result=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('name text'),array('customer_id ='.$reports['outlet_id'],'revision =1'),1);
                $data['title']='Dealers ( Showroom ::'.$result['text'].' ) Balance Report';
                $method='list_dealers_date_end';
                $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_dealers_date_end",$data,true));
            }
            else
            {
                //$data['title']='Dealers(All Showroom) Balance Report';
                $data['title']='All Showrooms Balance Report';
                //$method='list_dealers';
                $method='list_outlets';
                $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
                //$ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_dealers",$data,true));
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_outlets",$data,true));
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
    /*private function system_get_items_list_dealers()
    {
        $outlet_id=$this->input->post('outlet_id');
        $outlet_ids=array();
        if($outlet_id>0)
        {
            $outlet_ids[]=$outlet_id;
        }
        else
        {
            $this->db->from($this->config->item('table_login_csetup_customer').' cus');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = cus.id','INNER');
            $this->db->select('cus.id value');
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
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $outlet_ids[]=$result['value'];
            }
        }
        if(!(sizeof($outlet_ids)>0))
        {
            $this->json_return(array());
        }
        //$user=User_helper::get_user();
        $this->db->from($this->config->item('table_pos_setup_farmer_farmer').' f');
        $this->db->select('f.id,f.name,f.amount_credit_limit,f.amount_credit_balance');
        $this->db->join($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet','farmer_outlet.farmer_id = f.id and farmer_outlet.revision =1','INNER');
        $this->db->where_in('farmer_outlet.outlet_id',$outlet_ids);
        $this->db->where('f.amount_credit_limit > ',0);
        $this->db->order_by('f.id DESC');
        $this->db->group_by('f.id');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['barcode']=Barcode_helper::get_barcode_farmer($item['id']);
            $item['amount_credit_due']=$item['amount_credit_limit']-$item['amount_credit_balance'];
            if($item['amount_credit_due']>0)
            {
                $item['amount_credit_advance']=0;
            }
            else
            {
                $item['amount_credit_advance']=0-$item['amount_credit_due'];
                $item['amount_credit_due']=0;
            }
        }
        $this->json_return($items);
    }*/
    private function system_get_items_list_outlets()
    {
        //$outlet_id=$this->input->post('outlet_id');
        $date_end=$this->input->post('date_end');

        //$user=User_helper::get_user();
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

        //$this->db->order_by('division.ordering','ASC');
        //$this->db->order_by('zone.ordering','ASC');
        //$this->db->order_by('t.ordering','ASC');
        //$this->db->order_by('d.ordering','ASC');
        $this->db->order_by('cus_info.ordering','ASC');
        $outlets=$this->db->get()->result_array();
        //sales

        $this->db->from($this->config->item('table_pos_sale').' sale');
        $this->db->select('sale.outlet_id');
        $this->db->select('SUM(sale.amount_payable_actual) amount_debit_total',false);
        $this->db->select('SUM(CASE WHEN sale.sales_payment_method="Cash" then sale.amount_payable_actual ELSE 0 END) amount_sale_cash',false);
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = sale.farmer_id','INNER');

        $this->db->where_in('sale.status',$this->config->item('system_status_active'));
        $this->db->where('sale.date_sale <=',$date_end);
        $this->db->where('farmer.amount_credit_limit >',0);
        $this->db->group_by('sale.outlet_id');
        $results=$this->db->get()->result_array();
        $due_sales=array();
        foreach($results as $result)
        {
            $due_sales[$result['outlet_id']]=$result['amount_debit_total']-$result['amount_sale_cash'];//due
        }

        //previous payment
        $this->db->from($this->config->item('table_pos_farmer_credit_payment').' dp');
        $this->db->select('dp.outlet_id');
        $this->db->select('SUM(dp.amount) amount_payment_total',false);
        $this->db->where('dp.status',$this->config->item('system_status_active'));
        $this->db->where('dp.date_payment <=',$date_end);
        $this->db->group_by('dp.outlet_id');
        $results=$this->db->get()->result_array();
        $payments=array();
        foreach($results as $result)
        {
            $payments[$result['outlet_id']]=$result['amount_payment_total'];
        }
        $items=array();
        foreach($outlets as $outlet)
        {
            $item=array();
            $item['name']=$outlet['text'];
            $due=0;
            if(isset($due_sales[$outlet['value']]))
            {
                $due=$due_sales[$outlet['value']];
            }
            $payment=0;
            if(isset($payments[$outlet['value']]))
            {
                $payment=$payments[$outlet['value']];
            }

            if($due>$payment)
            {
                $item['amount_credit_advance']=0;
                $item['amount_credit_due']=$due-$payment;
            }
            else
            {
                $item['amount_credit_advance']=$payment-$due;
                $item['amount_credit_due']=0;
            }

            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_get_items_list_dealers_date_end()
    {
        $outlet_id=$this->input->post('outlet_id');
        $date_end=$this->input->post('date_end');

        //$user=User_helper::get_user();
        $this->db->from($this->config->item('table_pos_setup_farmer_farmer').' f');
        $this->db->select('f.id,f.name');
        $this->db->join($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet','farmer_outlet.farmer_id = f.id and farmer_outlet.revision =1','INNER');
        $this->db->where('farmer_outlet.outlet_id',$outlet_id);
        $this->db->where('f.amount_credit_limit > ',0);
        $this->db->order_by('f.id DESC');
        $this->db->group_by('f.id');
        $farmers=$this->db->get()->result_array();
        $farmer_ids[0]=0;
        foreach($farmers as $farmer)
        {
            $farmer_ids[$farmer['id']]=$farmer['id'];
        }

        //sales

        $this->db->from($this->config->item('table_pos_sale').' sale');
        $this->db->select('sale.farmer_id');
        $this->db->select('SUM(sale.amount_payable_actual) amount_debit_total',false);
        $this->db->select('SUM(CASE WHEN sale.sales_payment_method="Cash" then sale.amount_payable_actual ELSE 0 END) amount_sale_cash',false);

        $this->db->where_in('sale.status',$this->config->item('system_status_active'));
        $this->db->where_in('sale.farmer_id',$farmer_ids);
        $this->db->where('sale.date_sale <=',$date_end);
        $this->db->group_by('sale.farmer_id');
        $results=$this->db->get()->result_array();
        $due_sales=array();
        foreach($results as $result)
        {
            $due_sales[$result['farmer_id']]=$result['amount_debit_total']-$result['amount_sale_cash'];//due
        }

        //previous payment
        $this->db->from($this->config->item('table_pos_farmer_credit_payment').' dp');
        $this->db->select('dp.farmer_id');
        $this->db->select('SUM(dp.amount) amount_payment_total',false);
        $this->db->where('dp.status',$this->config->item('system_status_active'));
        $this->db->where_in('dp.farmer_id',$farmer_ids);
        $this->db->where('dp.date_payment <=',$date_end);
        $this->db->group_by('dp.farmer_id');
        $results=$this->db->get()->result_array();
        $payments=array();
        foreach($results as $result)
        {
            $payments[$result['farmer_id']]=$result['amount_payment_total'];
        }
        $items=array();
        foreach($farmers as $farmer)
        {
            $item=array();
            $item['barcode']=Barcode_helper::get_barcode_farmer($farmer['id']);
            $item['name']=$farmer['name'];
            $due=0;
            if(isset($due_sales[$farmer['id']]))
            {
                $due=$due_sales[$farmer['id']];
            }
            $payment=0;
            if(isset($payments[$farmer['id']]))
            {
                $payment=$payments[$farmer['id']];
            }

            if($due>$payment)
            {
                $item['amount_credit_advance']=0;
                $item['amount_credit_due']=$due-$payment;
            }
            else
            {
                $item['amount_credit_advance']=$payment-$due;
                $item['amount_credit_due']=0;
            }

            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_get_items_list_dealer()
    {
        $farmer_id=$this->input->post('farmer_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');
        $items=array();
        $debit_sub_total=0;
        //$debit_initial=0;
        $credit_sub_total=0;
        //$credit_initial=0;
        //$balance=0;

        //previous sale

        $this->db->from($this->config->item('table_pos_sale').' sale');
        $this->db->select('SUM(sale.amount_payable_actual) amount_debit_total',false);
        $this->db->select('SUM(CASE WHEN sale.sales_payment_method="Cash" then sale.amount_payable_actual ELSE 0 END) amount_sale_cash',false);

        $this->db->where_in('sale.status',$this->config->item('system_status_active'));
        $this->db->where('sale.farmer_id',$farmer_id);
        $this->db->where('sale.date_sale <',$date_start);
        $result=$this->db->get()->row_array();
        $item=array();
        $item['date']="Initial";
        $item['action_transaction']="-";
        $item['action_no']="-";
        $item['amount_debit']=$result['amount_debit_total'];
        $item['amount_credit']=$result['amount_sale_cash'];//+payment

        //previous payment
        $this->db->from($this->config->item('table_pos_farmer_credit_payment').' dp');
        $this->db->select('SUM(dp.amount) amount_payment_total',false);
        $this->db->where('dp.status',$this->config->item('system_status_active'));
        $this->db->where('dp.farmer_id',$farmer_id);
        $this->db->where('dp.date_payment <',$date_start);
        $result=$this->db->get()->row_array();
        $item['amount_credit']=$item['amount_credit']+$result['amount_payment_total'];

        $debit_initial=$item['amount_debit'];
        $credit_initial=$item['amount_credit'];
        $balance=$item['amount_balance']=$item['amount_credit']-$item['amount_debit'];
        $items[]=$item;//previous item

        //current payment
        $this->db->from($this->config->item('table_pos_farmer_credit_payment').' dp');
        $this->db->select('dp.id,dp.date_payment,dp.amount');
        $this->db->where('dp.status',$this->config->item('system_status_active'));
        $this->db->where('dp.farmer_id',$farmer_id);
        $this->db->where('dp.date_payment >=',$date_start);
        $this->db->where('dp.date_payment <=',$date_end);
        $this->db->order_by('dp.date_payment','ASC');
        $payments=$this->db->get()->result_array();
        //current sales
        $this->db->from($this->config->item('table_pos_sale').' sale');
        $this->db->select('sale.id,sale.date_sale,sale.sales_payment_method,sale.amount_payable_actual');
        $this->db->where_in('sale.status',$this->config->item('system_status_active'));
        $this->db->where('sale.farmer_id',$farmer_id);
        $this->db->where('sale.date_sale >=',$date_start);
        $this->db->where('sale.date_sale <=',$date_end);
        $this->db->order_by('sale.date_sale','ASC');
        $invoices=$this->db->get()->result_array();
        $i=0;
        foreach($payments as $payment)
        {
            while(($i<sizeof($invoices)) &&($invoices[$i]['date_sale']<$payment['date_payment']))
            {
                $item=$this->get_row_invoice($invoices[$i],$debit_sub_total,$credit_sub_total,$balance);
                $i++;
                $items[]=$item;
            }
            $item=array();
            $item['date']=System_helper::display_date_time($payment['date_payment']);
            $item['action_transaction']="Payment";
            $item['action_no']=Barcode_helper::get_barcode_dealer_payment($payment['id']);
            $item['amount_debit']=0;
            $item['amount_credit']=$payment['amount'];
            $credit_sub_total+=$payment['amount'];
            $balance+=$item['amount_credit'];
            $item['amount_balance']=$balance;
            $items[]=$item;

        }
        while(($i<sizeof($invoices)))
        {
            $item=$this->get_row_invoice($invoices[$i],$debit_sub_total,$credit_sub_total,$balance);
            $i++;
            $items[]=$item;
        }
        $item=array();
        $item['date']="Sub Total";
        $item['action_transaction']="-";
        $item['action_no']="-";
        $item['amount_debit']=$debit_sub_total;
        $item['amount_credit']=$credit_sub_total;
        $item['amount_balance']=$balance;
        $items[]=$item;

        $item=array();
        $item['date']="Total";
        $item['action_transaction']="-";
        $item['action_no']="-";
        $item['amount_debit']=$debit_initial+$debit_sub_total;
        $item['amount_credit']=$credit_initial+$credit_sub_total;
        $item['amount_balance']=$balance;
        $items[]=$item;
        $this->json_return($items);
    }
    private function get_row_invoice($result,&$debit_sub_total,&$credit_sub_total,&$balance)
    {
        $item=array();
        $item['date']=System_helper::display_date_time($result['date_sale']);
        $item['action_transaction']="Purchase";
        $item['action_no']=Barcode_helper::get_barcode_sales($result['id']);
        if($result['sales_payment_method']=="Cash")
        {
            $item['amount_debit']=$result['amount_payable_actual'];
            $item['amount_credit']=$result['amount_payable_actual'];

        }
        else
        {
            $item['amount_debit']=$result['amount_payable_actual'];
            $item['amount_credit']=0;
            $balance=$balance-$result['amount_payable_actual'];
        }
        $debit_sub_total+=$item['amount_debit'];
        $credit_sub_total+=$item['amount_credit'];
        $item['amount_balance']=$balance;
        return $item;

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
    private function system_get_dealers()
    {
        $outlet_id=$this->input->post('outlet_id');
        $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
        $this->db->select('farmer_outlet.farmer_id value');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id=farmer_outlet.farmer_id','INNER');
        $this->db->select('farmer.name text');
        $this->db->where('farmer.status',$this->config->item('system_status_active'));
        $this->db->where('farmer.farmer_type_id > ',1);
        $this->db->where('farmer_outlet.revision',1);
        $this->db->where('farmer_outlet.outlet_id',$outlet_id);
        $this->db->where('farmer.amount_credit_limit > ',0);
        $data['items']=$this->db->get()->result_array();
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>'#farmer_id',"html"=>$this->load->view("dropdown_with_select",$data,true));
        $this->json_return($ajax);
    }
}
