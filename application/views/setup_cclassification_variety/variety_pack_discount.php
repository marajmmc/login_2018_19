<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/pricing/'.$item['variety_id'])
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
    'id'=>'button_action_save_new',
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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_variety_pack_discount');?>" method="post">
    <input type="hidden" id="variety_id" name="item[variety_id]" value="<?php echo $item['variety_id']; ?>" />
    <input type="hidden" id="pack_size_id" name="item[pack_size_id]" value="<?php echo $item['pack_size_id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="" class="row show-grid" id="showroom_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right">Showroom<span style="color:#FF0000">*</span></label>
            </div>

            <div class="col-sm-4 col-xs-8">
                <select id="outlet_id" name="item[outlet_id]" class="form-control">
                    <option value="-1"><?php echo $this->lang->line('SELECT');?></option>
                    <option value="0">All Showroom</option>
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

        <div style="display: none" class="row show-grid" id="variety_pack_discount_container">
            <div id="add_edit_variety_pack_discount_id" class="col-sm-10 col-sm-offset-1">

            </div>
        </div>




    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).off('change','#outlet_id');
        $(document).on("change","#outlet_id",function()
        {
            $("#add_edit_variety_pack_discount_id").val('');
            $('#variety_pack_discount_container').hide();

            var outlet_id=$('#outlet_id').val();
            var variety_id='<?php echo $item['variety_id'];?>';
            var pack_size_id='<?php echo $item['pack_size_id'];?>';
            if(outlet_id>=0)
            {
                $('#variety_pack_discount_container').show();
                $.ajax({
                    url:"<?php echo site_url($CI->controller_url.'/get_farmer_type/');?>",
                    type: 'POST',
                    datatype: "JSON",
                    data:{outlet_id:outlet_id,variety_id:variety_id,pack_size_id:pack_size_id},
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
