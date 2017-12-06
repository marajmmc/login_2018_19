<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
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
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="old_password" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OLD_PASSWORD');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input id="old_password" type="password" name="old_password"  class="form-control" value="" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="new_password" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PASSWORD');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input id="new_password" type="password" name="new_password"  class="form-control" value="" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="re_password" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_RE_PASSWORD');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input id="re_password" type="password" name="re_password"  class="form-control" value="" >
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
