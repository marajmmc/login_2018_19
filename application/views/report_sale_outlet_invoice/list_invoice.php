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
        'href'=>site_url($CI->controller_url.'/index/set_preference_list_invoice')
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
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_list_invoice');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if((substr($key,0,6)=='amount') )
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'number' },
                        <?php
                    }
                    else
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'string' },
                        <?php
                    }
                }
                ?>
                { name: 'status', type: 'string' }
            ],
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if ((record.status=='In-Active')&& (record.amount_actual<0)&& (column!="outlet_name")&& (column!="date_sale")&& (column!="invoice_no"))
            {
                element.css({ 'background-color': '#D100C6','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else if ((record.status=='In-Active')&& (column!="outlet_name")&& (column!="date_sale")&& (column!="invoice_no"))
            {
                element.css({ 'background-color': '#FF0000','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else if ((record.amount_actual<0)&& (column!="outlet_name")&& (column!="date_sale")&& (column!="invoice_no"))
            {
                element.css({ 'background-color': '#00E5DD','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }

            if((column.substr(0,6)=='amount')||(column=='price_unit_pack'))
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
        var aggregatesrenderer_amount=function (aggregates)
        {
            var text='';
            if(!((aggregates['sum']=='0.00')||(aggregates['sum']=='')))
            {
                text=get_string_amount(aggregates['sum']);
            }
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';
        };
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                rowsheight: 35,
                source: dataAdapter,
                sortable: true,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                showaggregates: true,
                showstatusbar: true,
                pageable: true,
                filterable: true,
                showfilterrow: true,
                enablebrowserselection: true,
                pagesize: 5000,
                pagesizeoptions: ['100', '200', '300', '500','1000','5000'],
                selectionmode: 'singlerow',
                headerZIndex: 1000,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', pinned: true, dataField: 'outlet_name', width: '150',cellsrenderer: cellsrenderer, align: 'center', filtertype:'list',hidden: <?php echo $system_preference_items['outlet_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_INVOICE_NO'); ?>', pinned:true,dataField: 'invoice_no',width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['invoice_no']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_FARMER_NAME'); ?>', pinned: true, dataField: 'farmer_name',cellsrenderer: cellsrenderer, width: '200', align: 'center', filtertype:'list',hidden: <?php echo $system_preference_items['farmer_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_SALE'); ?>', dataField: 'date_sale',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['date_sale']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_CANCEL'); ?>', dataField: 'date_cancel',width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['date_cancel']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_SALES_PAYMENT_METHOD'); ?>', dataField: 'sales_payment_method',filtertype: 'list',width:'80',cellsAlign:'right',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['sales_payment_method']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_TOTAL'); ?>', dataField: 'amount_total',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_total']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_DISCOUNT_VARIETY_TOTAL'); ?>', dataField: 'amount_discount_variety_total',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_discount_variety_total']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_DISCOUNT_SLAB_PERCENTAGE'); ?>', dataField: 'discount_slab_percentage',filtertype: 'list',width:'50',cellsAlign:'right',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['discount_slab_percentage']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_DISCOUNT_SELF'); ?>', dataField: 'amount_discount_self',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_discount_self']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_PAYABLE'); ?>', dataField: 'amount_payable',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_payable']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_PAYABLE_ACTUAL'); ?>', dataField: 'amount_payable_actual',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_payable_actual']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_ACTUAL'); ?>', dataField: 'amount_actual',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_actual']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',cellsrenderer: cellsrenderer, width: '130', align: 'center',hidden: <?php echo $system_preference_items['variety_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',cellsrenderer: cellsrenderer, width: '100',cellsAlign:'right', align: 'center',hidden: <?php echo $system_preference_items['pack_size']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PRICE_UNIT_PACK'); ?>', dataField: 'price_unit_pack',cellsrenderer: cellsrenderer, width: '100',cellsAlign:'right', align: 'center',hidden: <?php echo $system_preference_items['price_unit_pack']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY'); ?>', dataField: 'quantity',cellsrenderer: cellsrenderer, width: '100', align: 'center',cellsAlign:'right',hidden: <?php echo $system_preference_items['quantity']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_KG'); ?>', dataField: 'quantity_kg',cellsrenderer: cellsrenderer, width: '100', align: 'center',cellsAlign:'right',hidden: <?php echo $system_preference_items['quantity_kg']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_VARIETY_TOTAL'); ?>', dataField: 'amount_variety_total',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_variety_total']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_DISCOUNT_VARIETY'); ?>', dataField: 'amount_discount_variety',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_discount_variety']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_VARIETY_ACTUAL'); ?>', dataField: 'amount_variety_actual',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['amount_variety_actual']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_VARIETY_ACTUAL_TOTAL'); ?>', dataField: 'amount_variety_actual_total', width:100,cellsrenderer: cellsrenderer,cellsalign: 'right', hidden: <?php echo $system_preference_items['amount_variety_actual_total']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount}

                ]
            });
    });
</script>
