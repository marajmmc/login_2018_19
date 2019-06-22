<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_SAVE"),
    'id' => 'button_action_save',
    'data-form' => '#save_form'
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

$variety_status = '';
if ($item['status'] == $this->config->item('system_status_inactive'))
{
    $variety_status = ' ( <span style="color:#FF0000">' . $item['status'] . '</span> )';
}
?>

<form id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>
    <input type="hidden" id="pack_size_id" name="item[pack_size_id]" value="<?php echo $item['pack_size_id']; ?>"/>
    <input type="hidden" id="variety_id" name="item[variety_id]" value="<?php echo $item['variety_id']; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title"><?php echo $title; ?></div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['variety_name'] . $variety_status; ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE_NAME'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['pack_size_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['crop_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['crop_type_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Discount(s) <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Outlet ID</th>
                            <th>Showrooms for Discount</th>
                            <th style="text-align:center; width:25%">Discount(%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($items)
                        {
                            foreach ($items as $outlet)
                            {
                                ?>
                                <tr>
                                    <td><?php echo $outlet['outlet_id']; ?></td>
                                    <td><?php echo $outlet['name']; ?></td>
                                    <td>
                                        <input type="text" class="form-control float_type_positive price_unit_tk amount_purchase" name="items[<?php echo $outlet['outlet_id']; ?>]" value="<?php echo $outlet['discount_percentage']; ?>"/>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS') ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <textarea id="remarks" name="item[remarks]" class="form-control" rows="4"><?php echo $item['remarks']; ?></textarea>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events();
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
    });
</script>
