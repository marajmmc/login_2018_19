<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
    <div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
        <div class="row show-grid">
            <div class="col-xs-6">

                <div style="" class="row show-grid" id="crop_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_id" name="report[crop_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </div>
                <div style="display: none;" class="row show-grid" id="crop_type_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_type_id" name="report[crop_type_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div style="" class="row show-grid">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['division_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                            <input type="hidden" name="report[division_id]" value="<?php echo $CI->locations['division_id'];?>">
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="division_id" name="report[division_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($divisions as $division)
                                {?>
                                    <option value="<?php echo $division['value']?>"><?php echo $division['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['zone_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
                            <input type="hidden" name="report[zone_id]" value="<?php echo $CI->locations['zone_id'];?>">
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="zone_id" class="form-control" name="report[zone_id]">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($zones as $zone)
                                {?>
                                    <option value="<?php echo $zone['value']?>"><?php echo $zone['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['territory_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['territory_name'];?></label>
                            <input type="hidden" name="report[territory_id]" value="<?php echo $CI->locations['territory_id'];?>">
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="territory_id" class="form-control" name="report[territory_id]">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($territories as $territory)
                                {?>
                                    <option value="<?php echo $territory['value']?>"><?php echo $territory['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['district_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
                            <input type="hidden" name="report[district_id]" value="<?php echo $CI->locations['district_id'];?>">
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="district_id" class="form-control" name="report[district_id]">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($districts as $district)
                                {?>
                                    <option value="<?php echo $district['value']?>"><?php echo $district['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($outlets)>0)){echo 'display:none';} ?>" class="row show-grid" id="outlet_id_container">
                    <div class="col-xs-6">
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
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($upazillas)>0)){echo 'display:none';} ?>" class="row show-grid" id="upazilla_id_container">
                    <div class="col-xs-6">
                        <select id="upazilla_id" name="report[upazilla_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($upazillas as $upazilla)
                            {?>
                                <option value="<?php echo $upazilla['value']?>"><?php echo $upazilla['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>

                </div>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-6">
                &nbsp;
            </div>
            <div class="col-xs-6">
                <div class="action_button">
                    <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>

<div id="system_report_container">

</div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(document).off('change', '#year_id');
        $('#crop_id').html(get_dropdown_with_select(system_crops));
        $(document).off('change', '#crop_id');
        $(document).on('change','#crop_id',function()
        {
            $('#system_report_container').html('');
            $('#crop_type_id').val('');
            $('#crop_type_id_container').hide();
            var crop_id=$('#crop_id').val();
            if(crop_id>0)
            {
                if(system_types[crop_id]!==undefined)
                {
                    $('#crop_type_id_container').show();
                    $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
                }
            }

        });
        $(document).off('change', '#crop_type_id');
        $(document).off('change', '#division_id');
        $(document).on('change','#division_id',function()
        {
            $('#zone_id').val('');
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            var division_id=$('#division_id').val();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $('#upazilla_id_container').hide();
            if(division_id>0)
            {
                if(system_zones[division_id]!==undefined)
                {
                    $('#zone_id_container').show();
                    $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
                }
            }

        });
        $(document).off('change', '#zone_id');
        $(document).on('change','#zone_id',function()
        {
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            var zone_id=$('#zone_id').val();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $('#upazilla_id_container').hide();
            if(zone_id>0)
            {
                if(system_territories[zone_id]!==undefined)
                {
                    $('#territory_id_container').show();
                    $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).off('change', '#territory_id');
        $(document).on('change','#territory_id',function()
        {
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            $('#outlet_id_container').hide();
            $('#district_id_container').hide();
            $('#upazilla_id_container').hide();
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                if(system_districts[territory_id]!==undefined)
                {
                    $('#district_id_container').show();
                    $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
                }

            }
        });
        $(document).off('change', '#district_id');
        $(document).on('change','#district_id',function()
        {
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            var district_id=$('#district_id').val();
            $('#outlet_id_container').hide();
            $('#upazilla_id_container').hide();
            if(district_id>0)
            {
                if(system_outlets[district_id]!==undefined)
                {
                    $('#outlet_id_container').show();
                    $('#outlet_id').html(get_dropdown_with_select(system_outlets[district_id]));
                }
                $('#upazilla_id_container').show();
                $.ajax({
                    url: '<?php echo site_url("common_controller/get_dropdown_upazillas_by_districtid/");?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{district_id:district_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
        });

        $(document).off('change', '#outlet_id');
        $(document).on('change','#outlet_id',function()
        {
            $('#upazilla_id').val('');
            $('#upazilla_id_container').hide();

            var outlet_id=$('#outlet_id').val();
            if(outlet_id>0)
            {
                $('#upazilla_id_container').show();
                $.ajax({
                    url: '<?php echo site_url("common_controller/get_dropdown_upazillas_by_outlet_id/");?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{outlet_id:outlet_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
        });

    });
</script>
