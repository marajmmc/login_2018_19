<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$user=User_helper::get_user();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list_offer_adjust/'.$farmer_info['id'])
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_delete');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <input type="hidden" id="system_user_token" name="system_user_token" value="<?php echo time().'_'.$user->id; ?>" />
    <div class="row widget">
        <div class="row">
            <div class="col-xs-6">
                <button type="button" class="btn btn-success btn-md" style="font-weight: bold;color: #000000;background-color: lightyellow"><?php echo $CI->lang->line('LABEL_OFFER_OFFERED'); ?>: <?php echo System_helper::get_string_amount($offer_info['offer_offered']);?></button>
                <button type="button" class="btn btn-success btn-md" style="font-weight: bold;color: #000000;background-color: lightyellow"><?php echo $CI->lang->line('LABEL_OFFER_GIVEN'); ?>: <?php echo System_helper::get_string_amount($offer_info['offer_given']);?></button>
                <button type="button" class="btn btn-success btn-md" style="font-weight: bold;color: #000000;background-color: lightyellow"><?php echo $CI->lang->line('LABEL_OFFER_ADJUSTED'); ?>: <?php echo System_helper::get_string_amount($offer_info['offer_adjusted']);?></button>
                <button type="button" class="btn btn-success btn-md" style="font-weight: bold;color: #000000;background-color: lightyellow"><?php echo $CI->lang->line('LABEL_OFFER_BALANCE'); ?>: <?php echo System_helper::get_string_amount($offer_info['offer_balance']);?></button>
            </div>
        </div>
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo 'Delete '.$CI->lang->line('LABEL_AMOUNT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::get_string_amount($item['amount']);?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS_DELETE');?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_delete]" id="remarks_delete" class="form-control" ></textarea>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>

