<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list')
);

$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-2"> &nbsp; </div>
        <div class="col-xs-8">
            <?php
            $i = 0;
            foreach ($outlets as $key => $outlet)
            {
                ?>
                <div class="panel panel-default" style="margin-bottom:5px">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <label style="display:inline-block; width:100%; margin:0; line-height: 1.9;">
                                <a class="external text-danger" data-toggle="collapse" data-target="#collapse<?php echo $key; ?>" href="#">
                                    <?php echo '+ ' . (++$i) . '. ' . $outlet['name']; ?>
                                </a>

                                <div style="float:right; display:inline-block; width:180px">
                                    <a class="btn btn-sm btn-primary" href="<?php echo site_url($CI->controller_url . '/index/edit/' . $outlet['customer_id']); ?>" style="float:right"> EDIT </a>
                                    <?php if ($outlet['number_of_discounts'] > 0)
                                    {
                                        ?>
                                        <a class="btn btn-sm btn-danger" href="<?php echo site_url($CI->controller_url . '/index/save_delete/' . $outlet['customer_id']); ?>" data-message-confirm="Sure to Remove All Discounts of '<?php echo $outlet['name']; ?>' ?" style="float:right; margin-right:10px"> REMOVE </a>
                                    <?php } ?>
                                </div>
                                <span style="float:right; <?php echo ($outlet['number_of_discounts'] > 0) ? '' : 'font-weight:normal' ?>"> Discount(s): <?php echo $outlet['number_of_discounts']; ?> </span>
                            </label>
                        </h4>
                    </div>

                    <div id="collapse<?php echo $key; ?>" class="panel-collapse collapse">
                        <div class="row widget" style="margin:0; padding:20px 0; border:none">

                            <div class="row show-grid" style="margin:0">
                                <div class="col-xs-3"> &nbsp; </div>
                                <div class="col-xs-6">
                                    <?php
                                    if (isset($outlet['discounts']) && (sizeof($outlet['discounts']) > 0))
                                    {
                                        ?>
                                        <table class="table table-bordered" style="margin:0">
                                            <tr>
                                                <th style="text-align:right">Amount (BDT)</th>
                                                <th style="text-align:right">Discount (%)</th>
                                            </tr>
                                            <?php
                                            foreach ($outlet['discounts'] as $amount => $discount)
                                            {
                                                ?>
                                                <tr>
                                                    <td style="text-align:right"><?php echo System_helper::get_string_amount($amount); ?></td>
                                                    <td style="text-align:right"><?php echo $discount; ?></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </table>
                                    <?php
                                    }
                                    else
                                    {
                                        echo '<h4>- No Discount Found -</h4>';
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events();
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
    });
</script>
