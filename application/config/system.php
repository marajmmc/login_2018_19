<?php
$config['offline_controllers']=array('home','sys_site_offline');
$config['external_controllers']=array('home');//user can use them without login
$config['system_max_actions']=8;

$config['system_site_root_folder']='login_2018_19';
$config['system_upload_image_auth_key']='ems_2018_19';
$config['system_upload_api_url']='http://45.251.59.5/api_file_server/upload';

$config['system_status_yes']='Yes';
$config['system_status_no']='No';
$config['system_status_active']='Active';
$config['system_status_inactive']='In-Active';
$config['system_status_delete']='Deleted';
$config['system_status_closed']='Closed';
$config['system_status_pending']='Pending';
$config['system_status_rollback']='Rollback';
$config['system_status_forwarded']='Forwarded';
$config['system_status_complete']='Complete';
$config['system_status_approved']='Approved';
$config['system_status_delivered']='Delivered';
$config['system_status_received']='Received';
$config['system_status_rejected']='Rejected';

$config['system_base_url_profile_picture']='http://45.251.59.5/login_2018_19/';
$config['system_base_url_picture']='http://45.251.59.5/login_2018_19/';

$config['USER_TYPE_EMPLOYEE']=1;

$config['system_status_not_done']='Not Done';
$config['system_status_done']='Done';

$config['system_save']='save';

// Outlet Type Config
$config['system_customer_type_outlet_id']=1;
$config['system_customer_type_customer_id']=2;

/*Bank & Account Config*/
// purpose
$config['system_bank_account_purpose_lc']='lc';
$config['system_bank_account_purpose_sale_receive']='sale_receive';
//System Configuration
    //login
$config['system_purpose_login_barcode_padding_top']='login_barcode_padding_top';
$config['system_purpose_login_barcode_margin_left']='login_barcode_margin_left';
$config['system_purpose_login_barcode_expire_date']='login_barcode_expire_date';
$config['system_purpose_login_barcode_title_barcode']='login_barcode_title_barcode';
$config['system_purpose_login_barcode_lot_number']='login_barcode_lot_number';
$config['system_purpose_login_barcode_ger_pur']='login_barcode_ger_pur';
$config['system_purpose_login_menu_odd_color']='login_menu_odd_color';
$config['system_purpose_login_menu_even_color']='login_menu_even_color';
$config['system_purpose_login_max_wrong_password']='login_max_wrong_password';
$config['system_purpose_login_status_mobile_verification']='login_status_mobile_verification';//for all commons
    //sms
$config['system_purpose_sms_date_expire']='sms_date_expire';
$config['system_purpose_sms_quantity_order_max']='sms_quantity_order_max';
$config['system_purpose_sms_menu_odd_color']='sms_menu_odd_color';
$config['system_purpose_sms_menu_even_color']='sms_menu_even_color';
//pos
$config['system_purpose_pos_menu_odd_color']='pos_menu_odd_color';
$config['system_purpose_pos_menu_even_color']='pos_menu_even_color';