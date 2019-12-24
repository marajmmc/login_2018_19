<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons = array();
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'class' => 'button_action_download',
        'data-title' => "Print",
        'data-print' => true
    );
}
if (isset($CI->permissions['action5']) && ($CI->permissions['action5'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DOWNLOAD"),
        'class' => 'button_action_download',
        'data-title' => "Download"
    );
    unset($options['fiscal_year_id']);
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_CSV"),
        'href'=>site_url($CI->controller_url.'_csv/system_list_outlets_dealers_varieties/'.urlencode(json_encode($options))),
        'class'=>'external',
        'target'=>'_blank'
    );
}
if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
{
    $action_buttons[] = array
    (
        'label' => 'Preference',
        'href' => site_url($CI->controller_url . '/index/set_preference_outlets_dealers_varieties')
    );
}
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
    {
        /* $CI->load->view('preference', array('system_preference_items' => $system_preference_items)); */
        ?>
        <div class="col-xs-2"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="outlet_name" <?php if($system_preference_items['outlet_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?></span></label></div></div>
        <div class="col-xs-2"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="dealer_name" <?php if($system_preference_items['dealer_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_DEALER_NAME'); ?></span></label></div></div>
        <div class="col-xs-2"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="amount_total" <?php if($system_preference_items['amount_total']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AMOUNT_TOTAL'); ?></span></label></div></div>
        <div class="col-xs-2"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_pkt" value="quantity_pkt" <?php if($system_preference_items['quantity_pkt']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_QUANTITY_PKT'); ?></span></label></div></div>
        <div class="col-xs-2"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_kg" value="quantity_kg" <?php if($system_preference_items['quantity_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_QUANTITY_KG'); ?></span></label></div></div>
        <div class="col-xs-2"><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_amount" value="amount" <?php if($system_preference_items['amount']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AMOUNT'); ?></span></label></div></div>
    <?php
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).off("click", ".system_jqx_column_pkt");
        $(document).on("click", ".system_jqx_column_pkt", function(event)
        {
            var jqx_grid_id = '#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php foreach($arm_varieties as $arm_variety){ ?>
                    $(jqx_grid_id).jqxGrid('showcolumn', '<?php echo 'quantity_'.$arm_variety['variety_id']. '_' . $arm_variety['pack_size_id'].'_pkt';?>');
                <?php } ?>
            }
            else
            {
                <?php foreach($arm_varieties as $arm_variety){ ?>
                    $(jqx_grid_id).jqxGrid('hidecolumn', '<?php echo 'quantity_'.$arm_variety['variety_id']. '_' . $arm_variety['pack_size_id'].'_pkt';?>');
                <?php } ?>
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });

        $(document).off("click", ".system_jqx_column_kg");
        $(document).on("click", ".system_jqx_column_kg", function(event)
        {
            var jqx_grid_id = '#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php foreach($arm_varieties as $arm_variety){ ?>
                    $(jqx_grid_id).jqxGrid('showcolumn', '<?php echo 'quantity_'.$arm_variety['variety_id']. '_' . $arm_variety['pack_size_id'].'_kg';?>');
                <?php } ?>
            }
            else
            {
                <?php foreach($arm_varieties as $arm_variety){ ?>
                    $(jqx_grid_id).jqxGrid('hidecolumn', '<?php echo 'quantity_'.$arm_variety['variety_id']. '_' . $arm_variety['pack_size_id'].'_kg';?>');
                <?php } ?>
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });

        $(document).off("click", ".system_jqx_column_amount");
        $(document).on("click", ".system_jqx_column_amount", function(event)
        {
            var jqx_grid_id = '#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php foreach($arm_varieties as $arm_variety){ ?>
                    $(jqx_grid_id).jqxGrid('showcolumn', '<?php echo 'amount_'.$arm_variety['variety_id']. '_' . $arm_variety['pack_size_id'];?>');
                <?php } ?>
            }
            else
            {
                <?php foreach($arm_varieties as $arm_variety){ ?>
                    $(jqx_grid_id).jqxGrid('hidecolumn', '<?php echo 'amount_'.$arm_variety['variety_id']. '_' . $arm_variety['pack_size_id'];?>');
                <?php } ?>
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_outlets_dealers_varieties'); ?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                    { name: 'outlet_name', type: 'string' },
                    { name: 'dealer_name', type: 'string' },
                    { name: 'amount_total', type: 'number' },
                <?php
                foreach($arm_varieties as $arm_variety)
                {
                ?>
                    { name: '<?php echo 'quantity_' . $arm_variety['variety_id']. '_' . $arm_variety['pack_size_id'] . '_pkt';?>', type: 'number' },
                    { name: '<?php echo 'quantity_' . $arm_variety['variety_id'] . '_' . $arm_variety['pack_size_id']. '_kg';?>', type: 'number' },
                    { name: '<?php echo 'amount_' . $arm_variety['variety_id']. '_' . $arm_variety['pack_size_id'];?>', type: 'number' },
                <?php
                }
                ?>
            ],
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };

        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var header_render=function (text, align)
        {
            return '<div style="margin: 5px;text-align: center">'+text+'</div>';
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            //console.log(defaultHtml);

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

        var aggregatesrenderer_quantity=function (aggregates)
        {
            var text='';
            if(!((aggregates['sum']=='0.00')||(aggregates['sum']=='')))
            {
                text=get_string_quantity(aggregates['sum']);
            }
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';
        };
        var aggregatesrenderer_kg=function (aggregates)
        {
            var text='';
            if(!((aggregates['sum']=='0.00')||(aggregates['sum']=='')))
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
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
        {
            width: '100%',
            height:'450px',
            source: dataAdapter,
            sortable: true,
            columnsresize: true,
            columnsreorder: true,
            altrows: true,
            enabletooltips: true,
            showaggregates: true,
            showstatusbar: true,
            rowsheight: 40,
            columnsheight: 110,
            pageable: true,
            filterable: true,
            showfilterrow: true,
            enablebrowserselection: true,
            pagesize: 1000,
            pagesizeoptions: ['10', '20', '50', '100', '200', '300', '500','1000'],
            selectionmode: 'singlerow',
            headerZIndex: 1000,
            columns: [
                { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', pinned: true, dataField: 'outlet_name', width: '150', align: 'center', filtertype:'list', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['outlet_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_DEALER_NAME'); ?>', pinned: true, dataField: 'dealer_name', width: '180', align: 'center', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['dealer_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_TOTAL'); ?>', pinned: true, dataField: 'amount_total', width: '180',cellsalign: 'right', filtertype:'none', cellsrenderer: cellsrenderer, align: 'center',  rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['amount_total']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                <?php foreach($arm_varieties as $arm_variety) { ?>
                { text: '<?php echo $arm_variety['variety_header']; ?> <br/><br/><b> <?php echo $CI->lang->line('LABEL_QUANTITY_PKT'); ?></b>',renderer: header_render, dataField: '<?php echo 'quantity_'.$arm_variety['variety_id'].'_'.$arm_variety['pack_size_id'].'_pkt';?>',cellsalign: 'right', width: '180', cellsrenderer: cellsrenderer, align: 'center', filtertype:'none', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['quantity_pkt']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_quantity},
                { text: '<?php echo $arm_variety['variety_header']; ?> <br/><br/><b> <?php echo $CI->lang->line('LABEL_QUANTITY_KG'); ?></b>',renderer: header_render, dataField: '<?php echo 'quantity_'.$arm_variety['variety_id'].'_'.$arm_variety['pack_size_id'].'_kg';?>',cellsalign: 'right', width: '180', cellsrenderer: cellsrenderer, align: 'center', filtertype:'none', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['quantity_kg']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                { text: '<?php echo $arm_variety['variety_header']; ?> <br/><br/><b> <?php echo $CI->lang->line('LABEL_AMOUNT'); ?></b>',renderer: header_render, dataField: '<?php echo 'amount_'.$arm_variety['variety_id'].'_'.$arm_variety['pack_size_id'];?>',cellsalign: 'right', width: '180', cellsrenderer: cellsrenderer, filtertype:'none', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['amount']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                <?php } ?>

            ]
        });
    });
</script>
