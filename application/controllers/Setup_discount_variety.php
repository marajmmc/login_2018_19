<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_discount_variety extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class());
        $this->controller_url = strtolower(get_class());
        // Extra Language
        $this->lang->language['LABEL_NUM_OUTLETS_DISCOUNTED'] = 'Discounted Outlets';
        $this->lang->language['LABEL_ALL_OUTLETS'] = 'All Outlets';
        $this->lang->language['LABEL_PACK_SIZE_ID'] = 'PS Id';
        $this->lang->language['LABEL_VARIETY_ID'] = 'Variety Id';
    }

    public function index($action = "list", $id = 0, $id1 = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "edit")
        {
            $this->system_edit($id, $id1);
        }
        elseif ($action == "save")
        {
            $this->system_save();
        }
        elseif ($action == "save_delete")
        {
            $this->system_save_delete($id, $id1);
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }

    private function get_preference_headers($method)
    {
        $data = array();
        $data['variety_id'] = 1;
        $data['pack_size_id'] = 1;
        $data['variety_name'] = 1;
        $data['pack_size'] = 1;
        $data['num_outlets_discounted'] = 1;
        $data['crop_name'] = 1;
        $data['crop_type_name'] = 1;
        $data['status'] = 1;
        return $data;
    }

    private function system_set_preference($method)
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference');
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
        $user = User_helper::get_user();
        $method = 'list';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Setup Variety Discount List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
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
        $discount_result = array();
        $results = Query_helper::get_info($this->config->item('table_login_setup_discount_variety'), array('id, pack_size_id, variety_id, discount'), array('revision=1'));
        if ($results)
        {
            foreach ($results as $result)
            {
                $discount_result[$result['variety_id']][$result['pack_size_id']] = sizeof(json_decode($result['discount'], TRUE));
            }
        }
        $this->db->from($this->config->item('table_login_setup_classification_variety_price').' vp');
        $this->db->select('vp.variety_id,vp.pack_size_id');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=vp.variety_id','INNER');
        $this->db->select('v.name variety_name,v.status');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
        $this->db->select('crop.name crop_name,crop.id crop_id');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=vp.pack_size_id','INNER');
        $this->db->select('pack.name pack_size');
        $this->db->where('v.whose', 'ARM');
        $this->db->order_by('crop.ordering ASC');
        $this->db->order_by('crop.id ASC');
        $this->db->order_by('crop_type.ordering ASC');
        $this->db->order_by('crop_type.id ASC');
        $this->db->order_by('v.ordering ASC');
        $this->db->order_by('v.id ASC');
        $items = $this->db->get()->result_array();
        if ($items)
        {
            foreach ($items as &$item)
            {
                $item['num_outlets_discounted'] = (isset($discount_result[$item['variety_id']][$item['pack_size_id']])) ? $discount_result[$item['variety_id']][$item['pack_size_id']] : '';
            }
        }
        $this->json_return($items);
    }

    private function system_edit($v_id, $pack_id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
        {
            if ($v_id > 0)
            {
                $variety_id = $v_id;
            }
            else
            {
                $variety_id = $this->input->post('variety_id');
            }

            if ($pack_id > 0)
            {
                $pack_size_id = $pack_id;
            }
            else
            {
                $pack_size_id = $this->input->post('pack_size_id');
            }



            $data = array();
            $this->db->from($this->config->item('table_login_setup_classification_variety_price') . ' vp');
            $this->db->select('vp.variety_id,vp.pack_size_id');
            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' v', 'v.id = vp.variety_id', 'INNER');
            $this->db->select('v.name variety_name, v.status');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size') . ' pack_size', ' pack_size.id = vp.pack_size_id', 'INNER');
            $this->db->select('pack_size.name pack_size_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
            $this->db->select('type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
            $this->db->select('crop.name crop_name');
            $this->db->where('v.whose', 'ARM');
            $this->db->where('vp.pack_size_id', $pack_size_id);
            $this->db->where('vp.variety_id', $variety_id);
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $pack_size_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $results = Query_helper::get_info($this->config->item('table_login_csetup_cus_info'), array('customer_id, name'), array('revision=1', 'type=' . $this->config->item('system_customer_type_outlet_id')), 0, 0, array('name ASC'));
            if (!$results)
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'No Outlet Found.';
                $this->json_return($ajax);
            }

            $data['items'][0] = array(
                'outlet_id' => 0,
                'name' => $this->lang->line('LABEL_ALL_OUTLETS'),
                'discount_percentage' => ''
            );
            foreach ($results as $result)
            {
                $data['items'][$result['customer_id']] = array(
                    'outlet_id' => $result['customer_id'],
                    'name' => $result['name'],
                    'discount_percentage' => ''
                );
            }

            $discount_result = Query_helper::get_info($this->config->item('table_login_setup_discount_variety'), array('id, discount, remarks'), array('revision=1', 'pack_size_id =' . $pack_size_id, 'variety_id =' . $variety_id), 1);
            if (!$discount_result)
            {
                $data['item']['id'] = 0;
                $data['item']['discount'] = array();
                $data['item']['remarks'] = '';
            }
            else
            {
                $data['item'] = array_merge($data['item'], $discount_result);
                $data['item']['discount'] = json_decode($data['item']['discount'], TRUE);
                foreach ($data['item']['discount'] as $outlet_id => $discount)
                {
                    $data['items'][$outlet_id]['discount_percentage'] = $discount;
                }
            }

            $data['title'] = 'Edit Outlet Discount :: ' . $data['item']['variety_name'] . ' (ID: ' . $data['item']['variety_id'] . ')';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' .$variety_id. '/' .$pack_size_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $item = $this->input->post('item');
        $items = $this->input->post('items');
        $user = User_helper::get_user();
        $time = time();
        // permission checking
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_login_setup_classification_variety_price') . ' vp');
        $this->db->select('*');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size') . ' pack_size', ' pack_size.id = vp.pack_size_id', 'INNER');
        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' v', 'v.id = vp.variety_id', 'INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' type', 'type.id = v.crop_type_id', 'INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = type.crop_id', 'INNER');
        $this->db->where('v.whose', 'ARM');
        $this->db->where('pack_size.id', $item['pack_size_id']);
        $this->db->where('vp.variety_id', $item['variety_id']);
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item['pack_size_id'], 'Pack Size Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }


        if ($items)
        {
            foreach ($items as $outlet_id => $discount)
            {
                if((trim($discount)!='')&& ($discount>=0))
                {
                    $item['discount'][$outlet_id] = $discount;
                }
            }

            if (isset($item['discount']) && sizeof($item['discount']) > 0)
            {
                $item['discount'] = json_encode($item['discount']);
            }
        }

        $discount_result = Query_helper::get_info($this->config->item('table_login_setup_discount_variety'), array('*'), array('revision=1', 'pack_size_id =' . $item['pack_size_id'], 'variety_id =' . $item['variety_id']), 1);
        $this->db->trans_start(); //DB Transaction Handle START
        if ($discount_result) // Revision Update
        {
            // Update Date_updated & User_updated
            $update_item = array();
            $update_item['date_updated'] = $time;
            $update_item['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_login_setup_discount_variety'), $update_item, array('revision=1', 'pack_size_id =' . $item['pack_size_id'], 'variety_id =' . $item['variety_id']));

            // Update Revision
            $this->db->set('revision', 'revision+1', FALSE);
            Query_helper::update($this->config->item('table_login_setup_discount_variety'), array(), array('pack_size_id =' . $item['pack_size_id'], 'variety_id =' . $item['variety_id']));
        }

        $item['revision'] = 1;
        $item['date_created'] = $time;
        $item['user_created'] = $user->user_id;
        Query_helper::add($this->config->item('table_login_setup_discount_variety'), $item);

        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function system_save_delete($v_id, $pack_id)
    {
        if ($v_id > 0)
        {
            $variety_id = $v_id;
        }
        else
        {
            $variety_id = $this->input->post('variety_id');
        }

        if ($pack_id > 0)
        {
            $pack_size_id = $pack_id;
        }
        else
        {
            $pack_size_id = $this->input->post('pack_size_id');
        }


        $user = User_helper::get_user();
        $time = time();
        if (!(isset($this->permissions['action3']) && ($this->permissions['action3'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $result = Query_helper::get_info($this->config->item('table_login_setup_discount_variety'), array('*'), array('revision=1', 'pack_size_id =' . $pack_size_id, 'variety_id =' . $variety_id), 1);
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $variety_id, 'Delete Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        // Update Date_updated & User_updated
        $update_item = array();
        $update_item['date_updated'] = $time;
        $update_item['user_updated'] = $user->user_id;
        Query_helper::update($this->config->item('table_login_setup_discount_variety'), $update_item, array('revision=1', 'pack_size_id =' . $pack_size_id, 'variety_id =' . $variety_id));

        // Update Revision
        $this->db->set('revision', 'revision+1', FALSE);
        Query_helper::update($this->config->item('table_login_setup_discount_variety'), array(), array('pack_size_id =' . $pack_size_id, 'variety_id =' . $variety_id));
        $this->db->trans_complete(); //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message = $this->lang->line("MSG_DELETED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_DELETED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $items = $this->input->post('items');
        if ($items)
        {
            $discount_found = FALSE;
            foreach ($items as $discount)
            {
                if ($discount > 0)
                {
                    $discount_found = TRUE;
                }
                if ($discount > 100)
                {
                    $this->message = 'Discount cannot be Greater than 100.';
                    return false;
                }
            }
            if (!$discount_found)
            {
                $this->message = 'Atleast 1 discount has to be saved.';
                return false;
            }
        }
        else
        {
            $this->message = 'No outlet Found.';
            return false;
        }
        return true;
    }
}
