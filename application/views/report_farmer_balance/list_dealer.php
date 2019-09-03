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
        'href'=>site_url($CI->controller_url.'/index/set_preference_list_dealer')
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_list_dealer');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if(($key=='id') || ($key=='amount_credit_limit') || ($key=='amount_credit_balance') )
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
            ],
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if(column.substr(0,6)=='amount')
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
            if (record.date=="Initial")
            {
                element.css({ 'background-color': system_report_color_type,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else if (record.date=="Sub Total")
            {
                element.css({ 'background-color': system_report_color_crop,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else if (record.date=="Total")
            {

                element.css({ 'background-color': system_report_color_grand,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
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
                height: '350px',
                source: dataAdapter,
                columnsresize: true,
                selectionmode: 'singlerow',
                altrows: true,
                rowsheight: 40,
                columnsreorder: true,
                enablebrowserselection: true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_DATE'); ?>', dataField: 'date', width:200,cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['date']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ACTION_TRANSACTION'); ?>', dataField: 'action_transaction',cellsrenderer: cellsrenderer, width:80, hidden: <?php echo $system_preference_items['action_transaction']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ACTION_NO'); ?>', dataField: 'action_no',width:'100',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['action_no']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_DEBIT'); ?>', dataField: 'amount_debit', width:100,cellsrenderer: cellsrenderer,cellsalign: 'right', hidden: <?php echo $system_preference_items['amount_debit']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_CREDIT'); ?>', dataField: 'amount_credit', width:100,cellsrenderer: cellsrenderer,cellsalign: 'right', hidden: <?php echo $system_preference_items['amount_credit']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_BALANCE'); ?>', dataField: 'amount_balance', width:100,cellsrenderer: cellsrenderer,cellsalign: 'right', hidden: <?php echo $system_preference_items['amount_balance']?0:1;?>}

                ]
            });
    });
</script>
