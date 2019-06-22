<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_SAVE"),
    'id' => 'button_action_save',
    'data-form' => '#save_form'
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<form id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
    <input type="hidden" id="outlet_id" name="item[outlet_id]" value="<?php echo $item['outlet_id']; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php
                echo $title;
                echo ($item['customer_code']) ? '(ID: ' . $item['outlet_id'] . ')' : '';
                ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label">
                    <?php
                    echo $item['name'];
                    if ($item['customer_code'])
                    {
                        echo ' ( CODE: ' . $item['customer_code'] . ' )';
                    }
                    ?>
                </label>
            </div>
        </div>

        <?php if ($item['address'])
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS'); ?> &nbsp;</label>
                </div>
                <div class="col-xs-4">
                    <label class="control-label"><?php echo nl2br($item['address']); ?></label>
                </div>
            </div>
        <?php } ?>

        <?php if ($item['phone'])
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PHONE'); ?> &nbsp;</label>
                </div>
                <div class="col-xs-4">
                    <label class="control-label"><?php echo $item['phone']; ?></label>
                </div>
            </div>
        <?php } ?>

        <div id="discount_setup_container">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISCOUNT'); ?>
                        <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-xs-4">
                    <table class="table table-bordered" style="margin:0">
                        <thead>
                        <tr>
                            <td style="text-align:center">Purchase Amount</td>
                            <td style="text-align:center; width:30%">Discount (%)</td>
                            <td style="width:1%">&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index = 0;
                        if ($item['discount'])
                        {
                            foreach ($item['discount'] as $amount => $discount)
                            {
                                ?>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control float_type_positive price_unit_tk amount_purchase" name="item[discount][<?php echo $index; ?>][amount]" value="<?php echo $amount; ?>"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control float_type_positive price_unit_tk discount_percentage" name="item[discount][<?php echo $index; ?>][discount_percentage]" value="<?php echo $discount; ?>"/>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                    </td>
                                </tr>
                                <?php
                                $index++;
                            }
                        }
                        else
                        {
                            ?>
                            <tr>
                                <td>
                                    <input type="text" class="form-control float_type_positive price_unit_tk amount_purchase" name="item[discount][<?php echo $index; ?>][amount]"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control float_type_positive price_unit_tk discount_percentage" name="item[discount][<?php echo $index; ?>][discount_percentage]"/>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                </td>
                            </tr>
                            <?php
                            $index++;
                        }
                        ?>
                        <tr class="outlet-addMore">
                            <td colspan="3">
                                <button type="button" class="btn btn-warning btn-sm system_button_add_more pull-right" data-current-id="<?php echo $index; ?>"><?php echo $CI->lang->line('LABEL_ADD_MORE'); ?></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS') ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <textarea id="remarks" name="item[remarks]" class="form-control" rows="4"><?php echo $item['remarks']; ?></textarea>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>

<div id="system_content_add_more" style="display:none;">
    <table>
        <tbody>
        <tr>
            <td>
                <input type="text" class="form-control float_type_positive price_unit_tk amount_purchase"/>
            </td>
            <td>
                <input type="text" class="form-control float_type_positive price_unit_tk discount_percentage"/>
            </td>
            <td style="width:1%">
                <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events();
        system_preset({controller: '<?php echo $CI->router->class; ?>'});

        $(document).on("click", ".system_button_add_more", function (event) {
            var current_id = parseInt($(this).attr('data-current-id'));
            $(this).attr('data-current-id', current_id + 1);

            var content_id = '#system_content_add_more table tbody';
            $(content_id + ' .amount_purchase').attr('name', 'item[discount][' + current_id + '][amount]');
            $(content_id + ' .discount_percentage').attr('name', 'item[discount][' + current_id + '][discount_percentage]');

            var html = $(content_id).html();
            $("#discount_setup_container tbody tr.outlet-addMore").before(html);
        });

        $(document).on("click", ".system_button_add_delete", function (event) {
            $(this).closest('tr').remove();
        });
    });
</script>
