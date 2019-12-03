<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="outlet_id" name="report[outlet_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($outlets as $outlet)
                    {?>
                        <option value="<?php echo $outlet['value']?>"><?php echo $outlet['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid" id="date_start_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_START');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" id="date_start" name="report[date_start]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
            </div>

        </div>
        <div class="row show-grid" id="date_end_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_END');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" id="date_end" name="report[date_end]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
            </div>

        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                </div>

            </div>
            <div class="col-xs-4">

            </div>
        </div>
    </form>
</div>
<div class="clearfix"></div>


<div id="system_report_container">

</div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_off_events();
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:c+2"});
    });
</script>
