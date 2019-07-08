<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
//line1 is outlet name and outlet id
//assuming all outlet id is 3
$price_outlet_id=$id.'_000';
//if($items['line2']['show']==1)
{
    $price_outlet_id=$id.'_'.substr($items['line2']['text'],-3);
}

?>
<div style="width: 320px;font-size: 10px;text-align: center; font-weight: bold;line-height: 10px;margin-left:<?php echo $margin_left; ?>px;padding-top: <?php echo $padding_top; ?>px; ">
    <div style="width: 150px;float: left;">

        <?php
        //line3==showroom
        foreach($items as $key=>$line)
        {
            if($line['show']==1)
            {
            ?>
                <?php
                if($key=='line2')
                {
                    ?>
                    <div><?php echo substr($line['text'],0,-3);?></div>

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
        <img src="<?php echo site_url('barcode/index/variety_by_price_outlet_id/'.($price_outlet_id).'/150/20');  ?>">
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
                if($key=='line2')
                {
                    ?>
                    <div><?php echo substr($line['text'],0,-3);?></div>

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
        <img src="<?php echo site_url('barcode/index/variety_by_price_outlet_id/'.($price_outlet_id).'/150/20');  ?>">
    </div>
</div>