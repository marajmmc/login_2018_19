<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
if (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line('ACTION_EDIT'),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/edit')
    );
}
if (isset($CI->permissions['action3']) && ($CI->permissions['action3'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => 'Remove',
        'class' => 'button_jqx_action',
        'data-message-confirm' => 'Sure to Remove Discounts of all Outlets?',
        'data-action-link' => site_url($CI->controller_url . '/index/save_delete')
    );
}
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
}
if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
{
    $action_buttons[] = array
    (
        'label' => 'Preference',
        'href' => site_url($CI->controller_url . '/index/set_preference')
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list')
);
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
        $CI->load->view('preference', array('system_preference_items' => $system_preference_items));
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items'); ?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                    foreach($system_preference_items as $key => $value)
                    {
                        if($key=='id' || $key=='pack_size_id' || $key=='variety_id' || $key=='order')
                        {
                        ?>
                        { name: '<?php echo $key; ?>', type: 'int' },
                        <?php
                        }
                        else
                        {
                        ?>
                        { name: '<?php echo $key; ?>', type: 'string' },
                        <?php
                        }
                    }
                ?>
            ],
            id: 'id',
            type: 'POST',
            url: url
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({ position: 'mouse', content: $(element).text() });
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
                pagesize: 50,
                pagesizeoptions: ['50', '100', '200', '300', '500'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection: true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_ID'); ?>', dataField: 'variety_id', rendered: tooltiprenderer, pinned: true, width: '80', cellsalign: 'right', hidden: <?php echo $system_preference_items['variety_id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE_ID'); ?>', dataField: 'pack_size_id', rendered: tooltiprenderer, pinned: true, width: '40', cellsalign: 'right', hidden: <?php echo $system_preference_items['pack_size_id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name', rendered: tooltiprenderer, pinned: true, width: '220', hidden: <?php echo $system_preference_items['variety_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size', rendered: tooltiprenderer, pinned: true, width: '80', cellsalign: 'right', hidden: <?php echo $system_preference_items['pack_size']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NUM_OUTLETS_DISCOUNTED'); ?>', dataField: 'num_outlets_discounted', rendered: tooltiprenderer, pinned: true, width: '80', cellsalign: 'right', filtertype: 'list', hidden: <?php echo $system_preference_items['num_outlets_discounted']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', rendered: tooltiprenderer, width: '150', filtertype: 'list', hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name', rendered: tooltiprenderer, width: '100', hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('STATUS'); ?>', dataField: 'status', rendered: tooltiprenderer, filtertype: 'list', width: '100', hidden: <?php echo $system_preference_items['status']?0:1;?>}
                ]
            });
    });
</script>
