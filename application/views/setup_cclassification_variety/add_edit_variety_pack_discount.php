<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="min-width: 150px;">Farmer Type</th>
            <th style="min-width: 150px;">Discount Percentage</th>
            <th style="min-width: 150px;">Number Of Days</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($items as $item){?>
            <tr>
                <td>
                    <label><?php echo $item['name']?></label>
                </td>

                <td class="text-right">
                    <input type="text" id="" class="form-control text-right float_type_positive discount_percentage" name="items[<?php echo $outlet_id?>][<?php echo $item['id']?>][discount_percentage]" value="<?php echo $item['discount_percentage']?>">
                </td>


                <td class="text-right">
                    <input type="text" id="" class="form-control text-right float_type_positive expire_day" name="items[<?php echo $outlet_id?>][<?php echo $item['id']?>][expire_day]" value="<?php echo $item['expire_day']?>">
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>