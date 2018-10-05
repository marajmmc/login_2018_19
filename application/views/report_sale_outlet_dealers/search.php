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
                <div class="col-xs-6">
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label>
                        </div>
                        <div class="col-xs-6">
                            <select id="fiscal_year_id" name="report[fiscal_year_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($fiscal_years as $year)
                                {?>
                                    <option value="<?php echo $year['value']?>"><?php echo $year['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_START');?></label>
                        </div>
                        <div class="col-xs-6">
                            <input type="text" id="date_start" name="report[date_start]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
                        </div>

                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_END');?></label>
                        </div>
                        <div class="col-xs-6">
                            <input type="text" id="date_end" name="report[date_end]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
                        </div>

                    </div>
                    <div id="container_product">
                        <div style="" class="row show-grid" id="crop_id_container">
                            <div class="col-xs-6">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                            </div>
                            <div class="col-xs-6">
                                <select id="crop_id" name="report[crop_id]" class="form-control">
                                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                </select>
                            </div>
                        </div>
                        <div style="display: none;" class="row show-grid" id="crop_type_id_container">
                            <div class="col-xs-6">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
                            </div>
                            <div class="col-xs-6">
                                <select id="crop_type_id" name="report[crop_type_id]" class="form-control">
                                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                </select>
                            </div>
                        </div>
                        <div style="display: none;" class="row show-grid" id="variety_id_container">
                            <div class="col-xs-6">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
                            </div>
                            <div class="col-xs-6">
                                <select id="variety_id" name="report[variety_id]" class="form-control">
                                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                </select>
                            </div>
                        </div>
                        <div class="row show-grid" id="pack_size_id_container">
                            <div class="col-xs-6">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?></label>
                            </div>
                            <div class="col-xs-6">
                                <select id="pack_size_id" name="report[pack_size_id]" class="form-control">
                                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                    <?php
                                    foreach($pack_sizes as $pack_size)
                                    {?>
                                        <option value="<?php echo $pack_size['value']?>"><?php echo $pack_size['text'];?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <!-- Location Section-->

                    <div style="<?php if(!(sizeof($outlets)>0)){echo 'display:none';} ?>" class="row show-grid" id="outlet_id_container">
                        <div class="col-xs-6">
                            <select id="outlet_id" name="report[outlet_id]" class="form-control">
                                <?php
                                foreach($outlets as $outlet)
                                {?>
                                    <option value="<?php echo $outlet['value']?>"><?php echo $outlet['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label class="control-label"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?><span style="color:#FF0000">*</span></label>
                        </div>
                    </div>
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
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+0"});
        $("#crop_id").html(get_dropdown_with_select(system_crops));
        $(document).on("change","#crop_id",function()
        {
            $('#system_report_container').html('');
            $('#crop_type_id').val("");
            $('#variety_id').val("");

            var crop_id=$('#crop_id').val();
            $('#crop_type_id_container').hide();
            $('#variety_id_container').hide();
            if(crop_id>0)
            {
                $('#crop_type_id_container').show();
                if(system_types[crop_id]!==undefined)
                {
                    $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
                }
            }
        });
        $(document).on("change","#crop_type_id",function()
        {
            $('#system_report_container').html('');
            $('#variety_id').val("");
            var crop_type_id=$('#crop_type_id').val();
            $('#variety_id_container').hide();
            if(crop_type_id>0)
            {
                $('#variety_id_container').show();
                if(system_varieties[crop_type_id]!==undefined)
                {
                    $('#variety_id').html(get_dropdown_with_select(system_varieties[crop_type_id]));
                }
            }
        });
        $(document).on('change','#outlet_id',function()
        {
            $("#system_report_container").html('');

        });
        $(document).off("change", "#fiscal_year_id");
        $(document).on("change","#fiscal_year_id",function()
        {

            var fiscal_year_ranges=$('#fiscal_year_id').val();
            if(fiscal_year_ranges!='')
            {
                var dates = fiscal_year_ranges.split("/");
                $("#date_start").val(dates[0]);
                $("#date_end").val(dates[1]);

            }
        });
    });
</script>
