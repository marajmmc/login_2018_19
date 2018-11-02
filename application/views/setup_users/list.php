<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line('ACTION_NEW'),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change password',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_password')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Assign sites',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/assign_sites')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change company',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/change_company')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change area',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_area')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change user group',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/change_user_group')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Authentication Setup',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_authentication_setup')
    );
}
if(isset($CI->permissions['action3']) && ($CI->permissions['action3']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change Employee ID',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_employee_id')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change username',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_username')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change status',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_status')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
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
    $action_buttons[]=array(
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')

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

        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'employee_id', type: 'string' },
                { name: 'username', type: 'string' },
                { name: 'name', type: 'string' },
                { name: 'user_group', type: 'string' },
                { name: 'user_area', type: 'string' },
                { name: 'company_name', type: 'string' },
                { name: 'other_sites', type: 'string' },
                { name: 'designation_name', type: 'string' },
                { name: 'department_name', type: 'string' },
                { name: 'mobile_no', type: 'string' },
                { name: 'email', type: 'string' },
                { name: 'blood_group', type: 'string' },
                { name: 'status', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
            url: url
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
                    { text: '<?php echo $CI->lang->line('ID'); ?>', dataField: 'id',pinned:true,width:'40',cellsAlign:'right', hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_EMPLOYEE_ID'); ?>', dataField: 'employee_id',pinned:true,width:'40', hidden: <?php echo $system_preference_items['employee_id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_USERNAME'); ?>', dataField: 'username',pinned:true,width:'100', hidden: <?php echo $system_preference_items['username']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name',width:'150', hidden: <?php echo $system_preference_items['name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_USER_GROUP'); ?>', dataField: 'user_group',filtertype: 'list',width:'100', hidden: <?php echo $system_preference_items['user_group']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_USER_AREA'); ?>', dataField: 'user_area',filtertype: 'list',width:'150', hidden: <?php echo $system_preference_items['user_area']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_COMPANY_NAME'); ?>', dataField: 'company_name',width:'200', hidden: <?php echo $system_preference_items['user_area']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_OTHER_SITES'); ?>', dataField: 'other_sites',width:'150', hidden: <?php echo $system_preference_items['other_sites']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DESIGNATION_NAME'); ?>', dataField: 'designation_name',filtertype: 'list',width:'150', hidden: <?php echo $system_preference_items['designation_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME'); ?>', dataField: 'department_name',filtertype: 'list',width:'150', hidden: <?php echo $system_preference_items['department_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_MOBILE_NO'); ?>', dataField: 'mobile_no',width:'120', hidden: <?php echo $system_preference_items['mobile_no']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_EMAIL'); ?>', dataField: 'email',width:'200', hidden: <?php echo $system_preference_items['email']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_BLOOD_GROUP'); ?>', dataField: 'blood_group',filtertype: 'list',width:'40', hidden: <?php echo $system_preference_items['blood_group']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('STATUS'); ?>', dataField: 'status',filtertype: 'list',width:'70',cellsalign: 'right', hidden: <?php echo $system_preference_items['status']?0:1;?>}

                ]
            });
    });
</script>
