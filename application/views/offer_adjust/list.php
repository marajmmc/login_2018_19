<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();

?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            Dealer Types
        </div>
        <div class="clearfix"></div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Dealer type</label>
        </div>
        <div class="col-sm-4 col-xs-4">
            <select id="farmer_type_id" class="form-control">
                <?php
                foreach($farmer_types as $row)
                {?>
                    <option value="<?php echo $row['value']?>" <?php if($farmer_type_id==$row['value']){echo 'selected';}?>><?php echo $row['text'];?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
</div>
<?php
$action_buttons=array();

if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Reward Points adjust list',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/list_offer_adjust')
    );
}
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
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list/'.$farmer_type_id)
);

$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));


?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            Dealers list
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

    jQuery(document).ready(function()
    {
        $(document).off("change", "#farmer_type_id");
        $(document).on("change","#farmer_type_id",function()
        {
            var farmer_type_id=$('#farmer_type_id').val();

            if(farmer_type_id>0)
            {
                $.ajax({
                    url:'<?php echo site_url($CI->controller_url.'/index/list') ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{farmer_type_id:farmer_type_id},
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items/'.$farmer_type_id);?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if(($key=='id')||(substr($key,0,5)=='offer'))
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
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if(column.substr(0,5)=='offer')
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
            height: '350px',
            source: dataAdapter,
            filterable: true,
            sortable: true,
            showfilterrow: true,
            columnsresize: true,
            selectionmode: 'singlerow',
            showaggregates: true,
            showstatusbar: true,
            altrows: true,
            pageable: true,
            pagesize:3000,
            pagesizeoptions: ['1000','3000','10000'],
            columnsreorder: true,
            enablebrowserselection: true,
            columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_BARCODE'); ?>', dataField: 'barcode', width:80,cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['barcode']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name', width:200,cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_MOBILE_NO'); ?>', dataField: 'mobile_no', width:150,hidden: <?php echo $system_preference_items['mobile_no']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', dataField: 'outlet_name',width:'200',cellsrenderer: cellsrenderer,filtertype: 'list', hidden: <?php echo $system_preference_items['outlet_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_OFFER_OFFERED'); ?>', dataField: 'offer_offered', width:200,cellsrenderer: cellsrenderer,cellsalign: 'right', hidden: <?php echo $system_preference_items['offer_offered']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_OFFER_GIVEN'); ?>', dataField: 'offer_given', width:200,cellsrenderer: cellsrenderer,cellsalign: 'right', hidden: <?php echo $system_preference_items['offer_given']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_OFFER_ADJUSTED'); ?>', dataField: 'offer_adjusted', width:200,cellsrenderer: cellsrenderer,cellsalign: 'right', hidden: <?php echo $system_preference_items['offer_adjusted']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_OFFER_BALANCE'); ?>', dataField: 'offer_balance', width:200,cellsrenderer: cellsrenderer,cellsalign: 'right', hidden: <?php echo $system_preference_items['offer_balance']?0:1;?>,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount}

                ]
        });
    });
</script>