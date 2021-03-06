<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();

if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_EDIT"),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_DETAILS"),
    'class'=>'button_jqx_action',
    'data-action-link'=>site_url($CI->controller_url.'/index/details')
);
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')

);
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
        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if(($key=='id') || ($key=='amount'))
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
            id: 'id',
            type: 'POST',
            url: url
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);


            if(column=='quantity_minimum')
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
            else if(column=='amount_per_kg')
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
            source: dataAdapter,
            pageable: true,
            filterable: true,
            sortable: true,
            showfilterrow: true,
            columnsresize: true,
            pagesize:50,
            pagesizeoptions: ['20', '50', '100', '200','300','500'],
            selectionmode: 'singlerow',
            enablebrowserselection: true,
            columnsreorder: true,
            altrows: true,
            autoheight: true,
            columns: [
                { text: '<?php echo $CI->lang->line('ID'); ?>', dataField: 'id',pinned:true,width:'40',cellsalign: 'right', hidden: <?php echo $system_preference_items['id']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',pinned:true,width:'130', hidden: <?php echo $system_preference_items['variety_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',pinned:true,width:'80',cellsalign: 'right', hidden: <?php echo $system_preference_items['pack_size']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width:'110',filtertype: 'list', hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name', width:'100',hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_MINIMUM'); ?>', dataField: 'quantity_minimum',cellsalign: 'right',cellsrenderer: cellsrenderer, width:'100',hidden: <?php echo $system_preference_items['quantity_minimum']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_PER_KG'); ?>', dataField: 'amount_per_kg',cellsalign: 'right',cellsrenderer: cellsrenderer, width:'100',hidden: <?php echo $system_preference_items['amount_per_kg']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS'); ?>', dataField: 'status',filtertype: 'list',width:'150',cellsalign: 'right', hidden: <?php echo $system_preference_items['status']?0:1;?>}

            ]
        });
    });
</script>