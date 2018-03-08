<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action7']) && ($CI->permissions['action7']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Set Discount',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/discount_edit/')
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
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/discount_list/'.$variety_id)
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        var url = "<?php echo site_url($CI->controller_url.'/index/get_discount_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            type:'POST',
            dataFields: [
                { name: 'pack_size', type: 'string' },
                { name: 'outlet_name', type: 'string' },
                <?php
                foreach($farmer_types as $farmer_type)
                {
                ?>
                { name: 'discount_<?php echo $farmer_type['value'];?>', type: 'string' },
                <?php
                }
                ?>
                { name: 'id', type: 'int' }
            ],
            type: 'POST',
            url: url,
            data:{variety_id:<?php echo $variety_id; ?>}
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
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_NAME'); ?>', dataField: 'pack_size',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', dataField: 'outlet_name',width:'300',filtertype: 'list'},
                    <?php
                    foreach($farmer_types as $farmer_type)
                    {
                    ?>
                    { text: '<?php echo $farmer_type['text']; ?>', dataField: 'discount_<?php echo $farmer_type['value'];?>',width:'100',cellsalign: 'right'},
                    <?php
                        }
                        ?>
                ]
            });
    });
</script>
