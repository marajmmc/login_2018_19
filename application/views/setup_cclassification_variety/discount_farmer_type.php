<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<table class="table table-bordered">
    <thead>
    <tr>
        <th style="min-width: 150px;">Farmer Type</th>
        <th style="min-width: 150px;"><?php echo $this->lang->line('ACTION_DISCOUNT'); ?> (<?php echo $this->lang->line('LABEL_PERCENTAGE'); ?>)</th>
        <th style="min-width: 150px;">Number Of Days</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($farmer_types as $farmer_type){?>
        <tr>
            <td>
                <label><?php echo $farmer_type['name']?></label>
            </td>
            <td class="text-right">
                <input type="text" id="" class="form-control text-right float_type_positive discount_percentage" name="items[<?php echo $farmer_type['id']?>][discount_percentage]" value="<?php echo (isset($discounts[$farmer_type['id']]))? $discounts[$farmer_type['id']]['discount_percentage']:0; ?>">
            </td>
            <td class="text-right">
                <input type="text" id="" class="form-control text-right float_type_positive expire_day" name="items[<?php echo $farmer_type['id']?>][expire_day]" value="<?php echo (isset($discounts[$farmer_type['id']]))? $discounts[$farmer_type['id']]['expire_day']:0; ?>">
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>