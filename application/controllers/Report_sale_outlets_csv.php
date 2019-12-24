<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_sale_outlets_csv extends CI_Controller
{
    public $current_user;
    public $controller_url;
    public $controller_main_url;

    public function __construct()
    {
        parent::__construct();
        $this->controller_url = strtolower(get_class($this));
        $this->controller_main_url = strtolower(str_replace("_csv", "", $this->controller_url));
        $user = User_helper::get_user();
        if (!$user)
        {
            echo 'Please Login and Try Again';
            die();
        }
        $this->lang->load('report_sale');
        $this->language_labels();
        $this->load->helper('csv');
    }

    private function language_labels()
    {
        $this->lang->language['LABEL_DEALER_NAME'] = 'Dealer Name';
    }
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='list_outlets_sales_cash')
        {
            $data['sl_no']= 1;
            $data['outlet']= 1;
            $data['amount_payable']= 1;
            $data['amount_sale_credit']= 1;
            $data['amount_sale_cash']= 1;
            $data['amount_cash_payment']= 1;
            $data['amount_cash_total']= 1;
        }
        else if($method=='list_outlets_dealers_varieties')
        {
            $data['outlet_name'] = 1;
            $data['dealer_name'] = 1;
            $data['amount_total'] = 1;
            $data['quantity_pkt'] = 1;
            $data['quantity_kg'] = 1;
            $data['amount'] = 1;
        }
        return $data;
    }

    public function system_list_outlets_dealers_varieties($params = array())
    {
        $options = json_decode(urldecode($params), true);
        $division_id=$options['division_id'];
        $zone_id=$options['zone_id'];
        $territory_id=$options['territory_id'];
        $district_id=$options['district_id'];
        $outlet_id=$options['outlet_id'];
        $date_end=$options['date_end'];
        $date_start=$options['date_start'];

        $crop_id=$options['crop_id'];
        $crop_type_id=$options['crop_type_id'];
        $variety_id=$options['variety_id'];
        $pack_size_id=$options['pack_size_id'];

        //varieties

        $this->db->from($this->config->item('table_sms_stock_summary_variety') . ' stock');

        $this->db->join($this->config->item('table_login_setup_classification_pack_size') . ' pack_size', 'pack_size.id = stock.pack_size_id');
        $this->db->select('pack_size.id pack_size_id, pack_size.name pack_size_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' v', 'v.id = stock.variety_id');
        $this->db->select('v.id variety_id, v.name variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = v.crop_type_id');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id');
        $this->db->select('crop.id crop_id, crop.name crop_name');


        if ($crop_id > 0)
        {
            $this->db->where('crop_type.crop_id', $crop_id);
            if ($crop_type_id > 0)
            {
                $this->db->where('crop_type.id', $crop_type_id);
            }
            if ($variety_id > 0)
            {
                $this->db->where('v.id', $variety_id);
            }
        }
        $this->db->where('v.status', $this->config->item('system_status_active'));
        if ($pack_size_id > 0)
        {
            $this->db->where('pack_size.id', $pack_size_id);
        }
        $this->db->where('v.whose', 'ARM');
        $this->db->where('stock.pack_size_id >', 0);
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $this->db->group_by('stock.variety_id');
        $this->db->group_by('stock.pack_size_id');
        $arm_varieties = $this->db->get()->result_array();
        //dealers with initial value
        $this->db->from($this->config->item('table_pos_setup_farmer_farmer') . ' farmer');
        $this->db->select('farmer.id, farmer.name dealer_name, farmer.mobile_no');

        $this->db->join($this->config->item('table_pos_setup_farmer_outlet') . ' farmer_outlet', 'farmer_outlet.farmer_id = farmer.id', 'INNER');
        $this->db->select('farmer_outlet.outlet_id');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' outlet_info', 'outlet_info.customer_id = farmer_outlet.outlet_id', 'INNER');
        $this->db->select('outlet_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');


        $this->db->order_by('outlet_info.ordering');
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
        $this->db->where('outlet_info.revision',1);
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));

        $this->db->where('farmer.status', $this->config->item('system_status_active'));
        $this->db->where('farmer.farmer_type_id > ', 1);
        $this->db->where('farmer_outlet.revision', 1);
        $this->db->order_by('outlet_info.ordering');
        $this->db->order_by('farmer.id','DESC');
        $dealers= $this->db->get()->result_array();
        $dealer_ids[0]=0;
        foreach($dealers as $dealer)
        {
            $dealer_ids[$dealer['id']]=$dealer['id'];
        }
        //sales
        $this->db->from($this->config->item('table_pos_sale_details').' details');
        $this->db->select('details.variety_id, details.pack_size, details.pack_size_id');
        $this->db->select('SUM(details.quantity) quantity_sale_pkt');
        $this->db->select('SUM((details.pack_size*details.quantity)/1000) quantity_sale_kg');
        $this->db->select('SUM(details.amount_payable_actual-((details.amount_payable_actual*sale.discount_self_percentage)/100)) amount_total');

        $this->db->join($this->config->item('table_pos_sale').' sale','sale.id = details.sale_id','INNER');
        $this->db->select('sale.farmer_id');


        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=details.variety_id', 'INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');


        $this->db->where('sale.date_sale >=',$date_start);
        $this->db->where('sale.date_sale <=',$date_end);
        $this->db->where('sale.status',$this->config->item('system_status_active'));
        if($crop_id>0)
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        if($pack_size_id>0)
        {
            $this->db->where('details.pack_size_id',$pack_size_id);
        }
        $this->db->where_in('sale.farmer_id',$dealer_ids);
        $this->db->group_by('sale.farmer_id,details.variety_id, details.pack_size_id');
        $results=$this->db->get()->result_array();
        $sales=array();
        foreach($results as $result)
        {
            $sales[$result['farmer_id']][$result['variety_id']][$result['pack_size_id']]=$result;
        }


        $items=array();
        foreach($dealers as $item)
        {
            $item['amount_total'] = 0;
            foreach($arm_varieties as $variety)
            {
                if(isset($sales[$item['id']][$variety['variety_id']][$variety['pack_size_id']]))
                {
                    $item['quantity_'.$variety['variety_id'].'_'.$variety['pack_size_id'].'_pkt']=$sales[$item['id']][$variety['variety_id']][$variety['pack_size_id']]['quantity_sale_pkt'];
                    $item['quantity_'.$variety['variety_id'].'_'.$variety['pack_size_id'].'_kg']=$sales[$item['id']][$variety['variety_id']][$variety['pack_size_id']]['quantity_sale_kg'];
                    $item['amount_'.$variety['variety_id'].'_'.$variety['pack_size_id']]=$sales[$item['id']][$variety['variety_id']][$variety['pack_size_id']]['amount_total'];
                }
                else
                {
                    $item['quantity_'.$variety['variety_id'].'_'.$variety['pack_size_id'].'_pkt']=0;
                    $item['quantity_'.$variety['variety_id'].'_'.$variety['pack_size_id'].'_kg']=0;
                    $item['amount_'.$variety['variety_id'].'_'.$variety['pack_size_id']]=0;
                }
                $item['amount_total'] += $item['amount_'.$variety['variety_id'].'_'.$variety['pack_size_id']];
            }

            $items[]=$item;
        }

        $user = User_helper::get_user();
        $method='list_outlets_dealers_varieties';
        $preference= System_helper::get_preference($user->user_id, $this->controller_main_url, $method, $this->get_preference_headers($method));

        $fields_price=array();
        $fields_kg=array();

        $preference_actual=array();
        $preference_actual['outlet_name']=$preference['outlet_name'];
        $preference_actual['dealer_name']=$preference['dealer_name'];
        $preference_actual['amount_total']=$preference['amount_total'];

        foreach ($arm_varieties as $variety)
        {
            $header_format = '';
            $header_format .= $this->lang->line('LABEL_CROP_NAME') . ': ' . $variety['crop_name'] . chr(10);
            $header_format .= $this->lang->line('LABEL_CROP_TYPE_NAME') . ': ' . $variety['crop_type_name'] . chr(10);
            $header_format .= $this->lang->line('LABEL_VARIETY_NAME') . ': ' . $variety['variety_name'] . chr(10);
            $header_format .= $this->lang->line('LABEL_PACK_SIZE') . ': ' . $variety['pack_size_name'] . chr(10);

            // NOTE: Here 10 in 'chr()' -function represents ASCII value for 'Enter' -key.
            //$row['variety_header'] = $header_format;
            if($preference['quantity_pkt']==1)
            {
                $preference_actual['quantity_'.$variety['variety_id'].'_'.$variety['pack_size_id'].'_pkt']=1;
                $this->lang->language['LABEL_' . strtoupper('quantity_'.$variety['variety_id'].'_'.$variety['pack_size_id'].'_pkt')] = $header_format.'pkt';
            }
            if($preference['quantity_kg']==1)
            {
                $preference_actual['quantity_'.$variety['variety_id'].'_'.$variety['pack_size_id'].'_kg']=1;
                $fields_kg[]='quantity_'.$variety['variety_id'].'_'.$variety['pack_size_id'].'_kg';
                $this->lang->language['LABEL_' . strtoupper('quantity_'.$variety['variety_id'].'_'.$variety['pack_size_id'].'_kg')] = $header_format.'kg';
            }
            if($preference['amount']==1)
            {
                $preference_actual['amount_'.$variety['variety_id'].'_'.$variety['pack_size_id']]=1;
                $fields_price[]='amount_'.$variety['variety_id'].'_'.$variety['pack_size_id'];
                $this->lang->language['LABEL_' . strtoupper('amount_'.$variety['variety_id'].'_'.$variety['pack_size_id'])] = $header_format.'amount';
            }
        }


        Csv_helper::get_csv($items, $preference_actual, 'dealer_wise_product_sales.csv', $fields_price,$fields_kg);
    }
}
