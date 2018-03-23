<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
?>
<div style="width: 320px;font-size: 10px;text-align: center; font-weight: bold;line-height: 10px;margin-left:-40px;padding-top: 40px; ">
    <div style="width: 150px;float: left;">
        <img src="<?php echo site_url('barcode/index/variety_by_price_id/'.($id).'/150/20');  ?>">
        <?php
        foreach($items as $line)
        {
            if($line['show']==1)
            {
            ?>
                <div><?php echo $line['text'];?></div>
            <?php
            }
        }
        ?>
    </div>
    <div style="width: 150px;float: left;margin-left: 20px;">
        <img src="<?php echo site_url('barcode/index/variety_by_price_id/'.($id).'/150/20');  ?>">
        <?php
        foreach($items as $line)
        {
            if($line['show']==1)
            {
                ?>
                <div><?php echo $line['text'];?></div>
            <?php
            }
        }
        ?>
    </div>
</div>