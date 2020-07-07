<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
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
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['crop_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['crop_type_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['variety_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_SIZE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['pack_size'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRICE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo number_format($item['price'],2);?></label>
            </div>
        </div>
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_MINIMUM');?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_AMOUNT_PER_KG');?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_CREATED_TIME');?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_STATUS');?></th>
                </tr>
                </thead>
                <tbody>
                <?php

                foreach($history_offer as $row)
                {
                    ?>
                    <tr>
                        <td><label><?php echo System_helper::get_string_kg($row['quantity_minimum']);?></label></td>
                        <td><label><?php echo System_helper::get_string_amount($row['amount_per_kg']);?></label></td>
                        <td><label><?php echo System_helper::display_date_time($row['date_created']);?></label></td>
                        <td><label><?php echo $row['status'];?></label></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>

    </div>

    <div class="clearfix"></div>
