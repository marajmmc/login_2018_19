<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
if(isset($CI->permissions['action4'])&&($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5'])&&($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference_stock_current_outlets')
    );
}
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {

        ?>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="crop_name" <?php if($system_preference_items['crop_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="crop_type_name" <?php if($system_preference_items['crop_type_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="variety_name" <?php if($system_preference_items['variety_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="pack_size" <?php if($system_preference_items['pack_size']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="amount_price_unit" <?php if($system_preference_items['amount_price_unit']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AMOUNT_PRICE_UNIT'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="stock_total_pkt" <?php if($system_preference_items['stock_total_pkt']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_STOCK_TOTAL_PKT'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="stock_total_kg" <?php if($system_preference_items['stock_total_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_STOCK_TOTAL_KG'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="amount_price_total" <?php if($system_preference_items['amount_price_total']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AMOUNT_TOTAL'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_pkt" value="stock_pkt" <?php if($system_preference_items['stock_pkt']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_STOCK_PKT'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_kg" value="stock_kg" <?php if($system_preference_items['stock_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_STOCK_KG'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_amount" value="amount" <?php if($system_preference_items['amount']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AMOUNT'); ?></span></label></div></div>

    <?php
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_stock_current_outlets');?>";
        $(document).off("click", ".system_jqx_column_pkt");
        $(document).on("click", ".system_jqx_column_pkt", function(event)
        {
            var jqx_grid_id='#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php
                foreach($areas as $area)
                {?>
                $(jqx_grid_id).jqxGrid('showcolumn', '<?php echo 'stock_'.$area['value'].'_pkt';?>');
                <?php
                }
                ?>

            }
            else
            {
                <?php
                foreach($areas as $area)
                {?>
                $(jqx_grid_id).jqxGrid('hidecolumn', '<?php echo 'stock_'.$area['value'].'_pkt';?>');
                <?php
                }
                ?>
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });
        $(document).off("click", ".system_jqx_column_kg");
        $(document).on("click", ".system_jqx_column_kg", function(event)
        {
            var jqx_grid_id='#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php
                foreach($areas as $area)
                {?>
                $(jqx_grid_id).jqxGrid('showcolumn', '<?php echo 'stock_'.$area['value'].'_kg';?>');
                <?php
                }
                ?>

            }
            else
            {
                <?php
                foreach($areas as $area)
                {?>
                $(jqx_grid_id).jqxGrid('hidecolumn', '<?php echo 'stock_'.$area['value'].'_kg';?>');
                <?php
                }
                ?>
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });

        $(document).off("click", ".system_jqx_column_amount");
        $(document).on("click", ".system_jqx_column_amount", function(event)
        {
            var jqx_grid_id='#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php
                foreach($areas as $area)
                {?>
                $(jqx_grid_id).jqxGrid('showcolumn', '<?php echo 'amount_'.$area['value'].'_price';?>');
                <?php
                }
                ?>

            }
            else
            {
                <?php
                foreach($areas as $area)
                {?>
                $(jqx_grid_id).jqxGrid('hidecolumn', '<?php echo 'amount_'.$area['value'].'_price';?>');
                <?php
                }
                ?>
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type_name', type: 'string' },
                { name: 'variety_name', type: 'string' },
                { name: 'pack_size', type: 'string' },
                { name: 'amount_price_unit', type: 'string' },
                    <?php
                        foreach($areas as $area)
                        {?>{ name: '<?php echo 'stock_'.$area['value'].'_pkt';?>', type: 'string' },
                        { name: '<?php echo 'stock_'.$area['value'].'_kg';?>', type: 'string' },
                        { name: '<?php echo 'amount_'.$area['value'].'_price';?>', type: 'string' },
                <?php
                    }
                ?>
                { name: 'stock_total_pkt', type: 'string' },
                { name: 'stock_total_kg', type: 'string' },
                { name: 'amount_price_total', type: 'string' }
            ],
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var header_render=function (text, align)
        {
            var words = text.split(" ");
            var label=words[0];
            var count=words[0].length;
            for (i = 1; i < words.length; i++)
            {
                if((count+words[i].length)>10)
                {
                    label=label+'</br>'+words[i];
                    count=words[i].length;
                }
                else
                {
                    label=label+' '+words[i];
                    count=count+words[i].length;
                }

            }
            return '<div style="margin: 5px;">'+label+'</div>';
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            //console.log(defaultHtml);
            if (record.variety_name=="Total Type")
            {
                if(!((column=='crop_name')||(column=='crop_type_name')))
                {
                    element.css({ 'background-color': system_report_color_type,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_type_name=="Total Crop")
            {
                if(column!='crop_name')
                {
                    element.css({ 'background-color': system_report_color_crop,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_name=="Grand Total")
            {

                element.css({ 'background-color': system_report_color_grand,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }

            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var aggregates=function (total, column, element, record)
        {
            //console.log(record);
            //console.log(record['warehouse_5_pkt']);
            if(record.crop_name=="Grand Total")
            {
                return record[element];

            }
            return total;
        };
        var aggregatesrenderer=function (aggregates)
        {
            //console.log('here');
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +aggregates['total']+'</div>';

        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                showaggregates: true,
                showstatusbar: true,
                rowsheight: 40,
                columnsheight: 80,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['variety_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['pack_size']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_PRICE_UNIT'); ?>', dataField: 'amount_price_unit',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_price_unit']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Total CS(pkt)',dataField: '<?php echo 'stock_total_pkt';?>',renderer: header_render,align:'center',cellsalign: 'right',width:'80',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_total_pkt']?0:1;?>,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Total CS(kg)', dataField: '<?php echo 'stock_total_kg';?>',renderer: header_render,align:'center',cellsalign: 'right',width:'80',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_total_kg']?0:1;?>,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Total Price', dataField: '<?php echo 'amount_price_total';?>',renderer: header_render,align:'center',cellsalign: 'right',width:'120',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_price_total']?0:1;?>,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    <?php
                        foreach($areas as $area)
                        {   ?>{ text: '<?php echo $area['text'].' CS(pkt)'; ?>',renderer: header_render,dataField: '<?php echo 'stock_'.$area['value'].'_pkt';?>',align:'center',cellsalign: 'right',width:'80',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_pkt']?0:1;?>,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                            { text: '<?php echo $area['text'].' CS(kg)'; ?>',renderer: header_render, dataField: '<?php echo 'stock_'.$area['value'].'_kg';?>',align:'center',cellsalign: 'right',width:'80',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['stock_kg']?0:1;?>,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                            { text: '<?php echo $area['text'].' Price'; ?>',renderer: header_render, dataField: '<?php echo 'amount_'.$area['value'].'_price';?>',align:'center',cellsalign: 'right',width:'120',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount']?0:1;?>,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                            <?php
                        }
                    ?>

                ]
            });
    });
</script>