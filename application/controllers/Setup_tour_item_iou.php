<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_tour_item_iou extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class());
        $this->controller_url = strtolower(get_class());
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list")
        {
            $this->system_list($id);
        }
        elseif ($action == 'get_items')
        {
            $this->system_get_items();
        }
        elseif ($action == "add")
        {
            $this->system_add();
        }
        elseif ($action == "edit")
        {
            $this->system_edit($id);
        }
        elseif ($action == "save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list($id);
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "IOU Items List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array('id' => '#system_content', 'html' => $this->load->view($this->controller_url . '/list', $data, true));
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
        $items = Query_helper::get_info($this->config->item('table_login_setup_tour_item_iou'), array('id', 'name', 'status', 'ordering'), array('status !="' . $this->config->item('system_status_delete') . '"'), 0, 0, array('ordering ASC'));
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data['title'] = "Create New IOU Item";
            $data["iou_item"] = Array(
                'id' => 0,
                'name' => '',
                'ordering' => 99,
                'status' => $this->config->item('system_status_active')
            );
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/add");

            $ajax['status'] = true;
            $ajax['system_content'][] = array('id' => '#system_content', 'html' => $this->load->view($this->controller_url . '/add_edit', $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
        {
            if (($this->input->post('id')))
            {
                $type_id = $this->input->post('id');
            }
            else
            {
                $type_id = $id;
            }
            $data['iou_item'] = Query_helper::get_info($this->config->item('table_login_setup_tour_item_iou'), '*', array('id =' . $type_id), 1);
            $data['title'] = 'Edit IOU Item :: ' . $data['iou_item']['name'];
            $ajax['status'] = true;
            $ajax['system_content'][] = array('id' => '#system_content', 'html' => $this->load->view($this->controller_url . '/add_edit', $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $type_id);
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
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if ($id > 0)
        {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }
        else
        {
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        else
        {
            $time = time();
            $data = $this->input->post('iou_item');
            $this->db->trans_start(); //DB Transaction Handle START
            if ($id > 0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;
                Query_helper::update($this->config->item('table_login_setup_tour_item_iou'), $data, array("id = " . $id));
            }
            else
            {
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                Query_helper::add($this->config->item('table_login_setup_tour_item_iou'), $data);
            }
            $this->db->trans_complete(); //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new = $this->input->post('system_save_new_status');
                $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
                if ($save_and_new == 1)
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
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }

    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('iou_item[name]', $this->lang->line('LABEL_NAME'), 'required');

        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
}
