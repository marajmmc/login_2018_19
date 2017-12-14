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
                    <label><input type="checkbox" name="item[name]" <?php if($items['name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_NAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[name_short]" <?php if($items['name_short']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_SHORT_NAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[type]" <?php if($items['type']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_CUSTOMER_TYPE'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[division_name]" <?php if($items['division_name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[zone_name]" <?php if($items['zone_name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[territory_name]" <?php if($items['territory_name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[district_name]" <?php if($items['district_name']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[customer_code]" <?php if($items['customer_code']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_CUSTOMER_CODE'); ?></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[incharge]" <?php if($items['incharge']){echo 'checked';}?> value="1">Incharge</label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="item[phone]" <?php if($items['phone']){echo 'checked';}?> value="1"><?php echo $CI->lang->line('LABEL_PHONE'); ?></label>
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
