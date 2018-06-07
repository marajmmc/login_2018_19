<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
?>
<div style="width: 320px;font-size: 10px;text-align: center; font-weight: bold;line-height: 10px;margin-left:-40px;padding-top: 18px; ">
    <div style="width: 150px;float: left;">

        <?php
        //line3==showroom
        foreach($items as $key=>$line)
        {
            if($line['show']==1)
            {
            ?>
                <?php
                if($key=='line3')
                {
                    ?>
                    <div><?php echo substr($line['text'],0,-3);?></div>
                    <img src="<?php echo site_url('barcode/index/outlet_for_sticker/'.substr($line['text'],-3).'/150/15');  ?>">
                    <?php
                }
                else
                {
                    ?>
                    <div><?php echo $line['text'];?></div>
                <?php
                }
                ?>

            <?php
            }
        }
        ?>
        <img src="<?php echo site_url('barcode/index/variety_by_price_id/'.($id).'/150/15');  ?>">
    </div>
    <div style="width: 150px;float: left;margin-left: 20px;">
        <?php
        //line3==showroom
        foreach($items as $key=>$line)
        {
            if($line['show']==1)
            {
                ?>

                <?php
                if($key=='line3')
                {
                    ?>
                    <div><?php echo substr($line['text'],0,-3);?></div>
                    <img src="<?php echo site_url('barcode/index/outlet_for_sticker/'.substr($line['text'],-3).'/150/15');  ?>">
                <?php
                }
                else
                {
                    ?>
                    <div><?php echo $line['text'];?></div>
                    <?php
                }
                ?>

            <?php
            }
        }
        ?>
        <img src="<?php echo site_url('barcode/index/variety_by_price_id/'.($id).'/150/15');  ?>">
    </div>
</div>