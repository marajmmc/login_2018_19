<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_discount_customer extends Root_Controller
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
        // Extra Language
        $this->lang->language['LABEL_ALL_OUTLETS'] = 'All Outlets';
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "edit")
        {
            $this->system_edit($id);
        }
        elseif ($action == "save")
        {
            $this->system_save();
        }
        elseif ($action == "save_delete")
        {
            $this->system_save_delete($id);
        }
        else
        {
            $this->system_list();
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data = array();
            $results = Query_helper::get_info($this->config->item('table_login_csetup_cus_info'), array('customer_id, name'), array('revision=1', 'type=' . $this->config->item('system_customer_type_outlet_id')), 0, 0, array('name ASC'));
            if (!$results)
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'No Outlet Found.';
                $this->json_return($ajax);
            }
            // Manually pushing 1st data for 'All Outlets'
            $data['outlets'][0] = array(
                'customer_id' => 0,
                'name' => $this->lang->line('LABEL_ALL_OUTLETS'),
                'discounts' => array(),
                'number_of_discounts' => 0
            );
            foreach ($results as &$result)
            {
                $result['discounts'] = array();
                $result['number_of_discounts'] = 0;
                $data['outlets'][$result['customer_id']] = $result;
            }

            $results = Query_helper::get_info($this->config->item('table_login_setup_discount_customer'), array('*'), array('revision=1'), 0, 0, array('outlet_id ASC'));
            foreach ($results as $result)
            {
                $outlet_discount = json_decode($result['discount'], TRUE);
                $data['outlets'][$result['outlet_id']]['discounts'] = $outlet_discount;
                $data['outlets'][$result['outlet_id']]['number_of_discounts'] = sizeof($outlet_discount);
            }

            $data['title'] = "Setup Outlet Discount List";
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

    private function system_edit($item_id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
        {
            $data = array();
            if ($item_id == 0)
            {
                $data['item']['name'] = $this->lang->line('LABEL_ALL_OUTLETS');
                $data['item']['customer_code'] = '';
                $data['item']['phone'] = '';
                $data['item']['address'] = '';
            }
            elseif ($item_id > 0)
            {
                $this->db->from($this->config->item('table_login_csetup_cus_info'));
                $this->db->select('name, customer_code, phone, address');
                $this->db->where('customer_id', $item_id);
                $this->db->where('revision', 1);
                $this->db->where('type', $this->config->item('system_customer_type_outlet_id'));
                $data['item'] = $this->db->get()->row_array();
            }

            if (!isset($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_login_setup_discount_customer'));
            $this->db->select('id, outlet_id, discount, remarks');
            $this->db->where('outlet_id', $item_id);
            $this->db->where('revision', 1);
            $result = $this->db->get()->row_array();
            if ($result)
            {
                $result['discount'] = json_decode($result['discount'], TRUE);
                $data['item'] = array_merge($data['item'], $result);
            }
            else
            {
                $data['item']['id'] = 0;
                $data['item']['outlet_id'] = $item_id;
                $data['item']['discount'] = array();
                $data['item']['remarks'] = '';
            }

            $data['title'] = "Edit Outlet Discount :: " . $data['item']['name'];
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $item_id);
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
        $user = User_helper::get_user();
        $time = time();
        // permission checking
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $result = array();
        if ($item['outlet_id'] == 0)
        {
            $result['name'] = $this->lang->line('LABEL_ALL_OUTLETS');
        }
        elseif ($item['outlet_id'] > 0)
        {
            $this->db->from($this->config->item('table_login_csetup_cus_info'));
            $this->db->select('*');
            $this->db->where('customer_id', $item['outlet_id']);
            $this->db->where('revision', 1);
            $this->db->where('type', $this->config->item('system_customer_type_outlet_id'));
            $result = $this->db->get()->row_array();
        }

        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item['outlet_id'], 'Update Not Exists');
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
        // Sorting Discounts using Amount in Ascending order.
        $discount_items = array_column($item['discount'], 'discount_percentage', 'amount');
        ksort($discount_items);
        $item['discount'] = json_encode($discount_items);

        $discount_result = Query_helper::get_info($this->config->item('table_login_setup_discount_customer'), array('*'), array('revision=1', 'outlet_id =' . $item['outlet_id']), 1);
        $this->db->trans_start(); //DB Transaction Handle START
        if ($discount_result) // EDIT
        {
            // Update Date_updated & User_updated
            $update_item = array();
            $update_item['date_updated'] = $time;
            $update_item['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_login_setup_discount_customer'), $update_item, array('revision=1', 'outlet_id=' . $item['outlet_id']),false);

            // Update Revision
            $this->db->set('revision', 'revision+1', FALSE);
            Query_helper::update($this->config->item('table_login_setup_discount_customer'), array(), array('outlet_id=' . $item['outlet_id']),false);
        }
        $item['revision'] = 1;
        $item['date_created'] = $time;
        $item['user_created'] = $user->user_id;
        Query_helper::add($this->config->item('table_login_setup_discount_customer'), $item,false);
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

    private function system_save_delete($outlet_id)
    {
        $user = User_helper::get_user();
        $time = time();
        if (!(isset($this->permissions['action3']) && ($this->permissions['action3'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_login_setup_discount_customer') . ' discount_customer');
        $this->db->select('*');
        $this->db->where('discount_customer.outlet_id', strval($outlet_id));
        $this->db->where('discount_customer.revision', 1);
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $outlet_id, 'Delete Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        // Update Date_updated & User_updated
        $update_item = array();
        $update_item['date_updated'] = $time;
        $update_item['user_updated'] = $user->user_id;
        Query_helper::update($this->config->item('table_login_setup_discount_customer'), $update_item, array('revision=1', 'outlet_id=' . $outlet_id));

        // Update Revision
        $this->db->set('revision', 'revision+1', FALSE);
        Query_helper::update($this->config->item('table_login_setup_discount_customer'), array(), array('outlet_id=' . $outlet_id));
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
        $item = $this->input->post('item');
        if (isset($item['discount']) && (sizeof($item['discount']) > 0))
        {
            foreach ($item['discount'] as $discount)
            {
                if (!($discount['amount'] > 0) || !($discount['discount_percentage'] >= 0))
                {
                    $this->message = 'Unfinished Discount in entry.';
                    return false;
                }
                if ($discount['discount_percentage'] > 100)
                {
                    $this->message = 'Discount cannot be Greater than 100.';
                    return false;
                }
            }
        }
        else
        {
            $this->message = 'Atleast 1 discount has to be saved.';
            return false;
        }
        return true;
    }
}
