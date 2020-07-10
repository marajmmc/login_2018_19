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
                            <label class="control-label pull-right">Report Type<span style="color:#FF0000">*</span></label>
                        </div>
                        <div class="col-xs-6">
                            <select id="report_name" name="report[report_name]" class="form-control">
                                <option value="offer_current_dealers_balance">Dealers Current Offer Balance</option>
                                <option value="offer_summary_dealers">Dealers offer summary</option>
                                <option value="offer_history_dealer">Dealer Offer History</option>
                            </select>
                        </div>
                    </div>
                    <div class="row show-grid" style="display: none;" id="fiscal_year_container">
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
                    <div class="row show-grid" style="display: none;" id="date_start_container">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_START');?></label>
                        </div>
                        <div class="col-xs-6">
                            <input type="text" id="date_start" name="report[date_start]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
                        </div>

                    </div>
                    <div class="row show-grid" style="display: none;" id="date_end_container">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_END');?></label>
                        </div>
                        <div class="col-xs-6">
                            <input type="text" id="date_end" name="report[date_end]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
                        </div>

                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="row show-grid">
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
                            <label class="control-label"><?php echo $CI->lang->line('LABEL_OUTLET');?><span style="color:#FF0000">*</span></label>
                        </div>
                    </div>
                    <div class="row show-grid" id="container_farmer_type">
                        <div class="col-xs-6">
                            <select name="report[farmer_type_id]" id="farmer_type_id" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                <?php
                                foreach($farmer_types as $row)
                                {?>
                                    <option value="<?php echo $row['value']?>"><?php echo $row['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label class="control-label">Dealer Type</label>
                        </div>
                    </div>
                    <div style="display: none;" class="row show-grid" id="farmer_id_container">
                        <div class="col-xs-6">
                            <select id="farmer_id" name="report[farmer_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label class="control-label">Dealer</label>
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
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:c+2"});
        $(document).off("change", "#outlet_id");
        $(document).on('change','#outlet_id',function()
        {
            $("#system_report_container").html('');
            $("#farmer_type_id").val("");
            $("#farmer_id").val("");
            $('#farmer_id_container').hide();

        });
        $(document).off("change", "#farmer_type_id");
        $(document).on("change","#farmer_type_id",function()
        {
            $("#farmer_id").val("");
            var farmer_type_id=$('#farmer_type_id').val();
            var outlet_id=$('#outlet_id').val();
            if((outlet_id>0)&&farmer_type_id>0)
            {
                $('#farmer_id_container').show();
                $.ajax({
                    url:'<?php echo site_url($CI->controller_url.'/get_dropdown_dealers_by_outlet_farmer_type_id') ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{outlet_id:outlet_id,farmer_type_id:farmer_type_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });

            }
            else
            {
                $('#farmer_id_container').hide();

            }
        });
        $(document).off("change", "#farmer_id");

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
        $(document).off("change", "#report_name");
        $(document).on("change","#report_name",function()
        {
            $("#system_report_container").html("");
            var report_name=$('#report_name').val();
            if((report_name=='offer_current_dealers_balance'))
            {
                $('#date_start_container').hide();
                $('#date_end_container').hide();
                $('#fiscal_year_container').hide();
            }
            else
            {
                $('#date_start_container').show();
                $('#date_end_container').show();
                $('#fiscal_year_container').show();
            }
        });
    });
</script>
