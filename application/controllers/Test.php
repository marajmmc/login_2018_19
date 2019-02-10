<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $this->load->helper('notification');
        $sites=array('ems','sms','login');
        $data=array();
        $data['site']=$sites[rand(0,2)];
        $data['title']=$data['site']." New Notification";
        $data['body']=$data['site']." New body.See details";
        $data['description']="TO NO: Test";
        $data['description'].="\nShowroom: Test";
        $data['description'].="\nRequested By: Test user";
        $data['description'].="\nRequested Time: ".System_helper::display_date_time(time());
        $data['description'].="\nTotal Quantity: Test KG";
        $data['link']=site_url();
        $device_tokens[]='eQBjbkiI5pI:APA91bEJivleBCaKdDEfgMvT59xHAKksNGgMttUe0g_HHGClA9wsbHyAy70Dtf4SZyWOmf9WQKCWKtOa1qPrH61PC4ZaPUt7w6E5UVw0Eq21Y0oPZKw8UPUZGJT3KKfrLawy80L7UmnJ';
        $device_tokens[]='eTRuaX_2Cgg:APA91bGXtbrCUeUaOOy7oTxgVIFC0P6yrPf2JMk45cNstnOq5YsrTCPfre3APnt9BOiP977RitzRPAiFHCOaWzDMBRCB0egTc34cof6jubLyA9S6r0-3N3iEerxGDfaq0fRo2_pxHjDx';
        echo '<pre>';
        print_r(Notification_helper::send_notification($device_tokens,$data));
        echo '</pre>';
    }
}
