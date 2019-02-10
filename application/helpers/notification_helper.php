<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_helper
{
    //$data['site']";
    //$data['title']
    //$data['body']
    //$data['description']
    //$data['link']
    public static $NOTIFICATION_URL ='https://fcm.googleapis.com/fcm/send';
    public static $API_SERVER_KEY ='AAAA_oBcedc:APA91bHehwd0bwlMoSCfv-iSgoCK_1_HhbdDFxBnLfH-Sds1GpZ_42pVLH1FhwsyL4r9wJSVSZywusL3S6oMCLgZN-zNTqNgElUUW0rxXLuNkAwZw0k5G82fEK9fKodfsqAvopeRxkod';
    public static function send_notification($device_tokens,$data)
    {
        $notification_data = [
            'registration_ids' => $device_tokens,
            'data' => $data
        ];
        $headers = [
            'Authorization: key=' . Notification_helper::$API_SERVER_KEY,
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,Notification_helper::$NOTIFICATION_URL);
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);//wait for response
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //timeout in seconds 2min
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $notification_data));
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close( $ch );
        return array('status_http'=>$http_status,'response'=>$response);
    }
}
