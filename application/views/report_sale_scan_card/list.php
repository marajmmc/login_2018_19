<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();

if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                 foreach($system_preference_items as $key=>$item)
                 {
                 if((substr($key,-3)=='pkt') || (substr($key,-2)=='kg')||(substr($key,0,6)=='amount'))
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
                { name: 'id', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            //console.log(defaultHtml);
            if ((record.status=='In-Active')&& (column!="outlet_name")&& (column!="date_sale")&& (column!="invoice_no"))
            {
                element.css({ 'background-color': '#FF0000','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            if(column=='button_details')
            {
                element.html('<div><button class="btn btn-primary pop_up" data-action-link="<?php echo site_url($CI->controller_url.'/index/details_invoice'); ?>/'+record.id+'">View Details</button></div>');
            }
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
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                filterable: true,
                showfilterrow: true,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                rowsheight: 45,
                enablebrowserselection:true,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', dataField: 'outlet_name',width:'200',cellsrenderer: cellsrenderer,filtertype: 'list', hidden: <?php echo $system_preference_items['outlet_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE'); ?>', dataField: 'date_sale',width:'200',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['date_sale']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_INVOICE_NO'); ?>', dataField: 'invoice_no',width:'100',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['invoice_no']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CODE_SCAN_TYPE'); ?>', dataField: 'code_scan_type',width:'70',cellsrenderer: cellsrenderer,filtertype: 'list', hidden: <?php echo $system_preference_items['code_scan_type']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?>', dataField: 'customer_name',width:'200',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['customer_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_TOTAL'); ?>', dataField: 'amount_total',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['amount_total']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_DISCOUNT'); ?>', dataField: 'amount_discount',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['amount_discount']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_ACTUAL'); ?>', dataField: 'amount_actual',width:'100',cellsAlign:'right',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['amount_actual']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_BUTTON_DETAILS'); ?>', dataField: 'button_details',width: '120',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['button_details']?0:1;?>}
                    ]
            });
    });
</script>
