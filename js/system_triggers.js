function system_preset(params={})
{
    system_resized_image_files=[];
    if(params.controller!==undefined)
    {
        // controller condition code
    }
}

function system_off_events()
{
    /*Product */
    $(document).off('change','#crop_id');
    $(document).off("change",".crop_id");
    $(document).off('change','#crop_type_id');
    $(document).off("change",".crop_type_id");
    $(document).off('change','#variety_id');
    $(document).off("change",".variety_id");
    $(document).off('change','#pack_size_id');
    $(document).off("change",".pack_size_id");

    /*warehouse*/
    $(document).off('change','#warehouse_id');
    $(document).off("change",".warehouse_id");
    $(document).off('change','#warehouse_id_source');
    $(document).off('change','.warehouse_id_source');
    $(document).off('change','#warehouse_id_destination');
    $(document).off('change','.warehouse_id_destination');
    
    /*Date/Time/Year*/
    $(document).off("change","#fiscal_year_id");
    $(document).off('change', '.datepicker');

    /*Location*/
    $(document).off('change', '#division_id');
    $(document).off('change', '.division_id');
    $(document).off('change', '#zone_id');
    $(document).off('change', '.zone_id');
    $(document).off('change', '#territory_id');
    $(document).off('change', '.territory_id');
    $(document).off('change', '#district_id');
    $(document).off('change', '.district_id');

    /*Outlet*/
    $(document).off('change', '#customer_id');
    $(document).off('change', '#outlet_id');
    $(document).off('change', '#outlet_id_source');
    $(document).off('change', '#outlet_id_destination');
    $(document).off('change', '#dealer_id');

    /*Add More & Pop Up*/
    $(document).off("click", ".system_button_add_more");
    $(document).off('click','.system_button_add_delete');
    $(document).off("click", ".pop_up");

    /*Module & Task*/
    $(document).off("click", ".task_action_all");
    $(document).off("click", ".task_header_all");

    /*Quantity/Amout/Number*/
    $(document).off('input','.amount');
    $(document).off('input', '.quantity_approve');
    $(document).off('input', '.quantity_request');

    /*jqx grid*/
    $(document).off('click', '#button_action_save_jqx');

    /*Others*/
    $(document).off('change','#purpose');

}