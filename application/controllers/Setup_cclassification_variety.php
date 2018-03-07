<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_cclassification_variety extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_cclassification_variety');
        $this->controller_url='setup_cclassification_variety';
    }

    public function index($action="list",$id=0,$id1=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="change_principals")
        {
            $this->system_change_principals($id);
        }
        elseif($action=="pricing")
        {
            $this->system_pricing($id);
        }
        elseif($action=="packing_setup")
        {
            $this->system_packing_setup($id);
        }
        elseif($action=='get_pricing_items')
        {
            $this->system_get_pricing_items();
        }
        elseif($action=='get_pack_items')
        {
            $this->system_get_pack_items();
        }
        elseif($action=='get_variety_discount_items')
        {
            $this->system_get_variety_discount_items();
        }
        elseif($action=='assign_price')
        {
            $this->system_assign_price($id);
        }
        elseif($action=='add_pack_item')
        {
            $this->system_add_pack_item($id);
        }
        elseif($action=='edit_price')
        {
            $this->system_edit_price($id,$id1);
        }
        elseif($action=='edit_pack_item')
        {
            $this->system_edit_pack_item($id,$id1);
        }
        elseif($action=='edit_price_kg')
        {
            $this->system_edit_price_kg($id);
        }
        elseif($action=='variety_discount_edit')
        {
            $this->system_variety_discount_edit($id,$id1);
        }
        elseif($action=='variety_discount_list')
        {
            $this->system_variety_discount_list($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="save_principals")
        {
            $this->system_save_principals();
        }
        elseif($action=="save_pack_size_price")
        {
            $this->system_save_pack_size_price();
        }
        elseif($action=="save_pack_item")
        {
            $this->system_save_pack_item();
        }
        elseif($action=="save_price_kg")
        {
            $this->system_save_price_kg();
        }
        elseif($action=="save_variety_discount")
        {
            $this->system_save_variety_discount();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Varieties";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id,v.name,v.status,v.ordering,v.whose,v.stock_id,v.ordering order');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->select('type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('crop.name crop_name');
        $this->db->join($this->config->item('table_login_basic_setup_competitor').' competitor','competitor.id = v.competitor_id','LEFT');
        $this->db->select('competitor.name competitor_name');
        $this->db->where('v.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {

            $data['title']="Create New Variety";
            $data["item"] = Array(
                'id' => 0,
                'crop_id'=>0,
                'crop_type_id'=>0,
                'whose'=>'ARM',
                'competitor_id'=>'',
                'stock_id'=>'',
                'hybrid'=>'',
                'name' => '',
                'description' => '',
                'date_release' => System_helper::display_date(time()),
                'trial_completed' => '',
                'remarks' => '',
                'ordering' => 999,
                'status' => $this->config->item('system_status_active')
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=array();
            $data['competitors']=Query_helper::get_info($this->config->item('table_login_basic_setup_competitor'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['hybrids']=Query_helper::get_info($this->config->item('table_login_setup_classification_hybrid'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
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
    private function system_edit($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }

            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->select('v.*');
            $this->db->select('type.crop_id crop_id');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->where('v.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists.',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
                die();
            }
            $data['item']['date_release']=System_helper::display_date($data['item']['date_release']);

            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['item']['crop_id']));
            $data['competitors']=Query_helper::get_info($this->config->item('table_login_basic_setup_competitor'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['hybrids']=Query_helper::get_info($this->config->item('table_login_setup_classification_hybrid'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="Edit Variety (".$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }

            $this->db->select('v.*');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->select('comp.name comp_name');
            $this->db->select('h.name hybrid_name');
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_login_basic_setup_competitor').' comp','comp.id = v.competitor_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_classification_hybrid').' h','h.id = v.hybrid','LEFT');
            $this->db->where('v.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('View Non Exists.',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
                die();
            }

            $this->db->select('p.name,vp.name_import');
            $this->db->from($this->config->item('table_login_setup_classification_variety_principals').' vp');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' p','p.id=vp.principal_id');
            $this->db->where('vp.variety_id',$item_id);
            $this->db->where('vp.revision',1);
            $data['principals']=$this->db->get()->result_array();

            $data['title']="Details of (".$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_change_principals($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }

            $data['item']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),'id,name',array('id ='.$item_id),1);
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists (Variety Change Principal).',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
                die();
            }
            $data['principals']=Query_helper::get_info($this->config->item('table_login_basic_setup_principal'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $results=Query_helper::get_info($this->config->item('table_login_setup_classification_variety_principals'),'*',array('variety_id ='.$item_id,'revision =1'));
            $data['assigned_principals']=array();
            foreach($results as $result)
            {
                $data['assigned_principals'][$result['principal_id']]=$result;
            }

            $data['title']="Edit Principals of Variety (".$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/change_principals",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/change_principals/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_pricing($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->select('v.*');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('v.id',$item_id);
            $data['item']=$this->db->get()->row_array();

            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists (Variety Pricing).',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }

            $data['title']="Pack Size Price List of Variety (".$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/pricing",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/pricing/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_packing_setup($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->select('v.*');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('v.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists (Variety Packing Setup).',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }

            $data['title']="Pack Item List of Variety (".$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/pack_item_list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/packing_setup/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_pricing_items()
    {
        $id=$this->input->post('id');

        $this->db->select('ps.name,ps.id');
        $this->db->select('price.price,price.price_net');
        $this->db->from($this->config->item('table_login_setup_classification_variety_price').' price');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' ps','ps.id=price.pack_size_id','INNER');
        $this->db->where('price.variety_id',$id);
        $this->db->order_by('price.pack_size_id','ASC');
        $results=$this->db->get()->result_array();
        $this->json_return($results);
    }
    private function system_get_pack_items()
    {
        $id=$this->input->post('id');

        $this->db->select('ps.name,ps.id');
        $this->db->from($this->config->item('table_login_setup_classification_variety_raw_config').' p_item');
        $this->db->select('p_item.masterfoil master_foil,p_item.foil,p_item.sticker');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' ps','ps.id=p_item.pack_size_id','INNER');
        $this->db->where('p_item.variety_id',$id);
        $this->db->where('p_item.revision',1);
        $this->db->order_by('p_item.pack_size_id','ASC');
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['name']=$result['name'];
            $item['master_foil']=number_format($result['master_foil'],3,'.','');
            $item['foil']=number_format($result['foil'],3,'.','');
            $item['sticker']=$result['sticker'];
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_get_variety_discount_items()
    {
        //$id=$this->input->post('id');
        $items=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id','name'),array('status ="'.$this->config->item('system_status_active').'"'));
        $this->json_return($items);
    }
    private function system_assign_price($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->select('v.*');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('v.id',$item_id);
            $data['info']=$this->db->get()->row_array();
            if(!$data['info'])
            {
                System_helper::invalid_try('Edit Non Exists (Variety Assign Pricing).',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }

            $this->db->select('pack.id value,pack.name text');
            $this->db->from($this->config->item('table_login_setup_classification_pack_size').' pack');
            $this->db->join($this->config->item('table_login_setup_classification_variety_price').' price','price.pack_size_id=pack.id AND price.variety_id='.$item_id,'LEFT');
            $this->db->where('price.id IS NULL',null,false);
            $data['pack_sizes']=$this->db->get()->result_array();

            $data['item']=array(
                'id'=>'',
                'pack_size_id'=>'',
                'price'=>'',
                'price_net'=>''
            );

            $data['title']="Assign Price to Pack Size of Variety (".$data['info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_price",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_price/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_add_pack_item($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->select('v.*');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('v.id',$item_id);
            $data['info']=$this->db->get()->row_array();
            if(!$data['info'])
            {
                System_helper::invalid_try('Edit Non Exists (Variety Pack Item).',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }

            $this->db->select('ps.id value,ps.name text');
            $this->db->from($this->config->item('table_login_setup_classification_pack_size').' ps');
            $this->db->join($this->config->item('table_login_setup_classification_variety_raw_config').' p_item','p_item.pack_size_id=ps.id AND p_item.variety_id='.$item_id.' AND p_item.revision=1','LEFT');
            $this->db->where('p_item.id IS NULL',null,false);
            $data['pack_sizes']=$this->db->get()->result_array();

            $data['item']=array(
                'id'=>'',
                'pack_size_id'=>'',
                'masterfoil'=>'',
                'foil'=>'',
                'sticker'=>''
            );

            $data['title']="Assign Packing Item to Pack Size of Variety (".$data['info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_pack_item",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_pack_item/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit_price($variety_id,$id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $pack_size_id=$id;
            }
            else
            {
                $pack_size_id=$this->input->post('id');
            }

            $this->db->select('v.*');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('v.id',$variety_id);
            $data['info']=$this->db->get()->row_array();
            if(!$data['info'])
            {
                System_helper::invalid_try('Edit Non Exists (Variety Pricing).',$variety_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }

            $this->db->select('pack.name');
            $this->db->select('price.*');
            $this->db->from($this->config->item('table_login_setup_classification_pack_size').' pack');
            $this->db->join($this->config->item('table_login_setup_classification_variety_price').' price','price.pack_size_id=pack.id','INNER');
            $this->db->where('price.variety_id',$variety_id);
            $this->db->where('price.pack_size_id',$pack_size_id);
            //$this->db->where('price.revision',1);
            $data['item']=$this->db->get()->row_array();
            if(!$data['info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Pack Size.';
                $this->json_return($ajax);
            }

            $data['title']='Change Price to Pack Size ('.$data['item']['name'].') of Variety ('.$data['info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_price",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_price/'.$variety_id.'/'.$pack_size_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit_pack_item($variety_id,$id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $pack_size_id=$id;
            }
            else
            {
                $pack_size_id=$this->input->post('id');
            }

            $this->db->select('v.*');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('v.id',$variety_id);
            $data['info']=$this->db->get()->row_array();
            if(!$data['info'])
            {
                System_helper::invalid_try('Edit Non Exists (Variety Pack Item).',$variety_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }

            $this->db->select('ps.name');
            $this->db->select('p_item.*');
            $this->db->from($this->config->item('table_login_setup_classification_pack_size').' ps');
            $this->db->join($this->config->item('table_login_setup_classification_variety_raw_config').' p_item','p_item.pack_size_id=ps.id','INNER');
            $this->db->where('p_item.variety_id',$variety_id);
            $this->db->where('p_item.pack_size_id',$pack_size_id);
            $this->db->where('p_item.revision',1);
            $data['item']=$this->db->get()->row_array();
            if(!$data['info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Pack Size.';
                $this->json_return($ajax);
            }

            //$data['title']='Change Pack Item Quantity to Pack Size ('.$data['item']['name'].') of Variety ('.$data['info']['name'].')';
            $data['title']='Edit Variety: '.$data['info']['name'].', Pack size: '.$data['item']['name'].' (gm) ';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_pack_item",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_pack_item/'.$variety_id.'/'.$pack_size_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit_price_kg($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->select('v.*');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('v.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists (Variety Price in Kg).',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Variety.';
                $this->json_return($ajax);
            }

            $data['title']='Edit Price in KG of Variety ('.$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_price_kg",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_price_kg/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_variety_discount_list($id)
    {
        if(isset($this->permissions['action7']) && ($this->permissions['action7']==1))
        {
            if($id>0)
            {
                $data['variety_id']=$id;
            }
            else
            {
                $data['variety_id']=$this->input->post('id');
            }

            $variety_name=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('name'),array('id='.$data['variety_id']),1);
            if(!$variety_name)
            {
                System_helper::invalid_try('List Variety Non Exists',$data['variety_id']);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $data['title']="Variety Discount (".$variety_name['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/variety_discount_list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/variety_discount_list/'.$data['variety_id']);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_variety_discount_edit($variety_id,$id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $pack_size_id=$id;
            }
            else
            {
                $pack_size_id=$this->input->post('id');
            }
            $data["item"] = Array
            (
                'pack_size_id' =>$pack_size_id,
                'variety_id' =>$variety_id
            );

            $data['variety_name']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('name'),array('id='.$variety_id),1);
            $data['pack_size_name']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('name'),array('id='.$pack_size_id),1);
            if(!$data['variety_name'])
            {
                System_helper::invalid_try('Edit Variety Non Exists',$variety_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$data['pack_size_name'])
            {
                System_helper::invalid_try('Edit Pack Size Non Exists',$pack_size_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $data['outlets']=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('type =1'));
            $data['title']="Variety Pack Discount";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/variety_discount_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/variety_discount_edit/'.$variety_id.'/'.$pack_size_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data=$this->input->post('item');
            if($data['whose']!='Competitor')
            {
                $data['competitor_id']='';
            }
            $data['date_release']=System_helper::get_time($data['date_release']);
            if($data['date_release']===0)
            {
                unset($data['date_release']);
            }

            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;
                Query_helper::update($this->config->item('table_login_setup_classification_varieties'),$data,array("id = ".$id));
            }
            else
            {
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                Query_helper::add($this->config->item('table_login_setup_classification_varieties'),$data);
            }

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function system_save_principals()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']='Wrong input. You use illegal way.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $revision_history_data=array();
        $revision_history_data['date_updated']=$time;
        $revision_history_data['user_updated']=$user->user_id;
        Query_helper::update($this->config->item('table_login_setup_classification_variety_principals'),$revision_history_data,array('revision=1','variety_id='.$id));

        $this->db->where('variety_id',$id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_login_setup_classification_variety_principals'));

        $principal_ids=$this->input->post('principal_ids');
        $name_imports=$this->input->post('name_imports');
        if(is_array($principal_ids))
        {
            foreach($principal_ids as $principal_id)
            {
                $data=array();
                $data['variety_id']=$id;
                $data['principal_id']=$principal_id;
                if(isset($name_imports[$principal_id]))
                {
                    $data['name_import']=$name_imports[$principal_id];
                }
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $data['revision'] = 1;
                Query_helper::add($this->config->item('table_login_setup_classification_variety_principals'),$data);
            }
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add();
            }
            else
            {
                $this->system_list();
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function system_save_pack_size_price()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation_pack_size_price())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $item=$this->input->post('item');

            $this->db->trans_start();  //DB Transaction Handle START
            $history_data=array();
            if($id>0)
            {
                $data=array();
                $data['price']=$item['price'];
                $data['price_net']=$item['price_net'];
                $data['user_updated']=$user->user_id;
                $data['date_updated']=$time;
                $this->db->set('revision_count', 'revision_count+1', FALSE);
                Query_helper::update($this->config->item('table_login_setup_classification_variety_price'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$id));

                $data=array();
                $data['pack_size_id']=$id;
                $data['user_updated']=$user->user_id;
                $data['date_updated']=$time;
                $this->db->set('revision', 'revision+1', FALSE);
                Query_helper::update($this->config->item('table_login_setup_classification_variety_price_history'),$data,array('variety_id='.$item['variety_id'],'pack_size_id='.$id));

                $history_data['pack_size_id']=$id;
            }
            else
            {
                $result=Query_helper::get_info($this->config->item('table_login_setup_classification_variety_price'),'*',array('variety_id='.$item['variety_id'],'pack_size_id='.$item['pack_size_id']),1);
                if($result)
                {
                    $ajax['status']=false;
                    $ajax['system_message']="Before this time someone set price to this Variety's Pack Size.";
                    $this->json_return($ajax);
                }

                $data=array();
                $data['variety_id']=$item['variety_id'];
                $data['pack_size_id']=$item['pack_size_id'];
                $data['price']=$item['price'];
                $data['price_net']=$item['price_net'];
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                $data['revision_count']=1;
                Query_helper::add($this->config->item('table_login_setup_classification_variety_price'),$data,false);

                $history_data['pack_size_id']=$item['pack_size_id'];
            }

            $history_data['variety_id']=$item['variety_id'];
            $history_data['price']=$item['price'];
            $history_data['price_net']=$item['price_net'];
            $history_data['user_created']=$user->user_id;
            $history_data['date_created']=$time;
            $history_data['revision']=1;
            Query_helper::add($this->config->item('table_login_setup_classification_variety_price_history'),$history_data,false);

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_assign_price($history_data['variety_id']);
                }
                else
                {
                    $this->system_pricing($history_data['variety_id']);
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function system_save_pack_item()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation_pack_item())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data=$this->input->post('item');

            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['pack_size_id']=$id;

                $this->db->where('variety_id',$data['variety_id']);
                $this->db->where('pack_size_id',$id);
                $this->db->where('revision',1);
                $this->db->set('user_updated',$user->user_id);
                $this->db->set('date_updated',$time);
                $this->db->update($this->config->item('table_login_setup_classification_variety_raw_config'));

                $this->db->where('variety_id',$data['variety_id']);
                $this->db->where('pack_size_id',$id);
                $this->db->set('revision','revision+1',FALSE);
                $this->db->update($this->config->item('table_login_setup_classification_variety_raw_config'));
            }
            else
            {
                $result=Query_helper::get_info($this->config->item('table_login_setup_classification_variety_raw_config'),'*',array('variety_id='.$data['variety_id'],'pack_size_id='.$data['pack_size_id'],'revision=1'),1);
                if($result)
                {
                    $ajax['status']=false;
                    $ajax['system_message']="Before this time someone set price to this Variety's Pack Size.";
                    $this->json_return($ajax);
                }
            }
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['revision']=1;
            Query_helper::add($this->config->item('table_login_setup_classification_variety_raw_config'),$data,false);

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add_pack_item($data['variety_id']);
                }
                else
                {
                    $this->system_packing_setup($data['variety_id']);
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function system_save_price_kg()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!$this->check_validation_price_kg())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data=$this->input->post('item');

            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $this->db->where('id',$id);
                $this->db->set('price_kg',$data['price_kg']);
                $this->db->set('revision_price_kg','revision_price_kg+1',FALSE);
                $this->db->set('user_updated',$user->user_id);
                $this->db->set('date_updated',$time);

                $this->db->update($this->config->item('table_login_setup_classification_varieties'));
            }

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_pricing($id);
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function system_save_variety_discount()
    {
        $item=$this->input->post('item');
        $items=$this->input->post('items');
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_variety_outlet_discount'),array('*'),array('outlet_id ='.$item['outlet_id'],'variety_id ='.$item['variety_id'],'pack_size_id ='.$item['pack_size_id']));

        if(!($item['outlet_id']>=0))
        {
            $ajax['status']=false;
            $ajax['system_message']='The Outlet field is required';
            $this->json_return($ajax);
        }
        if($item['outlet_id']!=0)
        {
            $valid_outlet=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('customer_id ='.$item['outlet_id'],'type =1'),1);
            if(!$valid_outlet)
            {
                System_helper::invalid_try('Save Outlet Non Exists',$item['outlet_id']);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
        }
        $old_items=array();
        foreach($results as $result)
        {
            $old_items[$result['farmer_type_id']]=$result;
        }

        $id=count($old_items);
        $user = User_helper::get_user();
        $time=time();

        /*--Start-- Permission Checking */
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        /*--End-- Permission Checking */

        //checking incomplete entry
        foreach($items as $outlet_info)
        {
            foreach($outlet_info as $discount_info)
            {
                if($discount_info['discount_percentage']>0 || $discount_info['expire_day']>0)
                {
                    if(!($discount_info['discount_percentage']>0) || !($discount_info['expire_day']>0))
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='Unfinished Discount Entry.';
                        $this->json_return($ajax);
                    }
                }
            }
        }

        if(!$this->check_validation_variety_pack_discount())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $this->db->trans_start();  //DB Transaction Handle START
            foreach($items as $outlet_id=>$outlet_info)
            {
                foreach($outlet_info as $farmer_type_id=>$discount_info)
                {
                    if(isset($old_items[$farmer_type_id]))
                    {
                        $data=array();
                        $data['outlet_id']=$outlet_id;
                        $data['variety_id']=$item['variety_id'];
                        $data['pack_size_id']=$item['pack_size_id'];
                        $data['farmer_type_id']=$farmer_type_id;
                        $data['discount_percentage']=$discount_info['discount_percentage'];
                        $data['expire_day']=$discount_info['expire_day'];
                        if($discount_info['expire_day']==$old_items[$farmer_type_id]['expire_day'])
                        {
                            $data['expire_time']=$old_items[$farmer_type_id]['expire_time'];
                        }
                        else
                        {
                            $data['expire_time']=$time+$discount_info['expire_day']*3600*24;
                        }
                        $data['user_updated']=$user->user_id;
                        $data['date_updated']=$time;
                        $this->db->set('revision_count', 'revision_count+1', FALSE);
                        Query_helper::update($this->config->item('table_login_setup_classification_variety_outlet_discount'),$data,array('outlet_id='.$data['outlet_id'],'variety_id='.$data['variety_id'],'pack_size_id='.$data['pack_size_id'],'farmer_type_id='.$data['farmer_type_id']));


                        $data_history=array();
                        $data_history['user_updated']=$user->user_id;
                        $data_history['date_updated']=$time;


                        $this->db->set('revision', 'revision+1', FALSE);
                        Query_helper::update($this->config->item('table_login_setup_classification_variety_outlet_discount_histories'),$data_history,array('outlet_id='.$data['outlet_id'],'variety_id='.$data['variety_id'],'pack_size_id='.$data['pack_size_id'],'farmer_type_id='.$data['farmer_type_id']));

                        unset($data['revision_count']);
                        $data['revision']=1;
                        Query_helper::add($this->config->item('table_login_setup_classification_variety_outlet_discount_histories'),$data,false);
                    }
                    else
                    {
                        $data=array();
                        $data['outlet_id']=$outlet_id;
                        $data['variety_id']=$item['variety_id'];
                        $data['pack_size_id']=$item['pack_size_id'];
                        $data['farmer_type_id']=$farmer_type_id;
                        $data['discount_percentage']=$discount_info['discount_percentage'];
                        $data['expire_day']=$discount_info['expire_day'];
                        $data['expire_time']=($time+($data['expire_day']*3600*24));
                        $data['revision_count']=1;
                        $data['user_created']=$user->user_id;
                        $data['date_created']=$time;
                        Query_helper::add($this->config->item('table_login_setup_classification_variety_outlet_discount'),$data,false);
                        unset($data['revision_count']);
                        $data['revision']=1;
                        Query_helper::add($this->config->item('table_login_setup_classification_variety_outlet_discount_histories'),$data,false);
                    }
                }

            }

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_variety_discount_edit($item['variety_id'],$item['pack_size_id']);
                }
                else
                {
                    $this->system_variety_discount_edit($item['variety_id'],$item['pack_size_id']);
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    public function get_variety_discount_farmer_type()
    {
        $time=time();
        $data['outlet_id'] = $this->input->post('outlet_id');
        $variety_id = $this->input->post('variety_id');
        $pack_size_id = $this->input->post('pack_size_id');

        if($data['outlet_id']>0)
        {
            $valid_outlet=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('customer_id ='.$data['outlet_id'],'type =1'),1);
            if(!$valid_outlet)
            {
                System_helper::invalid_try('Save Outlet Non Exists',$data['outlet_id']);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
        }

        $this->db->select('farmer_type.*');
        $this->db->from($this->config->item('table_pos_setup_farmer_type').' farmer_type');
        $this->db->where('farmer_type.status',$this->config->item('system_status_active'));
        $results_farmer_type=$this->db->get()->result_array();

        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_variety_outlet_discount'),array('*'),array('outlet_id ='.$data['outlet_id'],'variety_id ='.$variety_id,'pack_size_id ='.$pack_size_id));
        $discount_info=array();
        foreach($results as $result)
        {
            $discount_info[$result['farmer_type_id']]=$result;
        }
        $items=array();
        foreach($results_farmer_type as $f_type)
        {
            if(isset($discount_info[$f_type['id']]))
            {
                $item['id']=$discount_info[$f_type['id']]['farmer_type_id'];
                $item['name']=$f_type['name'];
                $item['discount_percentage']=$discount_info[$f_type['id']]['discount_percentage'];

                if($discount_info[$f_type['id']]['expire_time']<$time)
                {
                    $item['expire_day']=0;
                }
                else
                {
                    $item['expire_day']=ceil(($discount_info[$f_type['id']]['expire_time']-$time)/86400);
                }
            }
            else
            {
                $item['id']=$f_type['id'];
                $item['name']=$f_type['name'];
                $item['discount_percentage']='';
                $item['expire_day']='';
            }
            $items[]=$item;
        }
        $data['items']=$items;
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>'#add_edit_variety_pack_discount_id',"html"=>$this->load->view($this->controller_url."/variety_discount_farmer_type",$data,true));
        $this->json_return($ajax);
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('item[crop_type_id]',$this->lang->line('LABEL_CROP_TYPE'),'required');
        $item=$this->input->post('item');

        if($item['whose']=='Competitor')
        {
            $this->form_validation->set_rules('item[competitor_id]',$this->lang->line('LABEL_COMPETITOR_NAME'),'required');
        }
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_pack_size_price()
    {
        $this->load->library('form_validation');
        $id=$this->input->post('id');
        if($id==0)
        {
            $this->form_validation->set_rules('item[pack_size_id]',$this->lang->line('LABEL_PACK_NAME'),'required');
        }
        $this->form_validation->set_rules('item[price]',$this->lang->line('LABEL_PRICE_TRADE'),'required');
        $this->form_validation->set_rules('item[price_net]',$this->lang->line('LABEL_PRICE_NET'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_price_kg()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[price_kg]',$this->lang->line('LABEL_PRICE_KG'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_pack_item()
    {
        $this->load->library('form_validation');
        $id = $this->input->post("id");
        if(!$id>0)
        {
            $this->form_validation->set_rules('item[pack_size_id]',$this->lang->line('LABEL_PACK_NAME'),'required');
        }
        $this->form_validation->set_rules('item[masterfoil]',$this->lang->line('LABEL_MASTERFOIL'),'required');
        $this->form_validation->set_rules('item[foil]',$this->lang->line('LABEL_FOIL'),'required');
        $this->form_validation->set_rules('item[sticker]',$this->lang->line('LABEL_STICKER'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $item = $this->input->post("item");

        if($item['masterfoil']>0 && ($item['foil']>0 || $item['sticker']>0))
        {
            $this->message='Invalid input. you can not use ( common foil or sticker ).';
            return false;
        }
        else if(!($item['masterfoil']>0) && !($item['foil']>0 && $item['sticker']>0))
        {
            $this->message='Invalid input. Common foil or sticker is empty.';
            return false;
        }
        else
        {
            return true;
        }
    }
    private function check_validation_variety_pack_discount()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[outlet_id]','Showroom','required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['preference_method_name']='list';
            $data['title']="Set Preference";
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
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
        $data['id']= 1;
        $data['name']= 1;
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['whose']= 1;
        $data['competitor_name']= 1;
        $data['stock_id']= 1;
        $data['order']= 1;
        $data['status']= 1;
        if($result)
        {
            if($result['preferences']!=null)
            {
                $preferences=json_decode($result['preferences'],true);
                foreach($data as $key=>$value)
                {
                    if(isset($preferences[$key]))
                    {
                        $data[$key]=$value;
                    }
                    else
                    {
                        $data[$key]=0;
                    }
                }
            }
        }
        return $data;
    }
}
