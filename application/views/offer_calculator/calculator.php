<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url)
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            Items
        </div>
        <div class="clearfix"></div>
    </div>
    <div style="" class="row show-grid" id="crop_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-4">
            <select id="crop_id" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            </select>
        </div>
    </div>
    <div style="display: none;" class="row show-grid" id="variety_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-4">
            <select id="variety_id" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            </select>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo 'Variety '.$CI->lang->line('LABEL_BARCODE');?></label>
        </div>
        <div class="col-sm-4 col-xs-4">
            <input type="text" id="variety_barcode" class="form-control" value=""/>
        </div>
        <div class="col-sm-4 col-xs-4">
            <div class="action_button">
                <button id="button_action_variety_add" type="button" class="btn"><?php echo $CI->lang->line('LABEL_ACTION1');?></button>
            </div>
        </div>
    </div>
    <div style="overflow-x: auto;" class="row show-grid" id="container_sale_items">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></th>
                <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_SIZE_NAME'); ?></th>
                <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PRICE_PER_PACK'); ?></th>
                <th style="min-width: 100px;">Minimum Offer Quantity (kg)</th>
                <th style="min-width: 100px;">Per kg Offer Amount</th>
                <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY'); ?>(Packets)</th>
                <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></th>
                <th style="min-width: 100px;">Price</th>
                <th style="min-width: 100px;">Offer</th>
                <th style="min-width: 150px;"><?php echo $CI->lang->line('ACTION'); ?></th>
            </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
            <tr>
                <td colspan="6">&nbsp;</td>
                <td><label><?php echo $CI->lang->line('LABEL_TOTAL'); ?></label></td>
                <td class="text-right"><label id="total_quantity">0</label></td>
                <td class="text-right"><label id="total_weight_kg">0.000</label></td>
                <td class="text-right"><label id="total_price">0.00</label></td>
                <td class="text-right"><label id="total_offer">0.00</label></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td colspan="2">
                    <select id="outlet_id" class="form-control">
                        <option value="">Select Outlet</option>
                        <?php
                        foreach($outlets as $outlet)
                        {?>
                            <option value="<?php echo $outlet['value']?>"><?php echo $outlet['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
                <td colspan="2">
                    <select id="farmer_type_id" class="form-control">
                        <option value="">Select Dealer type</option>
                        <?php
                        foreach($farmer_types as $row)
                        {?>
                            <option value="<?php echo $row['value']?>"><?php echo $row['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
                <td colspan="2">
                    <div id="farmer_id_container" style="display: none">
                        <select id="farmer_id" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </td>
                <td class="text-right" colspan="2"><label>Offer Balance</label></td>
                <td class="text-right"><label id="offer_balance">0.00</label></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="8">&nbsp;</td>
                <td class="text-right" colspan="2"><label>New Offer Balance</label></td>
                <td class="text-right"><label id="offer_balance_new">0.00</label></td>
                <td>&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <label class="crop_name">&nbsp;</label>
            </td>
            <td>
                <label class="crop_type_name">&nbsp;</label>
            </td>
            <td>
                <label class="variety_name">&nbsp;</label>
            </td>
            <td>
                <label class="pack_size">&nbsp;</label>
            </td>
            <td class="text-right">
                <label class="price_unit_pack_label">&nbsp;</label>
            </td>
            <td class="text-right">
                <label class="quantity_minimum">&nbsp;</label>
            </td>
            <td class="text-right">
                <label class="amount_per_kg">&nbsp;</label>
            </td>
            <td class="text-right">
                <input type="text"class="form-control text-right quantity integer_type_positive" value="1"/>
            </td>
            <td class="text-right">
                <label class="weight_kg">&nbsp;</label>
            </td>
            <td class="text-right">
                <label class="price">&nbsp;</label>
            </td>
            <td class="text-right">
                <label class="offer">&nbsp;</label>
            </td>
            <td><button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('ACTION_DELETE'); ?></button></td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    <?php
    if(sizeof($sale_varieties_info)>0)
    {
        ?>
        var sale_varieties_info=JSON.parse('<?php echo json_encode($sale_varieties_info);?>');
        <?php
    }
    else
    {
        ?>
        var sale_varieties_info={};
        <?php
    }
    ?>
    function calculate_sale_total()
    {
        var total_quantity=0;
        var total_weight_kg=0;
        var total_price=0;
        var total_offer=0;
        $("#container_sale_items tbody .quantity").each( function( index, element )
        {
            var variety_barcode=$(this).attr('id');
            variety_barcode=variety_barcode.substr(9);
            var quantity=0;
            if($(this).val()==parseFloat($(this).val()))
            {
                quantity=parseFloat($(this).val());
            }
            total_quantity+=quantity;

            var weight_kg=quantity*sale_varieties_info[variety_barcode]['pack_size']/1000;
            total_weight_kg+=weight_kg;
            $('#'+'weight_kg_'+variety_barcode).html(number_format(weight_kg,3,'.',''));

            var price=quantity*sale_varieties_info[variety_barcode]['price_unit_pack'];
            total_price+=price;
            $('#'+'price_'+variety_barcode).html(number_format(price,2));
            var offer=0;
            if((sale_varieties_info[variety_barcode]['quantity_minimum']>0) && (sale_varieties_info[variety_barcode]['quantity_minimum']<=weight_kg))
            {
                offer=weight_kg*sale_varieties_info[variety_barcode]['amount_per_kg'];
                $('#'+'offer_'+variety_barcode).html(number_format(offer,2));

            }
            else
            {
                offer=0;
                $('#'+'offer_'+variety_barcode).html('');
            }
            total_offer+=offer;
        });
        $('#total_quantity').html(number_format(total_quantity,'0','.',''));
        $('#total_weight_kg').html(number_format(total_weight_kg,3,'.',''));
        $('#total_price').html(number_format(total_price,2));
        $('#total_offer').html(number_format(total_offer,2));
        var offer_balance = parseFloat($('#offer_balance').html().replace(/,/g,''));
        $('#offer_balance_new').html(number_format(total_offer+offer_balance,2));


    }
    function add_variety()
    {
        var scanned_code=$('#variety_barcode').val();
        var outlet_id='000';
        if(scanned_code.length!=8)//validation of barcode length
        {
            animate_message("Invalid Barcode length.");
            return;
        }
        var variety_barcode=outlet_id.concat(scanned_code.substr(3));
        if(sale_varieties_info[variety_barcode]===undefined)
        {
            animate_message("Invalid Product.");
        }
        else
        {
            if(($('#'+'quantity_'+variety_barcode).length)>0)
            {
                var cur_quantity=parseFloat($('#'+'quantity_'+variety_barcode).val());
                cur_quantity=cur_quantity+1;
                $('#'+'quantity_'+variety_barcode).val(cur_quantity);
            }
            else
            {
                var content_id='#system_content_add_more table tbody';
                $(content_id+' .crop_name').html(sale_varieties_info[variety_barcode]['crop_name']);
                $(content_id+' .crop_type_name').html(sale_varieties_info[variety_barcode]['crop_type_name']);
                $(content_id+' .variety_name').html(sale_varieties_info[variety_barcode]['variety_name']);
                $(content_id+' .pack_size').html(sale_varieties_info[variety_barcode]['pack_size']);
                $(content_id+' .price_unit_pack_label').html(sale_varieties_info[variety_barcode]['price_unit_pack']);
                $(content_id+' .quantity_minimum').html(sale_varieties_info[variety_barcode]['quantity_minimum']);
                $(content_id+' .amount_per_kg').html(sale_varieties_info[variety_barcode]['amount_per_kg']);

                $(content_id+' .quantity').attr('id','quantity_'+variety_barcode);
                $(content_id+' .weight_kg').attr('id','weight_kg_'+variety_barcode);
                $(content_id+' .price').attr('id','price_'+variety_barcode);
                $(content_id+' .offer').attr('id','offer_'+variety_barcode);

                var html=$(content_id).html();
                $("#container_sale_items tbody").append(html);

                $(content_id+' .quantity').removeAttr('id');
                $(content_id+' .weight_kg').removeAttr('id');
                $(content_id+' .price').removeAttr('id');
                $(content_id+' .offer').removeAttr('id');
            }
            calculate_sale_total();
            $('#variety_barcode').val('');
        }

    }
    function add_farmer_offer_balance(offer_balance)
    {
        var invoice_offer = parseFloat($('#total_offer').html().replace(/,/g,''));
        $('#offer_balance').html(number_format(offer_balance,2));
        $('#offer_balance_new').html(number_format(invoice_offer+parseFloat(offer_balance),2));
    }
    jQuery(document).ready(function()
    {
        $('#crop_id').html(get_dropdown_with_select(system_crops));
        $(document).off('change','#crop_id');
        $(document).on("change","#crop_id",function()
        {

            $('#variety_id').val('');
            $('#variety_barcode').val('');
            var crop_id=$('#crop_id').val();
            $('#variety_id_container').hide();
            if(crop_id>0)
            {
                var items=[];
                $.each( sale_varieties_info, function( key, value )
                {
                    if(value['crop_id']==crop_id)
                    {
                        items.push({'value':key, 'text':value['variety_name'].concat('- ',value['pack_size'],'gm')})
                    }

                });
                if(items.length>0)
                {
                    $('#variety_id_container').show();
                    $('#variety_id').html(get_dropdown_with_select(items));
                }

            }
        });
        $(document).off('change','#variety_id');
        $(document).on("change","#variety_id",function()
        {
            $('#variety_barcode').val($('#variety_id').val());

        });
        $(document).off("keypress", "#variety_barcode");
        $(document).on("keypress","#variety_barcode",function(event)
        {
            if(event.which == 13)
            {
                add_variety();
                return false;
            }

        });
        $(document).off("click", "#button_action_variety_add");
        $(document).on("click", "#button_action_variety_add", function(event)
        {
            add_variety();
        });
        $(document).off("click", ".system_button_add_delete");
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
            calculate_sale_total();

        });
        $(document).off("input", ".quantity");
        $(document).on("input", ".quantity", function(event)
        {
            calculate_sale_total();
        });
        $(document).off("change", "#outlet_id");
        $(document).on("change","#outlet_id",function()
        {
            $("#farmer_type_id").val("");
            $("#farmer_id").val("");
            $('#farmer_id_container').hide();
            add_farmer_offer_balance(0);
        });
        $(document).off("change", "#farmer_type_id");
        $(document).on("change","#farmer_type_id",function()
        {
            $("#farmer_id").val("");
            var farmer_type_id=$('#farmer_type_id').val();
            var outlet_id=$('#outlet_id').val();
            if((outlet_id>0)&&farmer_type_id>0)
            {
                $('#farmer_id_container').show();
                $.ajax({
                    url:'<?php echo site_url($CI->controller_url.'/get_dropdown_farmers_by_outlet_farmer_type_id') ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{outlet_id:outlet_id,farmer_type_id:farmer_type_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });

            }
            else
            {
                $('#farmer_id_container').hide();

            }
            add_farmer_offer_balance(0);
        });
        $(document).off("change", "#farmer_id");
        $(document).on("change","#farmer_id",function()
        {
            var farmer_id=$('#farmer_id').val();
            var offer_balance=0;
            if(farmer_id!='')
            {
                var id_offer_balance = farmer_id.split("/");
                offer_balance=id_offer_balance[1];
            }
            add_farmer_offer_balance(offer_balance);
        });

    });
</script>