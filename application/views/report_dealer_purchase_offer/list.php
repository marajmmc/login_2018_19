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
        'href'=>site_url($CI->controller_url.'/index/set_preference')
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
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="offer_name" <?php if($system_preference_items['offer_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_OFFER_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="quantity_minimum_kg" <?php if($system_preference_items['quantity_minimum_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_QUANTITY_MINIMUM_KG'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="amount_minimum" <?php if($system_preference_items['amount_minimum']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AMOUNT_MINIMUM'); ?></span></label></div></div>


        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="quantity_total_kg" <?php if($system_preference_items['quantity_total_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_KG'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="amount_total" <?php if($system_preference_items['amount_total']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AMOUNT_TOTAL'); ?></span></label></div></div>

        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_kg" value="quantity_kg" <?php if($system_preference_items['quantity_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_QUANTITY_KG'); ?></span></label></div></div>
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        $(document).off("click", ".system_jqx_column_kg");
        $(document).on("click", ".system_jqx_column_kg", function(event)
        {
            var jqx_grid_id='#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php
                foreach($dealers as $dealer)
                {?>
                $(jqx_grid_id).jqxGrid('showcolumn', '<?php echo 'quantity_'.$dealer['farmer_id'].'_kg';?>');
                <?php
                }
                ?>

            }
            else
            {
                <?php
                foreach($dealers as $dealer)
                {?>
                $(jqx_grid_id).jqxGrid('hidecolumn', '<?php echo 'quantity_'.$dealer['farmer_id'].'_kg';?>');
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
                foreach($dealers as $dealer)
                {?>
                $(jqx_grid_id).jqxGrid('showcolumn', '<?php echo 'amount_'.$dealer['farmer_id'];?>');
                <?php
                }
                ?>

            }
            else
            {
                <?php
                foreach($dealers as $dealer)
                {?>
                $(jqx_grid_id).jqxGrid('hidecolumn', '<?php echo 'amount_'.$dealer['farmer_id'];?>');
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
                { name: 'offer_name', type: 'string' },
                { name: 'quantity_minimum_kg', type: 'number' },
                { name: 'amount_minimum', type: 'number' },
                    <?php
                        foreach($dealers as $dealer)
                        {?>
                        { name: '<?php echo 'quantity_'.$dealer['farmer_id'].'_kg';?>', type: 'number' },
                        { name: '<?php echo 'amount_'.$dealer['farmer_id'];?>', type: 'number' },
                <?php
                    }
                ?>

                { name: 'quantity_total_kg', type: 'string' },
                { name: 'amount_total', type: 'string' }
            ],
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);


            if(column.substr(-3)=='pkt')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_quantity(value));
                }
            }
            else if(column.substr(-2)=='kg')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_kg(value));
                }
            }
            else if(column.substr(0,6)=='amount')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_amount(value));
                }
            }

            return element[0].outerHTML;



        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var aggregatesrenderer_kg=function (aggregates)
        {
            var text='';
            if(!((aggregates['sum']=='0.000')||(aggregates['sum']=='')))
            {
                text=get_string_kg(aggregates['sum']);
            }
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';
        };
        var aggregatesrenderer_amount=function (aggregates)
        {
            var text='';
            if(!((aggregates['sum']=='0.00')||(aggregates['sum']=='')))
            {
                text=get_string_amount(aggregates['sum']);
            }
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';
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
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                showaggregates: true,
                showstatusbar: true,
                columnsheight: 110,
                rowsheight: 40,
                enablebrowserselection: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_OFFER_NAME'); ?>', dataField: 'offer_name',filtertype: 'checkedlist',pinned:true,width:'300',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['offer_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_MINIMUM_KG'); ?>', dataField: 'quantity_minimum_kg',pinned:true,width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['quantity_minimum_kg']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_MINIMUM'); ?>', dataField: 'amount_minimum',pinned:true,width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_minimum']?0:1;?>},


                    { text: 'Total</br><b>kg</b>', dataField: '<?php echo 'quantity_total_kg';?>',align:'center',cellsalign: 'right',width:'80',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['quantity_total_kg']?0:1;?>,rendered: tooltiprenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: 'Total</br><b>Amount</b>', dataField: '<?php echo 'amount_total';?>',align:'center',cellsalign: 'right',width:'120',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_total']?0:1;?>,rendered: tooltiprenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    <?php
                        foreach($dealers as $dealer)
                        {   ?>
                            { text: '<?php echo $dealer['farmer_name']; ?></br><b>kg</b>', dataField: '<?php echo 'quantity_'.$dealer['farmer_id'].'_kg';?>',renderer: header_render,align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['quantity_kg']?0:1;?>,rendered: tooltiprenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                            { text: '<?php echo $dealer['farmer_name']; ?></br><b>Amount</b>', dataField: '<?php echo 'amount_'.$dealer['farmer_id'];?>',renderer: header_render,align:'center',cellsalign: 'right',width:'150',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount']?0:1;?>,rendered: tooltiprenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                            <?php
                        }
                    ?>

                ]
            });
    });
</script>