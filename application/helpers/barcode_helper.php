<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_helper
{

    public static function get_barcode_variety($crop_id,$variety_id,$pack_id)
    {
        return str_pad($crop_id,2,0,STR_PAD_LEFT).str_pad($variety_id,4,0,STR_PAD_LEFT).str_pad($pack_id,2,0,STR_PAD_LEFT);
    }
    //specially for variety. because it need crop_id,type_id,pack_id
    public static function get_barcode_variety_by_price_id($price_id)
    {
        //assuming $price_id is valid
        //not checking validation
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_login_setup_classification_variety_price').' price');
        $CI->db->select('price.id,price.variety_id,price.pack_size_id');
        $CI->db->join($CI->config->item('table_login_setup_classification_varieties').' v','v.id=price.variety_id','INNER');
        $CI->db->join($CI->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $CI->db->select('crop_type.crop_id crop_id');
        $CI->db->where('price.id',$price_id);
        $result=$CI->db->get()->row_array();
        return Barcode_helper::get_barcode_variety($result['crop_id'],$result['variety_id'],$result['pack_size_id']);
    }
}
