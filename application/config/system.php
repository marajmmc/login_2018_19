<?php
$config['offline_controllers']=array('home','sys_site_offline');
$config['external_controllers']=array('home');//user can use them without login
$config['system_max_actions']=8;

$config['system_status_active']='Active';
$config['system_status_inactive']='In-Active';
$config['system_status_delete']='Deleted';

$config['system_base_url_profile_picture']='http://50.116.76.180/login/';
//$config['system_base_url_profile_picture']='http://127.0.0.1/login_2018_19/';
$config['system_base_url_customer_profile_picture']='http://180.234.223.205/login_2018_19/';
$config['system_base_url_customer_document']='http://180.234.223.205/login_2018_19/';

$config['USER_TYPE_EMPLOYEE']=1;

$config['system_status_not_done']='Not Done';
$config['system_status_done']='Done';

$config['system_save']='save';

// Outlet Type Config
$config['system_customer_type_outlet_id']=1;
$config['system_customer_type_customer_id']=2;

/*Bank & Account Config*/
// purpose
$config['system_bank_account_purpose_lc']='Lc';
$config['system_bank_account_purpose_sale_receive']='Sale Receive';
//System Configuration
$config['system_purpose_sms_date_expire']='sms_date_expire';
$config['system_purpose_sms_quantity_order_max']='sms_quantity_order_max';
$config['system_purpose_pos_barcode_expire_date']='pos_barcode_expire_date';
