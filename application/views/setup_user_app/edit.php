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
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/edit')
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#accordion_ems" href="#">+ EMS <small>(Number of Task: <?php echo sizeof($task_ems);?>)</small></a></label>
                        </h4>
                    </div>
                    <div id="accordion_ems" class="panel-collapse collapse in">
                        <table class="table table-bordered table-responsive">
                            <tbody>
                            <?php
                            if(!(sizeof($task_ems)>0))
                            {
                                ?>
                                <tr>
                                    <th colspan="2" class="bg-danger text-center">No Task Assign</th>
                                </tr>
                            <?php
                            }
                            else
                            {
                                foreach($task_ems as $task)
                                {
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="ems[<?php echo $task['id']?>]" value="<?php echo $task['id']?>" <?php if (in_array($task['id'], $notify_task_ems_ids)){echo "checked='true'";}?> id="ems_task_<?php echo $task['id']?>" style="cursor: pointer"/>
                                        </td>
                                        <th><label for="ems_task_<?php echo $task['id']?>" style="cursor: pointer"><?php echo $task['name']?></label></th>
                                    </tr>
                                <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="col-xs-12 col-sm-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#accordion_sms" href="#">+ SMS <small>(Number of Task: <?php echo sizeof($task_sms);?>)</small></a></label>
                        </h4>
                    </div>
                    <div id="accordion_sms" class="panel-collapse collapse in">
                        <table class="table table-bordered table-responsive">
                            <tbody>
                            <?php
                            if(!(sizeof($task_sms)>0))
                            {
                                ?>
                                <tr>
                                    <th colspan="2" class="bg-danger text-center">No Task Assign</th>
                                </tr>
                            <?php
                            }
                            else
                            {
                                foreach($task_sms as $task)
                                {
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="sms[<?php echo $task['id']?>]" value="<?php echo $task['id']?>" <?php if(in_array($task['id'], $notify_task_sms_ids)){echo "checked='true'";}?> id="sms_task_<?php echo $task['id']?>" style="cursor: pointer"/>
                                        </td>
                                        <th><label for="sms_task_<?php echo $task['id']?>" style="cursor: pointer"><?php echo $task['name']?></label></th>
                                    </tr>
                                <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#accordion_bms" href="#">+ BMS <small>(Number of Task: <?php echo sizeof($task_bms);?>)</small></a></label>
                        </h4>
                    </div>
                    <div id="accordion_bms" class="panel-collapse collapse in">
                        <table class="table table-bordered table-responsive">
                            <tbody>
                            <?php
                            if(!(sizeof($task_bms)>0))
                            {
                                ?>
                                <tr>
                                    <th colspan="2" class="bg-danger text-center">No Task Assign</th>
                                </tr>
                            <?php
                            }
                            else
                            {
                                foreach($task_bms as $task)
                                {
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="bms[<?php echo $task['id']?>]" value="<?php echo $task['id']?>" <?php if(in_array($task['id'], $notify_task_bms_ids)){echo "checked='true'";}?> id="bms_task_<?php echo $task['id']?>" style="cursor: pointer"/>
                                        </td>
                                        <th><label for="bms_task_<?php echo $task['id']?>" style="cursor: pointer"><?php echo $task['name']?></label></th>
                                    </tr>
                                <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
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
