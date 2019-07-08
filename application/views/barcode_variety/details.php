<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Generate Barcode',
        'id'=>'button_action_report',
        'data-form'=>'#save_form'
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'onClick'=>"window.print()"
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/details/'.$item['id'])
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="hidden-print" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
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
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Expire <?php echo $CI->lang->line('LABEL_DATE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['date_expire'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Line 1</label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="items[line1][text]" class="form-control" value="<?php echo $item['title_barcode'];?>" <?php if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1))){echo 'readonly';} ?>/>
            </div>
            <div class="col-xs-2">
                <select name="items[line1][show]" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-xs-2">
                <label class="control-label">Show</label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Line 2(outlet)</label>
            </div>
            <div class="col-xs-4">
                <select name="items[line2][text]" class="form-control">
                    <option value="000"><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($outlets as $row)
                    {?>
                        <option value="<?php echo $row['text'].$row['value']?>"><?php echo $row['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="col-xs-2">
                <select name="items[line2][show]" class="form-control">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            <div class="col-xs-2">
                <label class="control-label">Show</label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Line 3</label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="items[line3][text]" class="form-control" value="<?php echo $item['lot_number'];?>" <?php if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1))){echo 'readonly';} ?>/>
            </div>
            <div class="col-xs-2">
                <select name="items[line3][show]" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-xs-2">
                <label class="control-label">Show</label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Line 4</label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="items[line4][text]" class="form-control" value="MRP Tk: <?php echo $item['price'].' (Wt: '.$item['pack_size'].'g)'; ?>" <?php if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1))){echo 'readonly';} ?>/>
            </div>
            <div class="col-xs-2">
                <select name="items[line4][show]" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-xs-2">
                <label class="control-label">Show</label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Line 5</label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="items[line5][text]" class="form-control" value="<?php echo $item['date_expire'];?>" <?php if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1))){echo 'readonly';} ?>/>
            </div>
            <div class="col-xs-2">
                <select name="items[line5][show]" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-xs-2">
                <label class="control-label">Show</label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Line 6</label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="items[line6][text]" class="form-control" value="<?php echo $item['ger_pur'];?>" <?php if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1))){echo 'readonly';} ?>/>
            </div>
            <div class="col-xs-2">
                <select name="items[line6][show]" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-xs-2">
                <label class="control-label">Show</label>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>
<div id="system_report_container">
</div>