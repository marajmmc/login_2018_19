<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_purchase_lc extends Root_Controller
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
        $this->common_view_location='report_purchase_lc';
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_FISCAL_YEARS']='Fiscal Years';
        $this->lang->language['LABEL_QUANTITY_PKT']='Quantity (pkt)';
        $this->lang->language['LABEL_QUANTITY_KG']='Quantity (kg)';
        $this->lang->language['LABEL_PRICE_COMPLETE_VARIETY_TAKA']='Variety Price';
        $this->lang->language['LABEL_PRICE_COMPLETE_OTHER_TAKA']='Air Freight & Docs';
        $this->lang->language['LABEL_PRICE_DC_EXPENSE_TAKA']='DC Expense';
        $this->lang->language['LABEL_PRICE_TOTAL_TAKA']='Total';
        $this->lang->language['LABEL_PRICE_PER_KG']='Unit (Kg) Price';
    }
    public function index($action="search")
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
            $this->system_set_preference('list_variety');
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
        if($method=='list_variety')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['pack_size']= 1;
            $data['quantity_pkt']= 1;
            $data['quantity_kg']= 1;
            $data['price_complete_variety_taka']= 1;
            $data['price_complete_other_taka']= 1;
            $data['price_dc_expense_taka']= 1;
            $data['price_total_taka']= 1;
            $data['price_per_kg']= 1;
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
            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),array('id value','name text','date_start','date_end'),array());
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['principals']=Query_helper::get_info($this->config->item('table_login_basic_setup_principal'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));

            $data['title']="Purchase Report (LC)";
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
            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <='.$fiscal_year_id),$reports['fiscal_year_number']+1,0,array('id DESC'));

            $method='list_variety';
            $data['title']="Purchase Report (LC)";
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
            $ajax['status']=true;
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

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $fiscal_year_id=$this->input->post('fiscal_year_id');
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
        //$this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $varieties = $this->db->get()->result_array();

        /*get pack size*/
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),'*',array());
        $pack_sizes=array();
        foreach($results as $result)
        {
            $pack_sizes[$result['id']]=$result;
        }
        /*get fiscal year*/
        $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <='.$fiscal_year_id),$fiscal_year_number+1,0,array('id DESC'));
        /*get lc details info*/
        $purchase=$this->get_lc_details($fiscal_years);

        $type_total=$this->initialize_row($fiscal_years,array('crop_name'=>'','crop_type_name'=>'','variety_name'=>'Total Type','pack_size'=>''));
        $crop_total=$this->initialize_row($fiscal_years,array('crop_name'=>'','crop_type_name'=>'Total Crop','variety_name'=>'','pack_size'=>''));
        $grand_total=$this->initialize_row($fiscal_years,array('crop_name'=>'Grand Total','crop_type_name'=>'','variety_name'=>'','pack_size'=>''));

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        foreach($varieties as $variety)
        {
            if(isset($purchase[$variety['variety_id']]))
            {
                foreach($purchase[$variety['variety_id']] as $pack_size_id=>$yearly_sale)
                {
                    $pack_size=isset($pack_sizes[$pack_size_id])?$pack_sizes[$pack_size_id]['name']:'Bulk';
                    $row=$this->initialize_row($fiscal_years,array('crop_name'=>$variety['crop_name'],'crop_type_name'=>$variety['crop_type_name'],'variety_name'=>$variety['variety_name'],'pack_size'=>$pack_size));

                    if(!$first_row)
                    {
                        if($prev_crop_name!=$variety['crop_name'])
                        {
                            $items[]=$type_total;
                            $items[]=$crop_total;
                            $type_total=$this->reset_row($type_total);
                            $crop_total=$this->reset_row($crop_total);

                            $prev_crop_name=$variety['crop_name'];
                            $prev_type_name=$variety['crop_type_name'];
                        }
                        elseif($prev_type_name!=$variety['crop_type_name'])
                        {
                            $items[]=$type_total;
                            $type_total=$this->reset_row($type_total);
                            $row['crop_name']='';
                            $prev_type_name=$variety['crop_type_name'];
                        }
                        else
                        {
                            $row['crop_name']='';
                            $row['crop_type_name']='';
                        }
                    }
                    else
                    {
                        $prev_crop_name=$variety['crop_name'];
                        $prev_type_name=$variety['crop_type_name'];
                        $first_row=false;
                    }
                    foreach($yearly_sale as $fy_id=>$details_sale)
                    {
                        if($details_sale['pack_size_id']==0)
                        {
                            $row['quantity_'.$fy_id.'_pkt']='';
                            $row['quantity_'.$fy_id.'_kg']=$details_sale['quantity_pkt'];
                        }
                        else
                        {
                            $row['quantity_'.$fy_id.'_pkt']=$details_sale['quantity_pkt'];
                            $row['quantity_'.$fy_id.'_kg']=$details_sale['quantity_kg'];
                        }
                        /*$row['quantity_'.$fy_id.'_kg']=$details_sale['quantity_kg'];
                        $row['quantity_'.$fy_id.'_pkt']=$details_sale['quantity_pkt'];*/
                        $row['price_complete_variety_'.$fy_id.'_taka']=$details_sale['price_complete_variety_taka'];
                        $row['price_complete_other_'.$fy_id.'_taka']=$details_sale['price_complete_other_taka'];
                        $row['price_dc_expense_'.$fy_id.'_taka']=$details_sale['price_dc_expense_taka'];
                        $row['price_total_'.$fy_id.'_taka']=$details_sale['price_total_taka'];
                        $row['price_per_'.$fy_id.'_kg']=($details_sale['price_total_taka']/$row['quantity_'.$fy_id.'_kg']);
                    }
                    foreach($row as $key=>$r)
                    {
                        if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
                        {
                            $type_total[$key]+=$row[$key];
                            $crop_total[$key]+=$row[$key];
                            $grand_total[$key]+=$row[$key];
                        }
                    }
                    $items[]=$row;
                }
            }
        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }

    private function get_lc_details($fiscal_years)
    {
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $principal_id=$this->input->post('principal_id');
        $month=$this->input->post('month');

        $fy_ids[0]=0;
        foreach($fiscal_years as $fy)
        {
            $fy_ids[$fy['id']]=$fy['id'];
        }

        $this->db->from($this->config->item('table_sms_lc_open').' lco');
        $this->db->select('lco.id');

        $this->db->join($this->config->item('table_sms_lc_details').' lcd','lcd.lc_id = lco.id','INNER');
        $this->db->select('lcd.variety_id,lcd.pack_size_id');
        $this->db->select('SUM(lcd.quantity_receive) quantity_pkt');
        $this->db->select('SUM((pack.name*lcd.quantity_receive)/1000) quantity_kg');
        $this->db->select('SUM(lcd.price_complete_variety_taka) price_complete_variety_taka');
        $this->db->select('SUM(lcd.price_complete_other_taka) price_complete_other_taka');
        $this->db->select('SUM(lcd.price_dc_expense_taka) price_dc_expense_taka');
        $this->db->select('SUM(lcd.price_total_taka) price_total_taka');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
        $this->db->select('v.id variety_id, v.name variety_name');

        if($principal_id>0)
        {
            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$principal_id.' AND vp.revision = 1','INNER');
            $this->db->select('vp.name_import variety_name_import');
        }

        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
        $this->db->select('pack.name pack_size');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','LEFT');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','LEFT');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.date_start <= lco.date_opening AND fy.date_end>lco.date_opening','INNER');
        $this->db->select('fy.id fiscal_id, fy.name fiscal_year');

        $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
        $this->db->select('principal.name principal_name');

        $this->db->where('lco.status_open',$this->config->item('system_status_complete'));
        $this->db->where('lcd.quantity_open >0');
        $this->db->where_in('fy.id',$fy_ids);
        if($month>0)
        {
            $this->db->where('lco.month_id',$month);
        }
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
            $this->db->where('lcd.pack_size_id',$pack_size_id);
        }

        $this->db->order_by('lcd.id ASC');
        $this->db->group_by('fy.id,lcd.variety_id,lcd.pack_size_id');

        $results=$this->db->get()->result_array();
        //echo $this->db->last_query();
        $purchase=array();
        foreach($results as $result)
        {
            $purchase[$result['variety_id']][$result['pack_size_id']][$result['fiscal_id']]=$result;
        }
        return $purchase;
    }
    private function initialize_row($fiscal_years,$row)
    {
        foreach($fiscal_years as $fy)
        {
            $row['quantity_'.$fy['id'].'_pkt']=0;
            $row['quantity_'.$fy['id'].'_kg']=0;
            $row['price_complete_variety_'.$fy['id'].'_taka']=0;
            $row['price_complete_other_'.$fy['id'].'_taka']=0;
            $row['price_dc_expense_'.$fy['id'].'_taka']=0;
            $row['price_total_'.$fy['id'].'_taka']=0;
            $row['price_per_'.$fy['id'].'_kg']=0;
        }
        return $row;
    }
    private function reset_row($row)
    {
        foreach($row  as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
            {
                $row[$key]=0;
            }
        }
        return $row;
    }



}
