<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_preference');?>" method="post">
    <input type="hidden" id="id" name="id" value="" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[id]" <?php if($items['id']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('ID'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[employee_id]" <?php if($items['employee_id']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_EMPLOYEE_ID'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[user_name]" <?php if($items['user_name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_USERNAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[name]" <?php if($items['name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_NAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[email]" <?php if($items['email']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_EMAIL'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[designation_name]" <?php if($items['designation_name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_DESIGNATION_NAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[department_name]" <?php if($items['department_name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[mobile_no]" <?php if($items['mobile_no']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_MOBILE_NO'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[blood_group]" <?php if($items['blood_group']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_BLOOD_GROUP'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[group_name]" <?php if($items['group_name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_USER_GROUP'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[ordering]" <?php if($items['ordering']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_ORDER'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[status]" <?php if($items['status']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('STATUS'); ?></label>
                </div>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>
