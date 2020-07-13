<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Offer_adjust extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $user_outlets;
    public $user_outlet_ids;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());

        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->user_outlet_ids=array();
        $this->user_outlets=User_helper::get_assigned_outlets($this->locations);

        if(sizeof($this->user_outlets)>0)
        {
            foreach($this->user_outlets as $row)
            {
                $this->user_outlet_ids[]=$row['outlet_id'];
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']='No Outlet Found.<br>'.$this->lang->line('MSG_CONTACT_ADMIN');
            $this->json_return($ajax);
        }

        $this->load->helper('offer');
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_OFFER_OFFERED']='Offer Offered';
        $this->lang->language['LABEL_OFFER_GIVEN']='Offer Given';
        $this->lang->language['LABEL_OFFER_ADJUSTED']='Offer Adjusted';
        $this->lang->language['LABEL_OFFER_BALANCE']='Offer Remains';
        $this->lang->language['LABEL_DELETE']='Delete';
        $this->lang->language['LABEL_REMARKS_DELETE']='Delete Reason';
        $this->lang->language['LABEL_DATE_ADJUST']='Adjust Date';
        $this->lang->language['LABEL_AMOUNT']='Amount';
    }
    public function index($action="list",$id=0,$id1=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="get_items")
        {
            $this->system_get_items($id);
        }
        elseif($action=="list_offer_adjust")
        {
            $this->system_list_offer_adjust($id);
        }
        elseif($action=="get_items_offer_adjust")
        {
            $this->system_get_items_offer_adjust($id);
        }
        elseif($action=="add")
        {
            $this->system_add($id);
        }
        elseif($action=="edit")
        {
            $this->system_edit($id,$id1);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif ($action == "delete")
        {
            $this->system_delete($id,$id1);
        }
        elseif ($action == "save_delete")
        {
            $this->system_save_delete();
        }
        elseif($action=="details")
        {
            $this->system_details($id,$id1);
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif($action=="set_preference_list_offer_adjust")
        {
            $this->system_set_preference('list_offer_adjust');
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list($id);
        }
    }
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='list')
        {
            $data['id']= 1;
            $data['barcode']= 1;
            $data['name']= 1;
            $data['mobile_no']= 1;
            $data['outlet_name']= 1;
            $data['offer_offered']= 1;
            $data['offer_given']= 1;
            $data['offer_adjusted']= 1;
            $data['offer_balance']= 1;
        }
        else if($method=='list_offer_adjust')
        {
            $data['id']= 1;
            $data['date_adjust']= 1;
            $data['amount']= 1;
            $data['remarks']= 1;
        }
        else
        {

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
    private function system_list($id)
    {
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $farmer_type_id=$id;
            }
            else
            {
                $farmer_type_id=$this->input->post('farmer_type_id');
            }
            $data['farmer_types']=Query_helper::get_info($this->config->item('table_pos_setup_farmer_type'),array('id value,name text'),array('status ="'.$this->config->item('system_status_active').'"','id >1','allow_offer ="'.$this->config->item('system_status_yes').'"'),0,0,array('ordering ASC'));
            if(sizeof($data['farmer_types'])==0)
            {
                $ajax['status']=false;
                $ajax['system_message']="No dealer is allowed for offer.<br>".$this->lang->line('MSG_CONTACT_ADMIN');
                $this->json_return($ajax);
            }
            if(!($farmer_type_id>1))
            {
                $farmer_type_id=$data['farmer_types'][0]['value'];
            }
            $data['farmer_type_id']=$farmer_type_id;
            $result=Query_helper::get_info($this->config->item('table_pos_setup_farmer_type'),'*',array('status ="'.$this->config->item('system_status_active').'"','id ='.$farmer_type_id,'allow_offer ="'.$this->config->item('system_status_yes').'"'),1);
            if(!$result)
            {
                $ajax['status']=false;
                $ajax['system_message']="Invalid access";
                $this->json_return($ajax);
            }
            $data['system_preference_items']= System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list/'.$farmer_type_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items($farmer_type_id)
    {
        $user=User_helper::get_user();



        //dealers
        $this->db->from($this->config->item('table_pos_setup_farmer_farmer') . ' farmer');
        $this->db->select('farmer.*');
        $this->db->join($this->config->item('table_pos_setup_farmer_outlet') . ' farmer_outlet', 'farmer_outlet.farmer_id = farmer.id AND farmer_outlet.revision =1', 'INNER');
        $this->db->select('farmer_outlet.outlet_id outlet_id');
        $this->db->where('farmer.farmer_type_id', $farmer_type_id);
        $this->db->where_in('farmer_outlet.outlet_id', $this->user_outlet_ids);
        $this->db->order_by('farmer_outlet.outlet_id');
        $this->db->order_by('farmer.id DESC');
        $farmers = $this->db->get()->result_array();
        $farmer_ids[0]=0;
        foreach($farmers as $farmer)
        {
            $farmer_ids[$farmer['id']]=$farmer['id'];
        }
        $offers=Offer_helper::get_offer_stats($farmer_ids);

        $items=array();
        foreach($farmers as $item)
        {
            $item['barcode']=Barcode_helper::get_barcode_farmer($item['id']);
            $item['outlet_name']=$this->user_outlets[$item['outlet_id']]['outlet_name'];
            $item['offer_offered']=$offers[$item['id']]['offer_offered'];
            $item['offer_given']=$offers[$item['id']]['offer_given'];
            $item['offer_adjusted']=$offers[$item['id']]['offer_adjusted'];
            $item['offer_balance']=$offers[$item['id']]['offer_balance'];
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_list_offer_adjust($id)
    {
        if($id>0)
        {
            $data['item_id']=$id;
        }
        else
        {
            $data['item_id']=$this->input->post('id');
        }
        //for fixing back button of preference
        if(!($data['item_id']>0))
        {
            $this->system_list(0);
        }
        $user = User_helper::get_user();

        if((isset($this->permissions['action0']) && ($this->permissions['action0']==1)) ||(isset($this->permissions['action1']) && ($this->permissions['action1']==1)) || (isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $farmer_info=Query_helper::get_info($this->config->item('table_pos_setup_farmer_farmer'),array('*'),array('id ='.$data['item_id'],'status!="'.$this->config->item('system_status_delete').'"'),1);
            if(!$farmer_info)
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Dealer';
                $this->json_return($ajax);
            }
            $data['farmer_info']=$farmer_info;
            $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
            $this->db->select('farmer_outlet.outlet_id');
            $this->db->where('farmer_outlet.farmer_id',$data['item_id']);
            $this->db->where('farmer_outlet.revision',1);
            $this->db->where_in('farmer_outlet.outlet_id',$this->user_outlet_ids);
            $results=$this->db->get()->result_array();
            if(!$results)
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $offers=Offer_helper::get_offer_stats(array($data['item_id']));

            $data['offer_info']=$offers[$data['item_id']];


            $method='list_offer_adjust';
            $data['system_preference_items']= System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']='Offer Adjust List ::'.$farmer_info['name'].'-'.$farmer_info['mobile_no'].' ('.Barcode_helper::get_barcode_farmer($farmer_info['id']).')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_offer_adjust",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_offer_adjust/'.$data['item_id']);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_offer_adjust($item_id)
    {
        $this->db->from($this->config->item('table_login_offer_adjust_farmer').' offer_adjust');
        $this->db->select('offer_adjust.*');
        $this->db->where('offer_adjust.farmer_id',$item_id);
        $this->db->where('offer_adjust.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('offer_adjust.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_adjust']=System_helper::display_date($item['date_adjust']);
        }
        $this->json_return($items);
    }

}
