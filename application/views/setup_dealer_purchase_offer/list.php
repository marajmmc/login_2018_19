<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();

if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add/'.$fiscal_year_id)
    );
}
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_EDIT"),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit/'.$fiscal_year_id)
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
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list/'.$fiscal_year_id)

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
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label>
        </div>
        <div class="col-xs-8 col-sm-4">
            <select id="fiscal_year_id" name="report[fiscal_year_id]" class="form-control">
                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                <?php
                foreach($fiscal_years as $year)
                {?>
                    <option value="<?php echo $year['value']?>" <?php if($year['value']==$fiscal_year_id){echo 'selected';} ?>><?php echo $year['text'];?></option>
                <?php
                }
                ?>
            </select>
        </div>
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

        $(document).off('change','#fiscal_year_id');
        $(document).on("change","#fiscal_year_id",function()
        {
            var fiscal_year_id=$('#fiscal_year_id').val();
            $.ajax({
                url: '<?php echo site_url($CI->controller_url.'/index/list'); ?>',
                type: 'POST',
                datatype: "JSON",
                data:{fiscal_year_id:fiscal_year_id},
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });

        });

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items/'.$fiscal_year_id);?>";

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

        var dataAdapter = new $.jqx.dataAdapter(source);
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
                    { text: '<?php echo $CI->lang->line('ID'); ?>', dataField: 'id',width:'40',cellsalign: 'right', hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name',width:'350', hidden: <?php echo $system_preference_items['name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_MINIMUM'); ?>', dataField: 'quantity_minimum',cellsalign: 'right',cellsrenderer: cellsrenderer, width:'100',hidden: <?php echo $system_preference_items['quantity_minimum']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_PER_KG'); ?>', dataField: 'amount_per_kg',cellsalign: 'right',cellsrenderer: cellsrenderer, width:'100',hidden: <?php echo $system_preference_items['amount_per_kg']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETIES'); ?>', dataField: 'varieties',hidden: <?php echo $system_preference_items['varieties']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_IS_FLOOR'); ?>', dataField: 'is_floor',filtertype: 'list',width:'150',cellsalign: 'right', hidden: <?php echo $system_preference_items['is_floor']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS'); ?>', dataField: 'status',filtertype: 'list',width:'150',cellsalign: 'right', hidden: <?php echo $system_preference_items['status']?0:1;?>}

                ]
            });
    });
</script>
