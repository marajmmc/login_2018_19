<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_sale_outlet_invoice extends Root_Controller
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
        $this->lang->language['LABEL_FARMER_NAME']='Customer';
        $this->lang->language['LABEL_DATE_CANCEL']='Cancel date';
        $this->lang->language['LABEL_SALES_PAYMENT_METHOD']='Cash/Credit';
        $this->lang->language['LABEL_AMOUNT_TOTAL']='Total Sales';
        $this->lang->language['LABEL_AMOUNT_DISCOUNT_VARIETY_TOTAL']='V. Discount Total';


        $this->lang->language['LABEL_DISCOUNT_SLAB_PERCENTAGE']='Slab Discount (Percentage)';
        $this->lang->language['LABEL_AMOUNT_DISCOUNT_SELF']='C. Discount';
        $this->lang->language['LABEL_AMOUNT_PAYABLE']='Payable';
        $this->lang->language['LABEL_AMOUNT_PAYABLE_ACTUAL']='Pay(rounded)';
        $this->lang->language['LABEL_AMOUNT_ACTUAL']='Actual Amount';
        $this->lang->language['LABEL_PRICE_UNIT_PACK']='Price/pack';
        $this->lang->language['LABEL_QUANTITY_KG']='Weight(kg)';
        $this->lang->language['LABEL_AMOUNT_VARIETY_TOTAL']='V. Amount';
        $this->lang->language['LABEL_AMOUNT_DISCOUNT_VARIETY']='V. Discount';
        $this->lang->language['LABEL_AMOUNT_VARIETY_ACTUAL']='V. Actual';
        $this->lang->language['LABEL_AMOUNT_VARIETY_ACTUAL_TOTAL']='V. Actual total';


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
        elseif($action=="get_items_list_invoice")
        {
            $this->system_get_items_list_invoice();
        }
        elseif($action=="set_preference_list_invoice")
        {
            $this->system_set_preference('list_invoice');
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
            $data['title']="Invoice wise Outlet sales Report Search";
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
        if($method=='list_invoice')
        {
            $data['outlet_name']= 1;
            $data['invoice_no']= 1;
            $data['farmer_name']= 1;
            $data['date_sale']= 1;
            $data['date_cancel']= 1;
            $data['sales_payment_method']= 1;
            $data['amount_total']= 1;
            $data['amount_discount_variety_total']= 1;
            $data['discount_slab_percentage']= 1;
            $data['amount_discount_self']= 1;
            $data['amount_payable']= 1;
            $data['amount_payable_actual']= 1;
            $data['amount_actual']= 1;
            $data['variety_name']= 1;
            $data['pack_size']= 1;
            $data['price_unit_pack']= 1;
            $data['quantity']= 1;
            $data['quantity_kg']= 1;
            $data['amount_variety_total']= 1;
            $data['amount_discount_variety']= 1;
            $data['amount_variety_actual']= 1;
            $data['amount_variety_actual_total']= 1;
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
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->json_return($ajax);
            }
            $data['options']=$reports;
            $ajax['status']=true;
            $data['title']="Invoice wise Outlet sales Report";
            $method='list_invoice';
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_invoice",$data,true));

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
    private function system_get_items_list_invoice()
    {
        $outlet_id=$this->input->post('outlet_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $this->db->from($this->config->item('table_pos_sale_details').' sd');
        $this->db->select('sd.sale_id,sd.pack_size,sd.price_unit_pack,sd.price_unit_pack,sd.quantity,sd.amount_total amount_variety_total,sd.amount_discount_variety,sd.amount_payable_actual amount_variety_payable_actual');
        $this->db->join($this->config->item('table_pos_sale').' sale','sale.id = sd.sale_id','INNER');
        $this->db->select('sale.status,sale.date_sale,sale.date_cancel,sale.sales_payment_method,sale.amount_total,sale.amount_discount_variety amount_discount_variety_total,sale.discount_self_percentage,sale.amount_discount_self');
        $this->db->select('sale.amount_payable,sale.amount_payable_actual');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet','outlet.customer_id = sale.outlet_id AND outlet.revision=1','INNER');
        $this->db->select('outlet.name outlet_name');

        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = sale.farmer_id','INNER');
        $this->db->select('farmer.name farmer_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = sd.variety_id','INNER');
        $this->db->select('v.name variety_name');

        $this->db->where('sale.date_sale >=',$date_start);
        $this->db->where('sale.date_sale <=',$date_end);
        if($outlet_id>0)
        {
            $this->db->where('sale.outlet_id',$outlet_id);
        }
        $this->db->order_by('sd.sale_id','DESC');
        $results=$this->db->get()->result_array();
        $items=array();
        $sl_no=0;
        $prev_invoice=0;
        foreach($results as $result)
        {
            $item=array();
            $item['status']=$result['status'];
            $item['outlet_name']=$result['outlet_name'];
            $item['invoice_no']=Barcode_helper::get_barcode_sales($result['sale_id']);
            $item['farmer_name']=$result['farmer_name'];
            $item['date_sale']=System_helper::display_date($result['date_sale']);
            if($result['date_cancel']>0)
            {
                $item['date_cancel']=System_helper::display_date_time($result['date_cancel']);
            }
            else
            {
                $item['date_cancel']='';
            }
            $item['sales_payment_method']=$result['sales_payment_method'];
            $item['amount_total']=$result['amount_total'];
            $item['amount_discount_variety_total']=$result['amount_discount_variety_total'];
            $item['discount_slab_percentage']=$result['discount_self_percentage'];
            $item['amount_discount_self']=$result['amount_discount_self'];
            $item['amount_payable']=$result['amount_payable'];
            $item['amount_payable_actual']=$result['amount_payable_actual'];
            $item['amount_variety_total']=$result['amount_variety_total'];
            $item['amount_discount_variety']=$result['amount_discount_variety'];
            $item['amount_variety_actual']=$result['amount_variety_payable_actual'];

            $item['amount_variety_actual_total']=$result['amount_total']-$result['amount_discount_variety_total'];;

            if($result['status']==$this->config->item('system_status_active'))
            {
                $item['amount_actual']=$result['amount_payable_actual'];
            }
            else
            {
                if($result['date_sale']<$date_start)
                {
                    $item['amount_actual']=0-$result['amount_payable'];

                }
                elseif($result['date_cancel']>$date_end)
                {
                    $item['amount_actual']=$result['amount_payable_actual'];
                }
                else
                {
                    $item['amount_actual']=0;
                }
            }
            $item['variety_name']=$result['variety_name'];
            $item['pack_size']=$result['pack_size'];
            $item['price_unit_pack']=$result['price_unit_pack'];
            $item['quantity']=$result['quantity'];
            $item['quantity_kg']=System_helper::get_string_kg($result['quantity']*$result['pack_size']/1000);
            if($prev_invoice==$result['sale_id'])
            {
                $item['date_sale']='';
                $item['date_cancel']='';
                $item['amount_total']=0;
                $item['amount_discount_variety']=0;
                $item['discount_slab_percentage']='';
                $item['amount_discount_self']=0;
                $item['amount_payable']=0;
                $item['amount_payable_actual']=0;
                $item['amount_actual']=0;
                $item['amount_variety_actual_total']=0;
            }
            $prev_invoice=$result['sale_id'];
            $items[]=$item;
        }

        $this->json_return($items);
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
}
