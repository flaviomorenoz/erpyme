var Igv = 10;

function exponer(deb){
    spositems = JSON.parse(get('spositems'));
    $.each(spositems, function () {
        this.row.price              = this.row.price * (1 - (deb/100));
        this.row.real_unit_price    = this.row.price;
    });
    store('spositems', JSON.stringify(spositems));
    loadItems();
}

function add_invoice_item(item) {
    
    if (count == 1) {
        spositems = {};
    }
    if (item == null) {
        return;
    }

    var item_id = Settings.item_addition == 1 ? item.item_id : item.id;
    //console.log("Corcega")
    //console.log(item_id)
    if (spositems[item_id]) {
        spositems[item_id].row.qty = parseFloat(spositems[item_id].row.qty) + 1;
    } else {
        spositems[item_id] = item;
    }

    store('spositems', JSON.stringify(spositems));
    
    loadItems();
    return true;
}

function pausecomp(millis)
{
    var date = new Date();
    var curDate = null;
    do { curDate = new Date(); }
    while(curDate-date < millis);
}

var ar_prec = [] // arreglo de precios

function cambiar_precios2(){
    var all = document.getElementsByTagName("*");
    var nombri = ""
    var cocx = ""
    for (var i=0, max=all.length; i < max; i++) {
        // Do something with the element here
        if(all[i].name == "real_unit_price[]"){
            //console.log(all[i].name)
            //all[i].value = ar_prec[?]
        }
    }
}

function loadItems() {
    console.log("Inicio loadItems")
    if (count == 1) {
        spositems = {};
    }
    if (get('spositems')) {
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;

        $("#posTable tbody").empty();
        var time = ((new Date).getTime())/1000;
        if (Settings.remote_printing != 1) {

            var style = '<style>p{margin:0} .bb td, .bb th { border-bottom: 1px solid #DDD; }</style>';
            var pos_head = `
                <p style="text-align: center;text-transform: uppercase;">
                    <strong>${Store.name}</strong><br>
                    ${Store.address2}<br>
                    ${Store.address1}<br>
                    ${Store.state +' - '+Store.city}<br>
                    ${'RUC : '+Store.code}<br>
                    ${'TLF : '+Store.phone}
                </p>
            `;
            var pos_customer =  ($('#select2-spos_customer-container').text()).split("-");
            var pos_curr_time = `
                <div style="text-transform: uppercase;">
                    <span>${date(Settings.dateformat+' '+Settings.timeformat, time)}</span>
                </div>
                <div style="text-transform: uppercase; border-bottom: 1px dashed;">
                    ${lang.sale_no_ref + ' : '+ pos_customer[0] }
                    <br>
                    ${lang.ccf1 + ' : '} ${ $('#spos_customer').val() === '2' ? '': pos_customer[1] }
                </div>
            `;
            // var pos_customer = 'C: '+$('#select2-spos_customer-container').text()+ "\n";
            // var hr = 'R: '+$('#hold_ref').val()+ "\n";
            // var user = 'U: '+username+ "\n";
            // var pos_curr_time = 'T: '+date(Settings.dateformat+' '+Settings.timeformat, time)+ "\n";
            
            // var ob_info = pos_customer+hr+user+pos_curr_time+ "\n";
            var ob_info = style + pos_customer + pos_curr_time + "\n";
            order_data.info = ob_info;
            bill_data.info = ob_info;
            var o_items = '';
            var b_items = '';

        } else {
            $("#order_span").empty(); $("#bill_span").empty();
            var style = '<style>p{margin:0}; .bb td, .bb th { border-bottom: 1px solid #DDD; }</style>';
            var pos_head = `
                <p style="text-align: center;text-transform: uppercase;">
                    <strong>${Store.name}</strong><br>
                    ${Store.address2}<br>
                    ${Store.address1}<br>
                    ${Store.state +' - '+Store.city}<br>
                    ${'RUC : '+Store.code}<br>
                    ${'TLF : '+Store.phone}
                </p>
            `;
            var pos_customer =  ($('#select2-spos_customer-container').text()).split("-");
            var pos_curr_time = `
                <div style="text-transform: uppercase;">
                    <span>${date(Settings.dateformat+' '+Settings.timeformat, time)}</span>
                </div>
                <div style="text-transform: uppercase; border-bottom: 1px dashed;">
                    ${lang.sale_no_ref + ' : '+ pos_customer[0] }
                    <br>
                    ${lang.ccf1 + ' : '} ${ $('#spos_customer').val() === '2' ? '': pos_customer[1] }
                </div>
            `;

            // var pos_customer = '<h5>Cs: '+$('#select2-spos_customer-container').text()+'</h5>';
            var hr = '<h5>R: '+$('#hold_ref').val()+'</h5>';
            var user = '<h5>U: '+username+'</h5>';

            $("#order_span").prepend(
                style+pos_head+
                `<p style="text-transform: uppercase; text-align: center; padding: 0.5rem;border-top: 1px dashed; border-bottom: 1px dashed;">
                    Boleta de Orden Electrónica
                </p>` +
                pos_curr_time
                // pos_customer+
                // hr+
                // user
            );

            $("#bill_span").prepend(
                style+pos_head+
                `<p style="text-transform: uppercase; text-align: center; padding: 0.5rem;border-top: 1px dashed; border-bottom: 1px dashed;">
                    ${lang.blv}
                </p>` +
                pos_curr_time
                // pos_customer+
                // hr+
                // user
            );
            $("#order-table").empty(); $("#bill-table").empty();
        }
        spositems = JSON.parse(get('spositems'));

        //console.log(spositems)
        $.each(spositems, function () {

            var item = this;
            
            var item_id = Settings.item_addition == 1 ? item.item_id : item.id;
            
            spositems[item_id] = item;

            //console.log("item_id  :  " + item_id)

            //var product_id = item.row.id, 
            var product_id = item_id
            var item_type = item.row.type, item_tax_method = parseFloat(item.row.tax_method), combo_items = item.combo_items, item_qty = item.row.qty, item_aqty = parseFloat(item.row.quantity), item_type = item.row.type, item_ds = item.row.discount, item_code = item.row.code, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
            
            var unit_price = parseFloat(item.row.real_unit_price);
            var net_price = unit_price;
            var item_comment = item.row.comment;
            var item_was_ordered = item.row.ordered ? item.row.ordered : 0;

            var ds = item_ds ? item_ds : '0';
            var item_discount = formatDecimal(ds);
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = formatDecimal((parseFloat(((net_price) * parseFloat(pds[0])) / 100)), 4);
                }
            }
            product_discount += formatDecimal((item_discount * item_qty), 4);
            net_price = formatDecimal((net_price-item_discount), 4);

            var pr_tax = parseInt(item.row.tax), pr_tax_val = 0;
            if (pr_tax !== null && pr_tax != 0) {
                if(item_tax_method == 0) {
                    pr_tax_val = formatDecimal(((net_price * parseFloat(pr_tax)) / (100+parseFloat(pr_tax))), 4);
                    net_price -= pr_tax_val;
                    tax = lang.inclusive;
                } else {
                    pr_tax_val = formatDecimal(((net_price * parseFloat(pr_tax)) / 100), 4);
                    tax = lang.exclusive;
                }
            }

            product_tax += formatDecimal((pr_tax_val * item_qty), 4);

            var row_no = (new Date).getTime();
            var newTr = $('<tr id="' + row_no + '" class="' + item_id + '" data-item-id="' + item_id + '" data-id="' + item.row.id + '"></tr>');
            
            
            // Product_id
            /*
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '">' +
                '<input name="item_comment[]" type="hidden" class="ritem_comment" value="' + item_comment + '">' +
                '<input name="product_code[]" type="hidden" value="' + item.row.code + '">' +
                '<input name="product_name[]" type="hidden" value="' + item.row.name + '">' +
                '<button type="button" class="btn bg-purple btn-block btn-xs edit" id="' + row_no + '" data-item="' + item_id + '">' +
                '<span class="sname" id="name_' + row_no + '">' + item_name + ' (' + item_code + ')</span></button></td>';
            */

            // Name producto
            tr_html = '<td><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '">' +
                '<input name="item_comment[]" type="hidden" class="ritem_comment" value="' + item_comment + '">' +
                '<input name="product_code[]" type="hidden" value="' + item.row.code + '">' +
                '<input name="product_name[]" type="hidden" value="' + item.row.name + '">' +
                '<span class="sname" id="name_' + row_no + '">' + item_name + ' (' + item_code + ')</span></td>';
            
            // Real_unit_price
            tr_html += '<td class="text-right">'+
                '<input class="realuprice" name="real_unit_price[]" type="hidden" value="' + item.row.real_unit_price + '">'+
                '<input class="rdiscount" name="product_discount[]" type="hidden" id="discount_' + row_no + '" value="' + ds + '">'+
                '<span class="text-right sprice" id="sprice_' + row_no + '">' + formatMoney(parseFloat(net_price) + parseFloat(pr_tax_val)) + '</span></td>';
            
            // Quantity
            tr_html += '<td><input name="item_was_ordered[]" type="hidden" class="riwo" value="' + item_was_ordered + '">'+
                '<input class="form-control input-qty kb-pad text-center rquantity" name="quantity[]" type="text" value="' + formatDecimal(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
            
            // Subtotal
            tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(((parseFloat(net_price) + parseFloat(pr_tax_val)) * parseFloat(item_qty))) + '</span></td>';
            
            // Trash
            tr_html += '<td class="text-center"><i class="fa fa-trash-o tip pointer posdel" id="' + row_no + '" title="Remove"></i></td>';
            
            // "Con Igv" :
            //tr_html += '<td style="color:red">' + parseFloat(net_price) + '</td>'

            newTr.html(tr_html);
            newTr.prependTo("#posTable");
            // total += formatDecimal(((parseFloat(net_price) + parseFloat(pr_tax_val)) * parseFloat(item_qty)), 4);
            total += formatDecimal(((parseFloat(net_price)) * parseFloat(item_qty)), 4);
            count += parseFloat(item_qty);
            an++;
            // $('#list-table-div').scrollTop(0);
            var oitb = $('#list-table-div')[0].scrollHeight;
            $('#list-table-div').slimScroll({scrollTo: oitb});
            if(item_type == 'standard' && item_qty > item_aqty) {
                $('#' + row_no).addClass('danger');
                $('#' + row_no).find('.edit').removeClass('bg-purple').addClass('btn-warning');
            }
            else if (item_type == 'combo') {
                if(combo_items === false) {
                    $('#' + row_no).addClass('danger');
                } else {
                    $.each(combo_items, function(){
                        if(parseFloat(this.quantity) < (parseFloat(this.qty)*item_qty)) {
                            $('#' + row_no).addClass('danger');
                            $('#' + row_no).find('.edit').removeClass('bg-purple').addClass('btn-warning');
                        }
                    });
                }
            }

            var comments = item_comment ? item_comment.split(/\r?\n/g) : [];
            if (Settings.remote_printing != 1) {

                b_items += "#"+(an-1)+" "+ item_name + " (" + item_code + ")" + "\n";
                for (var i = 0, len = comments.length; i < len; i++) {
                    b_items += (comments[i].length > 0 ? "   * "+comments[i]+"\n" : "");
                }
                b_items += printLine(item_qty + " x " + formatMoney(parseFloat(net_price) + parseFloat(pr_tax_val))+": "+ formatMoney(((parseFloat(net_price) + parseFloat(pr_tax_val)) * parseFloat(item_qty)))) + "\n";
                o_items += printLine("#"+(an-1)+" "+ item_name + " (" + item_code + "): [ "+ (item_was_ordered != 0 ? 'xxxx' : item_qty)) + " ]\n";
                for (var i = 0, len = comments.length; i < len; i++) {
                    o_items += (comments[i].length > 0 ? "   * "+comments[i]+"\n" : "");
                }
                o_items += "\n";

            } else {

                // ****** Llenando antigua Tabla Bill ******
                /*var bprTr = '<tr class="row_' + item_id + '" data-item-id="' + item_id + '"> <td>'+(item_id == 0 ? item.row.name :item_name );
                bprTr += '<br><span style="padding-left: 3rem;">'+item_qty + ' x ' + formatMoney(parseFloat(net_price) + parseFloat(pr_tax_val))+'</span></td>';
                for (var i = 0, len = comments.length; i < len; i++) {
                    bprTr += (comments[i] ? '<br> <b>*</b> <small>'+comments[i]+'</small>' : '');
                }
                bprTr += '<td class="text-right"><br>'+formatMoney(((parseFloat(net_price) + parseFloat(pr_tax_val)) * parseFloat(item_qty))) +'</td>';
                bprTr += '</tr>';*/
                

                // ****** Llenando Tabla Orden ******
                var oprTr = '<tr class="bb row_' + item_id + '" data-item-id="' + item_id + '"><td>#'+(an-1)+' ' + item_name + ' (' + item_code + ')';
                for (var i = 0, len = comments.length; i < len; i++) {
                    oprTr += (comments[i] ? '<br> <b>*</b> <small>'+comments[i]+'</small>' : '');
                }
                oprTr += '</td><td>[ '+(item_was_ordered != 0 ? 'xxxx' : item_qty)+' ]</td></tr>';
                $("#order-table").append(oprTr);
            }
        });

        var ds = get('spos_discount') ? get('spos_discount') :
        ($('#discount_val').val() ? $('#discount_val').val() : '0');
        order_discount = parseFloat(ds);
        if(ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            order_discount = parseFloat((total*parseFloat(pds[0]))/100);
        }

        var ts = get('spos_tax') ? get('spos_tax') : ($('#tax_val').val() ? $('#tax_val').val() : '0');
        order_tax = parseFloat(ts);
        //console.log("order_tax:"+order_tax)

        //if(ts.indexOf("%") !== -1) {
            var pts = ts.split("%");
            order_tax = ((total-order_discount)*parseFloat(pts[0]))/100;
            //console.log("order_tax despues del calculo:"+order_tax)
        //}

        var g_total = total - parseFloat(order_discount) + parseFloat(order_tax);
        grand_total = formatMoney(g_total);

        $("#ds_con").text('('+formatMoney(product_discount)+') '+formatMoney(order_discount));
        $("#ts_con").text(formatMoney(order_tax));
        $("#total-payable").text(grand_total);
        $("#total").text(formatMoney(total));
        $("#count").text((an-1)+' ('+(count-1)+')');

        if (Settings.remote_printing != 1) {

            order_data.items = o_items;
            bill_data.items = b_items;
            var b_totals = '';
            b_totals += printLine(lang.total+': '+ formatMoney(total)) +"\n";
            if(order_discount > 0 || product_discount > 0) {
                b_totals += printLine(lang.discount+': '+ formatMoney(order_discount+product_discount)) +"\n";
            }
            if (order_tax != 0) {
                b_totals += printLine(lang.order_tax+': '+ formatMoney(order_tax)) +"\n";
            }
            b_totals += printLine(lang.grand_total+': '+ formatMoney(g_total)) +"\n";
            if(Settings.rounding != 0) {
                round_total = roundNumber(g_total, parseInt(Settings.rounding));
                var rounding = formatDecimal((round_total - g_total), 4);
                b_totals += printLine(lang.rounding+': '+ formatMoney(rounding)) +"\n";
                b_totals += printLine(lang.total_payable+': '+ formatMoney(round_total)) +"\n";
            }
            b_totals += "\n"+ lang.total_items+': '+ (an - 1) + ' (' + (parseFloat(count) - 1) + ')' +"\n";
            bill_data.totals = b_totals;

        } else {
            var bill_totals = '';

            bill_totals = `
            <tr style="border-top: 1px dashed;">
                <td>OP. EXONERADAS</td>
                <td class="text-right">${formatMoney(0.00)}</td>
            </tr>
            <tr>
                <td style="text-align: left">OP. INAFECTAS</td>
                <td class="text-right">${formatMoney(0.00)}</td>
            </tr>
            <tr>
                <td style="text-align: left" >${lang.total}</td>
                <td class="text-right">${formatMoney(total)}</td>
            </tr>
            <tr>
                <td style="text-align: left" >OP. GRATUITAS</td>
                <td class="text-right">${formatMoney(0.00)}</td>
            </tr>
            <tr>
                <td style="text-align: left" >I.S.C</td>
                <td class="text-right">${formatMoney(0.00)}</td>
            </tr>
            <tr>
                <td style="text-align: left">OTROS CARGOS</td>
                <td class="text-right">${formatMoney(0.00)}</td>
            </tr> `;

            if(order_discount > 0 || product_discount > 0){
                bill_totals += `<tr><td style="text-align: left">${lang.discount}</td><td  class="text-right">${formatMoney(order_discount+product_discount)}</td></tr>`;
            }
            if (order_tax != 0) {
                bill_totals += `<tr><td style="text-align: left">${lang.order_tax}</td><td  class="text-right">${formatMoney(order_tax)}</td></tr>`;
            }
            bill_totals += ` <tr style="border-bottom: 1px dashed;">
                <td style="text-align: left">${lang.grand_total}</td>
                <td class="text-right">${formatMoney(g_total)}</td>
            </tr>`;

            // bill_totals += '<tr class="bb"><td>'+lang.total_items+'</td><td style="text-align:right;">'+(an - 1) + ' (' + (parseFloat(count) - 1) + ')</td></tr>';
            // bill_totals += '<tr class="bb"><td>'+lang.total+'</td><td style="text-align:right;">'+formatMoney(total)+'</td></tr>';
            // if(order_discount > 0 || product_discount > 0) {
                // bill_totals += '<tr class="bb"><td>'+lang.discount+'</td><td style="text-align:right;">'+formatMoney(order_discount+product_discount)+'</td></tr>';
            // }
            // if (order_tax != 0) {
                // bill_totals += '<tr class="bb"><td>'+lang.order_tax+'</td><td style="text-align:right;">'+formatMoney(order_tax)+'</td></tr>';
            // }

            // bill_totals += '<tr class="bb"><td>'+lang.grand_total+'</td><td style="text-align:right;">'+formatMoney(g_total)+'</td></tr>';
            
            if(Settings.rounding != 0) {
                round_total = roundNumber(g_total, parseInt(Settings.rounding));
                var rounding = formatDecimal((round_total - g_total), 4);
                bill_totals += '<tr class="bb"><td>'+lang.rounding+'</td><td style="text-align:right;">'+formatMoney(rounding)+'</td></tr>';
                bill_totals += '<tr class="bb"><td>'+lang.total_payable+'</td><td style="text-align:right;">'+formatMoney(round_total)+'</td></tr>';
            }
            // bill_totals += '<tr class="bb"><td colspan="2" style="text-align:center;">'+lang.merchant_copy+'</td></tr>';
            $('#bill-total-table').empty();
            $('#bill-total-table').append(bill_totals);
        }

        if (Settings.display_kb == 1) { display_keyboards(); }
        $('#add_item').focus();
    }
}

function chr(i) {
    return String.fromCharCode(i);
}

$(document).ready(function () {

    $(document).on('click', '.no-results, #filter-suspended-sales', function (e) {
        e.preventDefault();
        e.stopPropagation();
    });

    $('#susModal').on('shown.bs.modal', function (e) {
        $('#reference_note').focus();
    })

    $('#filter-categories').hideseek({
        nodata: lang.no_match_found
    });

    $(document).on('click', '.suspended_sales .dropdown-menu .header', function (e) {
        e.stopPropagation();
    });

    $('#filter-suspended-sales').hideseek({
        nodata: lang.no_match_found
    });

    $('#suspended_sales').on('shown.bs.dropdown', function () {
        $('#filter-suspended-sales').focus();
    });

    $(document).on('click', '#update-note', function(){
        var n = $('#snote').val();
        store('spos_note', n);
        $('#note').val(n);
        $('#noteModal').modal('hide');
    });
    /* =============================
    Edit Item Modal
    ============================= */

    $("#posTable").on("click", '.edit', function() {
        var row = $(this).closest('tr');
        var id = row.attr('id');
        var item_id = row.attr('data-item-id');
        var item_rid = row.attr('data-id');
        var item = spositems[item_id];
        //var unit_price = parseFloat(item.row.real_unit_price);
        var unit_price = formatDecimal(row.find('.realuprice').val());
        var net_price = unit_price;
        var ds = item.row.discount ? item.row.discount : '0';
        item_discount = formatDecimal(parseFloat(ds));
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = formatDecimal((net_price * parseFloat(pds[0])) / 100);
            }
        }
        net_price -= item_discount;
        var pr_tax = parseFloat(item.row.tax), pr_tax_val = 0, tax = '';
        if (pr_tax !== null && pr_tax != 0) {
            if(parseFloat(item.row.tax_method) == 0) {
                pr_tax_val = formatDecimal((net_price * parseFloat(pr_tax)) / (100+parseFloat(pr_tax)));
                net_price -= pr_tax_val;
                tax = lang.inclusive;
            } else {
                pr_tax_val = formatDecimal((net_price * parseFloat(pr_tax)) / 100);
                tax = lang.exclusive;
            }
        }
        //console.log(item_rid, item_id, id);
        $('#proModalLabel').html('<a href="'+base_url+'/products/view/'+item_rid+'" data-toggle="ajax">'+item.label+'</a>');
        $('#net_price').text(formatMoney(net_price));
        $('#pro_tax').text(formatMoney(pr_tax_val));
        $('#pro_tax_method').text('('+tax+')');
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#nPrice').val(unit_price);
        $('#nQuantity').val(item.row.qty);
        $('#nDiscount').val(ds);
        $('#nComment').val(item.row.comment);
        $('#proModal').modal({backdrop:'static'});
    });

    $(document).on('change', '#nPrice, #nDiscount', function () {
        var item_id = $('#item_id').val();
        var unit_price = parseFloat($('#nPrice').val());
        var net_price = unit_price;
        var item = spositems[item_id];
        var ds = $('#nDiscount').val() ? $('#nDiscount').val() : '0';
        item_discount = formatDecimal(parseFloat(ds));
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = formatDecimal((unit_price * parseFloat(pds[0])) / 100);
            }
        }
        net_price -= item_discount;
        var pr_tax = parseFloat(item.row.tax), pr_tax_val = 0;
        if (pr_tax !== null && pr_tax != 0) {
            if(parseFloat(item.row.tax_method) == 0) {
                pr_tax_val = formatDecimal((net_price * parseFloat(pr_tax)) / (100+parseFloat(pr_tax)));
                net_price -= pr_tax_val;
                tax = lang.inclusive;
            } else {
                pr_tax_val = formatDecimal((net_price * parseFloat(pr_tax)) / 100);
                tax = lang.exclusive;
            }
        }

        $('#net_price').text(formatMoney(net_price));
        $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    /* =============================
    Edit Item Method
    ============================= */
    $(document).on('click', '#editItem', function () {
        var item_id = $('#item_id').val();
        var price = parseFloat($('#nPrice').val());
        if(!is_valid_discount($('#nDiscount').val())) {
            bootbox.alert(lang.unexpected_value);
            return false;
        }
        spositems[item_id].row.qty = parseFloat($('#nQuantity').val()),
        spositems[item_id].row.real_unit_price = price,
        spositems[item_id].row.comment = $('#nComment').val(),
        spositems[item_id].row.discount = $('#nDiscount').val() ? $('#nDiscount').val() : '0',
        localStorage.setItem('spositems', JSON.stringify(spositems));
        $('#proModal').modal('hide');

        loadItems();
        return;
    });

    /* =============================
    Row quantity change
    ============================= */
    $(document).on("change", '.rquantity', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || $(this).val() == 0) {
            loadItems();
            bootbox.alert(lang.unexpected_value);
            return false;
        }
        var new_qty = parseFloat($(this).val()),
        item_id = row.attr('data-item-id');
        spositems[item_id].row.qty = new_qty;
        localStorage.setItem('spositems', JSON.stringify(spositems));
        loadItems();
    });

    $('#reset').click(function (e) {
        if (count <= 1) {
            return false;
        }
        if(protect_delete == 1) {
            var boxd = bootbox.dialog({
                title: lang.enter_pin_code,
                closeButton: true,
                message: '<input id="pos_pin" name="pos_pin" type="password" placeholder="Pin Code" class="form-control kb-pad"> ',
                buttons: {
                    danger: {
                      label: lang.close,
                      className: "btn-default pull-left",
                      callback: function() { }
                  },
                  success: {
                    label: "<i class='fa fa-tick'></i> "+lang.delete,
                    className: "btn-warning verify_pin",
                    callback: function () {
                        var pos_pin = md5($('#pos_pin').val());
                        if (pos_pin == Settings.pin_code) {
                            if (get('spositems')) { remove('spositems'); }
                            if (get('spos_tax')) { remove('spos_tax'); }
                            if (get('spos_discount')) { remove('spos_discount'); }
                            if (get('spos_customer')) { remove('spos_customer'); }
                            window.location.href = base_url+"pos";
                        } else {
                            bootbox.alert(lang.wrong_pin);
                        }
                    }
                }
            }
        });
            boxd.on("shown.bs.modal", function() {
                if (Settings.display_kb == 1) { display_keyboards(); }
                $( "#pos_pin" ).focus().keypress(function(e) {
                    if (e.keyCode == 13) {
                        e.preventDefault();
                        $('.verify_pin').trigger('click');
                        return false;
                    }
                });
            });
        } else {
            bootbox.confirm(lang.r_u_sure, function (result) {
                if (result) {
                    if (get('spositems')) { remove('spositems'); }
                    if (get('spos_tax')) { remove('spos_tax'); }
                    if (get('spos_discount')) { remove('spos_discount'); }
                    if (get('spos_customer')) { remove('spos_customer'); }
                    window.location.href = base_url+"pos";
                }
            });
        }
    });

    $("#print_order").click(function(t){
        document.getElementById('id01').style.display='block'
        let d = new Date()
        $('#orden-hora').html(d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds())
/*
        console.log("Hola 1")
        if (t.preventDefault(),count <= 1){
            bootbox.alert(lang.please_add_product);
            //console.log("Bips A")
        }else if (0 == Settings.remote_printing){
            console.log("Hola 2")
            if ($("#order-data").show(),1 == Settings.print_img) {
                $("#preo").html('<pre style="background:#FFF;font-size:18px;margin:0;border:0;color:#000 !important;">' + order_data.info + order_data.items + "</pre>");
                var e = $("#order-data").get(0);
                html2canvas(e, {
                    scrollY: 0,
                    scale: 1.3
                }).then(function(t) {
                    var e = t.toDataURL().split(",")[1];
                    $.post(base_url + "pos/receipt_img", {
                        img: e,
                        spos_token: csrf_hash
                    })
                })
            } else {
                console.log("Hola 3")
                var a = $("#pos-sale-form").serialize();
                $.post(base_url + "pos/p/order", a)
            }
            //console.log("Bips B")
        }else{
            console.log("Hola 4")
            printOrder(order_data);
            //console.log("Bips C")
        }
        return setTimeout(function() {
            $("#order-data").hide()
        }, 500),
        !1
*/
    }),
/*    
    $("#print_bill").click(function(t) {
        if (t.preventDefault(),
        count <= 1)
            bootbox.alert(lang.please_add_product);
        else if (0 == Settings.remote_printing)
            if ($("#bill-data").show(),
            1 == Settings.print_img) {
                $("#preb").html('<pre style="background:#FFF;font-size:18px;margin:0;border:0;color:#000 !important;">' + bill_data.info + bill_data.items + "\n" + bill_data.totals + "</pre>");
                var e = $("#bill-data").get(0);
                html2canvas(e, {
                    scrollY: 0,
                    scale: 1.3
                }).then(function(t) {
                    var e = t.toDataURL().split(",")[1];
                    $.post(base_url + "pos/receipt_img", {
                        img: e,
                        spos_token: csrf_hash
                    })
                })
            } else {
                var a = $("#pos-sale-form").serialize();
                $.post(base_url + "pos/p/bill", a)
            }
        else
            printBill(bill_data);
        return setTimeout(function() {
            $("#bill-data").hide()
        }, 500),
        !1
    }),
*/
    $("#updateDiscount").click(function() {
        
        var ds = $('#get_ds').val() ? $('#get_ds').val() : '0';
                var apply_to = $('input[name=apply_to]:checked').val();
                console.log("apply_to:"+apply_to)
                if (ds.length != 0){
                    if (apply_to == 'order'){
                        $('#discount_val').val(ds);
                        
                        store('spos_discount', ds);
                        if(ds.indexOf("%") !== -1){
                            
                            var pds = ds.split("%");
                            
                            var totel       = $("#total-payable").text()
                            console.log("totel:"+totel)
                            var ar_totel    = totel.split("S/")
                            var totil       = ar_totel[1]
                            console.log("totil:"+totil)

                            // descuento
                            order_discount = (totil*parseFloat(pds[0]))/100;
                            
                            // Grand total:
                            grand_total = totil - order_discount //parseFloat(g_total);
                            
                            // Operaciones gravadas:
                            var oper_grav  = grand_total / (1 + (Igv/100))
                            console.log("oper_grav:"+oper_grav)
                            
                            // Hallando el igv
                            var el_igv = oper_grav * Igv
                            console.log("el_igv:"+el_igv)
                            
                            // no se que es
                            //order_tax =  //calTax();
                            //var g_total = total + order_tax - order_discount;
                            //$("#ds_con").text('('+formatMoney(product_discount)+') '+formatMoney(order_discount));
                            //console.log("oper_grav:" + oper_grav)
                            //console.log("el_igv:" + el_igv)

                            $("#total").text(formatMoney(oper_grav))
                            $("#ts_con").text(formatMoney(el_igv))
                            $("#total-payable").text(formatMoney(grand_total));
                            

                        }/* else {
                                order_discount = ds;
                                order_tax = calTax();
                                var g_total = (total + order_tax) - parseFloat(order_discount);
                                grand_total = parseFloat(g_total);
                                $("#ds_con").text('('+formatMoney(product_discount)+') '+formatMoney(order_discount));
                                $("#total-payable").text(formatMoney(grand_total));
                        }*/
                    } 
                    /*else if (apply_to == 'products') {
                        var items = {};
                        spositems = JSON.parse(get('spositems'));
                        $.each(spositems, function () {
                            this.row.discount = ds;
                        });
                        store('spositems', JSON.stringify(spositems));
                    }*/
                    //loadItems();
                    $('#dsModal').modal('hide');
                }
        /*    
            // Quitando el porcentaje
            var pos_p = ds.indexOf("%")
            if (pos_p >= 0){
                var nuevo = ds.replace("%","")
                console.log(nuevo)
                ds = parseFloat(nuevo)
            }

            exponer(ds)  // solo descuenta en forma porcentual
        */
        $('#dsModal').modal('hide');
    });

    $("#add_discount").click(function() {
        var dval = $('#discount_val').val();
        $('#get_ds').val(dval);
        $('#dsModal').modal({backdrop:'static'});
        return false;
    });
    $('#dsModal').on('shown.bs.modal', function () {
        $('#get_ds').focusToEnd();
    });

    $("#updateTax").click(function() {
        var ts = $('#get_ts').val();
        if (ts.length != 0) {
            $('#tax_val').val(ts);
            store('spos_tax', ts);
            if(ts.indexOf("%") !== -1) {
                var pts = ts.split("%");
                if(! isNaN(pts[0])){
                    order_tax = ((total-order_discount)*parseFloat(pts[0]))/100;
                    var g_total = (total + order_tax) - order_discount;
                    grand_total = parseFloat(g_total);
                    $("#ts_con").text(formatMoney(order_tax));
                    $("#total-payable").text(formatMoney(grand_total));
                } else {
                    $('#get_ts').val('0'); $('#tax_val').val('0');
                    var g_total = total - order_discount;
                    grand_total = parseFloat(g_total);
                    $("#ts_con").text('0');
                    $("#total-payable").text(formatMoney(grand_total));
                }
            } else {
                if(! isNaN(ts) && ts != 0){
                    order_tax = ts;
                    var g_total = (total + parseFloat(ts)) - order_discount;
                    grand_total = parseFloat(g_total);
                    $("#ts_con").text(formatMoney(order_tax));
                    $("#total-payable").text(formatMoney(grand_total));
                } else {
                    $('#get_ts').val('0'); $('#tax_val').val('0');
                    var g_total = total - order_discount;
                    grand_total = parseFloat(g_total);
                    $("#ts_con").text('0');
                    $("#total-payable").text(formatMoney(grand_total));
                }
            }
            $('#tsModal').modal('hide');
        }
    });

    $("#add_tax").click(function() {
        var tval = $('#tax_val').val();
        $('#get_ts').val(tval);
        $('#tsModal').modal({backdrop:'static'});
        return false;
    });
    $('#tsModal').on('shown.bs.modal', function () {
        $('#get_ts').focusToEnd();
    });
    $('#noteModal').on('shown.bs.modal', function () {
        $('#snote').focusToEnd();
    });

    $(document).on('click', '.product', function (e) {
        code            = $(this).val();
        let tipo        = $("#delivery").val()
        //console.log("Augurio:"+base_url+'pos/get_product/'+code+"/"+delivery)
        $.ajax({
            type: "get",
            url: base_url+'pos/get_product/'+code+"/"+tipo,
            dataType: "json",
            success: function (data) {
                if (data !== null) {
                    add_invoice_item(data);
                } else {
                    bootbox.alert(lang.no_match_found);
                }
            }
        });
    });
    
    $(document).on('click', '.category', function () {
        var cid = $(this).attr('id');
        if (cat_id != cid) {
            cat_id = cid;
            $.ajax({
                type: "get",
                url: base_url+'pos/ajaxproducts',
                data: {category_id: cat_id, tcp: 1},
                dataType: "json",
                success: function (data) {
                    p_page = 'n';
                    // $('#categories-list').addClass('control-sidebar-open');
                    //ocat_id = cat_id;
                    tcp = data.tcp;
                    $('.items').html(data.products);
                    $('.category').removeClass('active');
                    $('#' + cat_id).addClass('active');
                    nav_pointer();
                }
            });
        }
        return false;
    });
    $('#category-' + cat_id).addClass('active');

    $('#next').click(function () {
        if (p_page == 'n') { p_page = 0; }
        p_page += pro_limit;
        if (tcp >= pro_limit && p_page < tcp) {
            $.ajax({
                type: "get",
                url: base_url+'pos/ajaxproducts',
                data: {category_id: cat_id, per_page: p_page},
                dataType: "html",
                success: function (data) {
                    $('.items').html(data);
                    nav_pointer();
                }
            });
        } else {
            p_page -= pro_limit;
        }
    });

    $('#previous').click(function () {
        if (p_page == 'n') { p_page = 0; }
        if (p_page != 0) {
            p_page -= pro_limit;
            if (p_page == 0) { p_page = 'n'; }
            $.ajax({
                type: "get",
                url: base_url+'pos/ajaxproducts',
                data: {category_id: cat_id, per_page: p_page},
                dataType: "html",
                success: function (data) {
                    $('.items').html(data);
                    nav_pointer();
                }
            });
        }
    });

    $("#add_item").autocomplete({
        source      : base_url+'pos/suggestions',
        minLength   : 1,
        autoFocus   : false,
        delay       : 200,
        response    : function (event, ui) {
            
            if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                
                bootbox.alert(lang.no_match_found, function () {
                    $('#add_item').focus();
                });
                $(this).val('');
            }
            else if (ui.content.length == 1 && ui.content[0].id != 0) {
                
                ui.item = ui.content[0];
                $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                $(this).autocomplete('close');
            }
            else if (ui.content.length == 1 && ui.content[0].id == 0) {
                
                bootbox.alert(lang.no_match_found, function () {
                    $('#add_item').focus();
                });
                $(this).val('');
            }
        },
        select: function (event, ui) {
            event.preventDefault();
            //console.log("Aqui rayamos")
            if (ui.item.id !== 0) {
                var row = add_invoice_item(ui.item);
                if (row)
                    $(this).val('');
            } else {
                bootbox.alert(lang.no_match_found);
            }
        }
    });

    $('#add_item').bind('keypress', function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            $(this).autocomplete("search");
        }
    });

    $('#add_item').focus();
    $('#gccard_no').inputmask("9999 9999 9999 9999");
    $('#gift_card_no').inputmask("9999 9999 9999 9999");
    $("#gcexpiry").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    $('#genNo').click(function () {
        var no = generateCardNo();
        $(this).parent().parent('.input-group').children('input').val(no);
        return false;
    });

    $(document).on('click', '#sellGiftCard', function (e) {
        if (count == 1) {
            spositems = {};
        }
        $('#gcModal').modal({backdrop:'static'});
     });

    $(document).on('click', '#addGiftCard', function (e) {
        var mid = 0,
        gccode = $('#gccard_no').val(),
        gcname = $('#gcname').val(),
        gcvalue = $('#gcvalue').val(),
        gcprice = parseFloat($('#gcprice').val());
        gcexpiry = $('#gcexpiry').val();
        if(gccode == '' || gcvalue == '' || gcprice == '' || gcvalue == 0 || gcprice == 0) {
            $('#gcerror').text(lang.file_required_fields);
            $('.gcerror-con').show();
            return false;
        }
        var gc_data = new Array();
        gc_data[0] = gccode;
        gc_data[1] = gcvalue;
        gc_data[2] = gcexpiry;

        $.ajax({
            type: 'get',
            url: base_url+'gift_cards/sell_gift_card',
            dataType: "json",
            data: { gcdata: gc_data },
            success: function (data) {
                if(data.result === 'success') {
                    
                    spositems[mid] = {"id": mid, "item_id": mid, "label": gcname + ' (' + gccode + ')', "row": {"id": mid, "code": gccode, "name": gcname, "quantity": 1, "price": gcprice, "real_unit_price": gcprice, "tax": 0, "qty": 1, "type": "manual", "discount": "0", "comment": ""}};
                    store('spositems', JSON.stringify(spositems));
                    loadItems();
                    $('#gcModal').modal('hide');
                    $('#gccard_no').val('');
                    $('#gcvalue').val('');
                    $('#gcprice').val('');
                } else {
                    $('#gcerror').text(data.message);
                    $('.gcerror-con').show();
                }
            }
        });
    });

    // $('#opModal').bind().on('click', 'a', function(){
    //     var pg = $.url($(this).attr("href")).param("per_page");
    //     $.get( base_url+'pos/ob_page&per_page='+pg, function( data ) {
    //         $( ".html_con" ).html( data.pd );
    //         $( ".page_con" ).html( data.page );
    //     }, "json");

    //     return false;
    // });


        var pwacc = false;
        $(document).on('click', '.posdel', function () {
            var row = $(this).closest('tr');
            var item_id = row.attr('data-item-id');
            //if(protect_delete == 1) {
            if(1 == 2){    
                var boxd = bootbox.dialog({
                    title: lang.enter_pin_code,
                    closeButton: true,
                    message: '<input id="pos_pin" name="pos_pin" type="password" placeholder="Pin Code" class="form-control kb-pad"> ',
                    buttons: {
                        danger: {
                          label: lang.close,
                          className: "btn-default pull-left",
                          callback: function() { }
                        },
                        success: {
                            label: "<i class='fa fa-tick'></i> "+lang.delete,
                            className: "btn-warning verify_pin",
                            callback: function () {
                                var pos_pin = md5($('#pos_pin').val());
                                if(pos_pin == Settings.pin_code) {
                                    delete spositems[item_id];
                                    row.remove();
                                    if(spositems.hasOwnProperty(item_id)) { } else {
                                        localStorage.setItem('spositems', JSON.stringify(spositems));
                                        loadItems();
                                    }
                                } else {
                                    bootbox.alert(lang.wrong_pin);
                                }
                            }
                        }
                    }
                });
                boxd.on("shown.bs.modal", function() {
                    if (Settings.display_kb == 1) { display_keyboards(); }
                    $( "#pos_pin" ).focus().keypress(function(e) {
                        if (e.keyCode == 13) {
                            e.preventDefault();
                            $('.verify_pin').trigger('click');
                            return false;
                        }
                    });
                });
            } else {
                delete spositems[item_id];
                row.remove();
                if(spositems.hasOwnProperty(item_id)) { } else {
                    localStorage.setItem('spositems', JSON.stringify(spositems));
                    loadItems();
                }
            }
            return false;
        });

    $('#suspend').click(function (){
        if (count <= 1) {
            bootbox.alert(lang.please_add_product);
            return false;
        } else {
            $('#susModal').modal({backdrop:'static'});
        }
    });

    $('#suspend_sale').click(function (){
        ref = $('#reference_note').val();
        if (!ref || ref == '') {
            bootbox.alert(lang.type_reference_note);
            return false;
        } else {
            suspend = $('<span></span>');
            if (sid !== 0) {
                suspend.html('<input type="hidden" name="delete_id" value="'+sid+'" /><input type="hidden" name="suspend" value="yes" /><input type="hidden" name="suspend_note" value="' + ref + '" />');
            } else {
                suspend.html('<input type="hidden" name="suspend" value="yes" /><input type="hidden" name="suspend_note" value="' + ref + '" />');
            }
            suspend.appendTo("#hidesuspend");
            $('#pos-sale-form').submit();
        }
        
    });

    $('#payment').click(function () {
        if (count <= 1) {
            bootbox.alert(lang.please_add_product);
            return false;
        } else {

            // activando los botones de orden de cocina:
            document.getElementById("print_order").disabled = false
            //document.getElementById("print_bill").disabled = false
            //console.log("Ahora se activan los botoncillos")

            if (sid) {
            suspend = $('<span></span>');
            suspend.html('<input type="hidden" name="delete_id" value="'+sid+'" />');
            suspend.appendTo("#hidesuspend");
            }

            gtotal = formatDecimal(total - order_discount + order_tax);
            if(Settings.rounding != 0) {
                round_total = roundNumber(gtotal, parseInt(Settings.rounding));
                var rounding = formatDecimal(round_total - gtotal);
                $('#twt').text(formatMoney(round_total) + ' (' + formatMoney(rounding) + ')');
                $('#quick-payable').text(round_total);
            } else {
                $('#twt').text(formatMoney(gtotal));
                $('#quick-payable').text(gtotal);
            }
            $('#item_count').text((an-1)+' ('+(count-1)+')');
            $("#order_quantity").val(count-1);
            $("#order_items").val(an-1);
            $('#balance').text('0.00');
            
            $('#payModal').modal({backdrop:'static'});

            $('#monto_paid_by').val( $('#amount').val() )
            
            // Trae nuevo correlativo (comprobante)
            document.getElementById('submit-sale').disabled = true
            correlativo()
        }
    });
    $('#payModal').on('shown.bs.modal', function (e) {
        $('#amount').focus().val(0);
        $('#quick-payable').click();
    });
    $('#payModal').on('hidden.bs.modal', function (e) {
        $('#amount').val('').change();
    });

    $('#amount').change(function(e) {
        var total_paying = $(".amount").val();
        $('#total_paying').text(formatMoney(total_paying));
        
        if (typeof round_total == 'undefined') {
          round_total = 0; // by fmz
          console.log("Le ponemos 0")
        }
        
        if(Settings.rounding != 0) {
            //console.log("A rounding != 0")
            //$('#balance').text(formatMoney(total_paying - round_total));
            //$('#balance_val').val(formatDecimal(total_paying - round_total));
            //total_paid = total_paying;
            //grand_total = round_total;
        } else {
            console.log("B rounding == 0")
            $('#balance').text(formatMoney(total_paying - gtotal));
            $('#balance_val').val(formatDecimal(total_paying - gtotal));
            total_paid = total_paying;
            grand_total = gtotal;
        }
    });

    $('#add-customer').click(function () {
        $('#customerModal').modal({backdrop:'static'});
    });

    $('#payModal').on('change', '#paid_by', function (){

/*
        $('#clear-cash-notes').click();

        $('#amount').val(grand_total);

        var p_val = $(this).val();
        $('#paid_by_val').val(p_val);
        var gtotal = formatDecimal(total - order_discount + order_tax);
        if(Settings.rounding != 0) {
            var rounded_total = formatDecimal(roundNumber(gtotal, parseInt(Settings.rounding)));
        } else {
            var rounded_total = formatDecimal(gtotal);
        }

        $('#rpaidby').val(p_val);
        if (p_val == 'gift_card') {
            $('.gc').slideDown();
            $('.ngc').slideUp('fast');
            setTimeout(function(){ $('#gift_card_no').focus(); }, 10);
            $('#amount').attr('readonly', true);
        } else {
            $('.ngc').slideDown();
            $('.gc').slideUp('fast');
            $('#gc_details').html('');
            $('#amount').attr('readonly', false);
        }
        if (p_val == 'cash' || p_val == 'other' || p_val == 'Yape' || p_val == 'Plin') {
            $('.pcash').slideDown();
            $('.pcheque').slideUp('fast');
            $('.pcc').slideUp('fast');
            setTimeout(function(){ $('#amount').focus(); }, 10);
        } else if (p_val == 'CC' || p_val == 'stripe') {
            $('.pcc').slideDown();
            $('.pcheque').slideUp('fast');
            $('.pcash').slideUp('fast');
            $('#amount').val(rounded_total);
            setTimeout(function(){ $('#swipe').val('').focus(); }, 10);
        } else if (p_val == 'cheque') {
            $('.pcheque').slideDown();
            $('.pcc').slideUp('fast');
            $('.pcash').slideUp('fast');
            $('#amount').val(rounded_total);
            setTimeout(function(){ $('#cheque_no').focus(); }, 10);
        } else {
            $('.pcheque').hide();
            $('.pcc').hide();
            $('.pcash').hide();
        }
*/
    });

    $(document).on('change', '.gift_card_no', function () {
        var cn = $(this).val() ? $(this).val() : '';
        if (cn != '') {
            $.ajax({
                type: "get", async: false,
                url: base_url + "pos/validate_gift_card/" + cn,
                dataType: "json",
                success: function (data) {
                    if (data === false || data.balance < 0) {
                        $('#gift_card_no').parent('.form-group').addClass('has-error');
                        bootbox.alert(lang.incorrect_gift_card);
                    } else {
                        $('#gc_details').html(lang.card_no+': ' + data.card_no + '<br>'+lang.value+': ' + data.value + ' - '+lang.balance+': ' + data.balance);
                        $('#gift_card_no').parent('.form-group').removeClass('has-error');
                        var paying = (gtotal > data.balance) ? data.balance : gtotal;
                        $('#amount_val').val(paying);
                        $('#amount').val(paying);
                    }
                }
            });
        }
        return false;
    });

    $(document).on('click', '#quick-payable', function () {
        $('#clear-cash-notes').click();
        $(this).append('<span class="badge">1</span>');
        $('#amount').val(grand_total);
    });

    $(document).on('click', '.quick-cash', function () {
        
        if ($('#quick-payable').find('span.badge').length) {
            $('#clear-cash-notes').click();
        }
        
        var $quick_cash = $(this);
        var amt = $quick_cash.contents().filter(function () {
            return this.nodeType == 3;
        }).text();
        
        var th = Settings.thousands_sep == 0 ? '' : Settings.thousands_sep;
        var $pi = $('#amount');
        
        amt = formatDecimal(amt.split(th).join("")) * 1 + $pi.val() * 1;
        $pi.val(formatDecimal(amt)).change().focus();
        
        var note_count = $quick_cash.find('span');
        if (note_count.length == 0) {
            $quick_cash.append('<span class="badge">1</span>');
        } else {
            note_count.text(parseInt(note_count.text()) + 1);
        }
        
        //console.log("total_paying:" + $("#total_paying").text())
        //console.log("total-payable:" + $("#total-payable").text())
        
        let con_pago = $("#total_paying").text()
        let con_monto = $("#total-payable").text()
        if(con_pago.substring(0,1) == 'S'){
            con_pago = con_pago.substring(2)
            con_monto = con_monto.substring(2)
        }
        let con_vuelto = (con_pago * 1.00) - (con_monto * 1.00)
        console.log("con_pago:" + con_pago)
        console.log("con_monto:" + con_monto)
        console.log("con_vuelto:" + con_vuelto)
        $("#balance").text(con_vuelto)
    });

    $(document).on('click', '#clear-cash-notes', function () {
        $('.quick-cash').find('.badge').remove();
        $('#amount').val('').change().focus();
    });

    $('#payModal').on('change', '#amount, #paid_by', function (e) {
        $('#amount_val').val($('#amount').val());
    });
    $('#payModal').on('blur', '#amount', function (e) {
        $('#amount_val').val($('#amount').val());
    });
    //$('#payModal').on('select2-close', '#paid_by', function (e) {
    //    $('#paid_by_val').val($(this).val());
    //});
    $('#payModal').on('change', '#pcc_no', function (e) {
        $('#cc_no_val').val($(this).val());
    });
    $('#payModal').on('change', '#pcc_holder', function (e) {
        $('#cc_holder_val').val($(this).val());
    });
    $('#payModal').on('change', '#gift_card_no', function (e) {
        $('#paying_gift_card_no_val').val($(this).val());
    });
    $('#payModal').on('change', '#pcc_month', function (e) {
        $('#cc_month_val').val($(this).val());
    });
    $('#payModal').on('change', '#pcc_year', function (e) {
        $('#cc_year_val').val($(this).val());
    });
    $('#payModal').on('change', '#pcc_type', function (e) {
        $('#cc_type_val').val($(this).val());
    });
    $('#payModal').on('change', '#pcc_cvv2', function (e) {
        $('#cc_cvv2_val').val($(this).val());
    });
    $('#payModal').on('change', '#cheque_no', function (e) {
        $('#cheque_no_val').val($(this).val());
    });
    $('#payModal').on('change', '#payment_note', function (e) {
        $('#payment_note_val').val($(this).val());
    });

    //$('#payModal').on('change', '#tipoDoc', function (e) {
    //    $('#tipoDoc_val').val($(this).val());
    //    console.log("Mahikari");
    //});

    $('#payModal').on('change', '#note', function (e) {
        var n = $(this).val();
        store('spos_note', n);
        $('#spos_note').val(n);
    });
    if(spos_note = get('spos_note')) {
        $('#note').val(spos_note);
        $('#snote').val(spos_note);
    }
    $('#spos_customer').change( function (e) {
        store('spos_customer', $(this).val());
    });
    if(spos_customer = get('spos_customer')) {
        $('#spos_customer').select2('val', spos_customer);
    }

    $('.treeview').hover(function(e) {
        var wh = $(document).height();
        var top = $(this).offset().top;
        var menu = $(this).find('.treeview-menu');
        var menuh = menu.height() + 44;
        if ((wh-top) < menuh) {
            $(this).find('a').children('span').addClass('popup')
            menu.addClass('popup');
        } else {
            $(this).find('a').children('span').removeClass('popup')
            menu.removeClass('popup');
        }
    });

    $('body').click(function(e) {
        if (!$(e.target).hasClass('sidebar-icon') && !$(e.target).hasClass('sb') && $('#categories-list').hasClass('control-sidebar-open')) {
            $('#categories-list').removeClass('control-sidebar-open');
        }
    });

    $('#submit-sale').click(function(){
        
        if($("#paid_by").val() == "Rappi" || $("#paid_by").val() == "PedidosYa"){
            //**** CAMBIANDO PRECIOS SEGUN TABLA PRECIOS DELIVERY (tec_product_store_qty) 
            //cambiar_precios()
            if($("#paid_by").val() == "Rappi"){
                document.getElementById("cual_delivery").value = "1"
            }

            if($("#paid_by").val() == "PedidosYa"){
                document.getElementById("cual_delivery").value = "2"
            }
        }        

        // Validando temas
        if($("#tipoDoc").val() == ''){
            alert("Falta elegir Tipo de Documento")
            document.getElementById("tipoDoc").focus()
            return false
        }

        if($("#paid_by").val() == ''){
            alert("Falta elegir Método de Pago")
            document.getElementById("paid_by").focus()
            return false
        }

        // Validacion de personal_delivery
        if(document.getElementById("delivery").value == "4"){  // Delivery Propio
            var xPersona = document.getElementById("persona_delivery").value
            if(xPersona.length == 0){
                alert("Debe ingresar la persona que hizo el Delivery")
                //toastr.warning('Debe ingresar la persona que hizo el Delivery', 'Alerta!')
                document.getElementById("persona_delivery").focus()
                return false
            }else{
                $("#txt_persona_delivery").val(xPersona)
            }
        }

        $("#txt_tipoDocAfectado").val($("#tipoDocAfectado").val())
        $("#txt_numDocfectado").val($("#numDocfectado").val())
        $("#txt_serieDocfectado").val($("#serieDocfectado").val())
        
        if($("#tipoDoc").val() == 'Nota_de_credito'){
            let cValor_motivo = document.getElementById("codMotivo").value
            if(cValor_motivo.length == 0){
                alert("Elija un motivo de Nota de Credito")
                document.getElementById("codMotivo").focus()
                return false
            }
        }

        if($("#tipoDoc").val() == 'Nota_de_debito'){
            $("#txt_codMotivo").val($("#codMotivo2").val())

            let combo = document.getElementById("codMotivo2");
            let selected = combo.options[combo.selectedIndex].text;
            $("#txt_desMotivo").val(selected)
        }


        // Verifica si ha elegido Directo en Tienda, no debe permitir elegir Rappi o PedidosYa
        if(document.getElementById("delivery").value == "3"){
            if(document.getElementById("paid_by").value == "Rappi" || document.getElementById("paid_by").value == "PedidosYa"){
                //alert("No puede escoger Delivery en Directo_en_tienda")
                //document.getElementById("paid_by").focus()
                //return false
            }
        }

        if(document.getElementById("delivery").value == "3"){
            if(document.getElementById("paid_by2").value == "Rappi" || document.getElementById("paid_by2").value == "PedidosYa"){
                //alert("No puede escoger Delivery en Directo_en_tienda")
                //document.getElementById("paid_by2").focus()
                //return false
            }
        }

        // Validar que la suma de los montos no sea mayor al total
        
        if($("#monto_paid_by").length > 0){ var monto_paid_by   = $("#monto_paid_by").val() * 1 }else{ var monto_paid_by   = 0 }
        if($("#monto_paid_by2").length > 0){ var monto_paid_by2  = $("#monto_paid_by2").val() * 1}else{ var monto_paid_by2 = 0 }
        
        // Validar si en caso no tiene metodo de pago y si monto.
        if(document.getElementById("paid_by").value == ""){
            alert("falta ingresar Metodo de Pago")
            document.getElementById("paid_by").focus()
            return false
        }
        if(document.getElementById("paid_by2").value == ""){
            //alert("falta ingresar Metodo de Pago")
            //document.getElementById("paid_by2").focus()
            //return false
        }

        if(monto_paid_by <= 0){
            alert("Los montos no son válidos")
            return false
        }

        // Hallando el monto total:
        var twt = document.getElementById("twt").innerHTML
        var pos_t = twt.indexOf("(")
        var subC = twt.substr(0,pos_t)

        // Ahora falta quitarle el simbolo de soles
        if(subC.substr(0,1) == 'S'){
            var monto_grand_total = (subC.substr(2,subC.length - 2))*1
        }else{
            var monto_grand_total = subC * 1
        }

        if(parecido_a(monto_grand_total, monto_paid_by + monto_paid_by2, 0.05)){
            // OK
        }else{
            alert("Los montos difieren al Total. Corrija")
            return false
        }

        // Parte 1
        $("#paid_by_val").val(document.getElementById("paid_by").value)
        $("#txt_monto_paid_by").val( $("#monto_paid_by").val() )

        // Parte 2
        $("#txt_paid_by2").val( $("#paid_by2").val() )
        $("#txt_monto_paid_by2").val( $("#monto_paid_by2").val() )

        if($("#tipoDoc").val() == 'Boleta'){document.getElementById('correlativo').value = jObe.maximo_boleta}
        if($("#tipoDoc").val() == 'Factura'){document.getElementById('correlativo').value = jObe.maximo_factura}
        if($("#tipoDoc").val() == 'Ticket'){document.getElementById('correlativo').value = jObe.maximo_ticket}

        //console.log("Llega Axario");
        if(jObe.maximo_boleta > 0 || jObe.maximo_factura > 0 || jObe.maximo_ticket > 0){
            if($("#tipoDoc").val() == 'Nota_de_debito' || $("#tipoDoc").val() == 'Nota_de_credito'){
                $('#total_items').val(an - 1);
                $('#total_quantity').val(count - 1);
                $('#submit').click();
            }else{
                //console.log("Blow_1")
                verificar_datos_cliente()
            }
        }

        
    });

    var hold_ref = $('#hold_ref').val();
    $('#hold_ref').change(function () {
        hold_ref = $(this).val();
        $('#reference_note').val(hold_ref);
    });
    
    $('#reference_note').change(function () {
        hold_ref = $(this).val();
        $('#hold_ref').val(hold_ref);
    });

    $('#suspend_sale').click(function() {
        if($('#reference_note').val()) {
            $('#hold_ref').val($('#reference_note').val());
            $('#total_items').val(an - 1);
            $('#total_quantity').val(count - 1);
            $('#submit').click();
        }
        return false;
    });

    $("#customer-form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            url: base_url+'customers/add',
            data: $( this ).serialize(),
            dataType: "json",
            success: function(res) {
                if(res.status == 'success') {
                    $('#spos_customer').append($("<option></option>").attr("value",res.id).text(res.val));
                    $('#spos_customer').select2('val', res.id);
                    $('#customerModal').modal('hide');
                } else {
                    $('#c-alert').html(res.msg);
                    $('#c-alert').show();
                }
            },
            error: function(){
                bootbox.alert(lang.customer_request_failed);
                return false;
            }
        });
        return false;
    });

    $('#customerModal').on('hidden.bs.modal', function (e) {
        $('#c-alert').hide();
        $('#cname').val('');
        $('#cemail').val('');
        $('#cphone').val('');
        $('#cf1').val('');
        $('#cf2').val('');
    });

});

function display_keyboards() {
    if( ! jQuery.browser.mobile) {
        $('.kb-text').keyboard({
            autoAccept: true,
            alwaysOpen: false,
            openOn: 'focus',
            usePreview: false,
            // layout: 'qwerty',
            layout: 'custom',
            display: {
                'bksp': "\u2190",
                'accept': 'return',
                'default': 'ABC',
                'meta1': '123',
                'meta2': '#+='
            },
            customLayout: {
                'default': [
                    'q w e r t y u i o p {bksp}',
                    'a s d f g h j k l {enter}',
                    '{s} z x c v b n m , . {s}',
                    '{meta1} {space} {cancel} {accept}'
                ],
                'shift': [
                    'Q W E R T Y U I O P {bksp}',
                    'A S D F G H J K L {enter}',
                    '{s} Z X C V B N M / ? {s}',
                    '{meta1} {space} {meta1} {accept}'
                ],
                'meta1': [
                    '1 2 3 4 5 6 7 8 9 0 {bksp}',
                    '- / : ; ( ) \u20ac & @ {enter}',
                    '{meta2} . , ? ! \' " {meta2}',
                    '{default} {space} {default} {accept}'
                ],
                'meta2': [
                    '[ ] { } # % ^ * + = {bksp}',
                    '_ \\ | &lt; &gt; $ \u00a3 \u00a5 {enter}',
                    '{meta1} ~ . , ? ! \' " {meta1}',
                    '{default} {space} {default} {accept}'
                ]}
        });

        $('.kb-pad').keyboard({
            restrictInput: true,
            preventPaste: true,
            autoAccept: true,
            alwaysOpen: false,
            openOn: 'click',
            usePreview: false,
            layout: 'costom',
            display: {
                'b': '\u2190:Backspace'
            },
            customLayout: {
                'default': [
                    '1 2 3 {b}',
                    '4 5 6 . {clear}',
                    '7 8 9 0 %',
                    '{accept} {cancel}'
                ]
            }
        });
    }
}

function calTax() {
    var ts = get('spos_tax') ? get('spos_tax') : $('#tax_val').val();
    if(ts.indexOf("%") !== -1) {
        var pts = ts.split("%");
        order_tax = ((total-order_discount)*parseFloat(pts[0]))/100;
        $("#ts_con").text(formatMoney(order_tax));
    } else {
        order_tax = parseFloat(ts);
        $("#ts_con").text(formatMoney(order_tax));
    }
    return order_tax;
}

function nav_pointer() {
    var pp = p_page == 'n' ? 0 : p_page;
    (pp == 0) ? $('#previous').attr('disabled', true) : $('#previous').attr('disabled', false);
    ((pp+pro_limit) > tcp) ? $('#next').attr('disabled', true) : $('#next').attr('disabled', false);
}


// function Popup(data) {
//     createWin(data).then(function (w) {
//         // w.close();
//     });
// }
function Popup(t, e) {
    //$("#print-title").text(lang.print + " " + lang[e]),
    //$("#print-body").html(t),
    //$("#printModal").show()
    location.href = "./themes/default/views/pos/wanabana.php?t=" + t
}

// function createWin(data) {
//     return new Promise(function(resolve) {
//         var d = '<!DOCTYPE html><html><head><title>Print</title><link rel="stylesheet" href="'+assets+'bootstrap/css/bootstrap.min.css" type="text/css" /></head><body>'+data+'<script type="text/javascript">window.print();</script></body></html>';
//         var mywindow = window.open(d, 'spos_print', 'height=500,width=300');
//         mywindow.document.write(d);
//         setTimeout(function() { resolve(mywindow); }, 20);
//     });
// }
function createWin(o){
    return new Promise(function(t) {
        var e = '<!DOCTYPE html><html><head><title>Print</title><link rel="stylesheet" href="' + assets + 'bootstrap/css/bootstrap.min.css" type="text/css" /></head><body>' + o + '<script type="text/javascript">window.print();<\/script></body></html>'
          , a = window.open(e, "spos_print", "height=500,width=300");
        a.document.write(e),
        setTimeout(function() {
            t(a)
        }, 20)
    }
    )
}
$(document).ready(function() {
    $(document).on("click", "#print-modal-close", function(t) {
        $("#printModal").hide()
    })
}),

$(document).ready(function($) {
    window.setTimeout(function() {
        $('.alerts').slideUp();
    }, 15000);
    $('.alerts').on('click',function(e) {
        $(this).slideUp();
    });
    $('#list-table-div').slimScroll({start: 'bottom'});
    $('#category-sidebar-menu').slimScroll({width: '100%'});
    $('.items').slimScroll({});
});

function posScreen() {
    var wh = $(window).height(),
    total_dh = $('#totaldiv').height(),
    buttons_dh = $('.botbuttons').height(),
    left_top_dh = $('#lefttop').outerHeight();
    var items_dh = wh-120, list_table_dh = wh-185-left_top_dh-total_dh-buttons_dh;
    $('#right-col').height(wh-100);
    $('.items').height((items_dh > 400 ? items_dh : 400));
    $('#list-table-div').height(list_table_dh);
}

function printLine(str) {
    var size = parseInt(Settings.char_per_line)-4;
    var len = str.length;
    var res = str.split(":");
    var newd = res[0];
    for(i=1; i<(size-len); i++) {
        newd += " ";
    }
    newd += res[1];
    return newd;
}

$(window).bind("resize", posScreen);

function read_card() {

}

$.extend($.keyboard.keyaction, {
    enter : function(base) {
        if (base.$el.is("textarea")){
            base.insertText('\r\n');
        } else {
            base.accept();
        }
    }
});

$(document).ready(function() {
    posScreen();
    if (Settings.display_kb == 1) { display_keyboards(); }
    nav_pointer();
    loadItems();
    read_card();

    $('.swipe').keypress( function (e) {
        var TrackData = $(this).val() ? $(this).val() : '';
        if(TrackData != '') {
            if (e.keyCode == 13) {
                e.preventDefault();
                var p = new SwipeParserObj(TrackData);

                if(p.hasTrack1)
                {

                    var CardType = null;
                    var ccn1 = p.account.charAt(0);
                    if(ccn1 == 4)
                        CardType = 'Visa';
                    else if(ccn1 == 5)
                        CardType = 'MasterCard';
                    else if(ccn1 == 3)
                        CardType = 'Amex';
                    else if(ccn1 == 6)
                        CardType = 'Discover';
                    else
                        CardType = 'Visa';

                    $('#pcc_no').val(p.account).change();
                    $('#pcc_holder').val(p.account_name).change();
                    $('#pcc_month').val(p.exp_month).change();
                    $('#pcc_year').val(p.exp_year).change();
                    $('#pcc_cvv2').val('');
                    $('#pcc_type').select2('val', CardType);

                } else {
                    $('#pcc_no').val('').change();
                    $('#pcc_holder').val('').change();
                    $('#pcc_month').val('').change();
                    $('#pcc_year').val('').change();
                    $('#pcc_cvv2').val('').change();
                    $('#pcc_type').val('').change();
                }

                $('#pcc_cvv2').focus();
            }
        }

    }).blur(function (e) {
        $(this).val('');
    }).focus( function (e) {
        $(this).val('');
    });

    $(document).on('blur', '#pcc_no', function () {
        var cn = $(this).val();
        var ccn1 = cn.charAt(0);
        if(ccn1 == 4)
            CardType = 'Visa';
        else if(ccn1 == 5)
            CardType = 'MasterCard';
        else if(ccn1 == 3)
            CardType = 'Amex';
        else if(ccn1 == 6)
            CardType = 'Discover';
        else
            CardType = 'Visa';

        $('#pcc_type').select2('val', CardType);
    });

    $('.modal').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });
    $('#clearLS').click(function(event) {
        bootbox.confirm(lang.r_u_sure, function(result) {
            if(result == true) {
                localStorage.clear();
                location.reload();
            }
        });
        return false;
    });

    if(Settings.focus_add_item != '') {
        shortcut.add(Settings.focus_add_item, function() { $("#add_item").focus(); }, { 'type':'keydown', 'propagate':false, 'target':document} );
    }
    if(Settings.add_customer != '') {
        shortcut.add(Settings.add_customer, function() { $("#add-customer").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} );
    }
    if(Settings.toggle_category_slider != '') {
        shortcut.add(Settings.toggle_category_slider, function() { $('[data-toggle="control-sidebar"]').trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} );
    }
    if(Settings.cancel_sale != '') {
        shortcut.add(Settings.cancel_sale, function() { $("#reset").click(); }, { 'type':'keydown', 'propagate':false, 'target':document} );
    }
    if(Settings.suspend_sale != '') {
       shortcut.add(Settings.suspend_sale, function() { $("#suspend").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} );
   }
   if(Settings.print_order != '') {
       shortcut.add(Settings.print_order, function() { $("#print_order").click(); }, { 'type':'keydown', 'propagate':false, 'target':document} );
   }
   /*if(Settings.print_bill != '') {
       shortcut.add(Settings.print_bill, function() { $("#print_bill").click(); }, { 'type':'keydown', 'propagate':false, 'target':document} );
   }*/
   if(Settings.finalize_sale != '') {
       shortcut.add(Settings.finalize_sale, function() { $("#payment").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} );
   }
   if(Settings.today_sale != '') {
       shortcut.add(Settings.today_sale, function() { $("#today_sale").click(); }, { 'type':'keydown', 'propagate':false, 'target':document} );
   }
   if(Settings.open_hold_bills != '') {
       shortcut.add(Settings.open_hold_bills, function() { $("#opened_bills").trigger('click'); }, { 'type':'keydown', 'propagate':false, 'target':document} );
   }
   if(Settings.close_register != '') {
       shortcut.add(Settings.close_register, function() { $("#close_register").click(); }, { 'type':'keydown', 'propagate':false, 'target':document} );
   }

});
$.fn.focusToEnd = function() { return this.each(function() { var v = $(this).val(); $(this).focus().val("").val(v); }); };
$.ajaxSetup ({ cache: false, headers: { "cache-control": "no-cache" } });

function parecido_a(a, b, dife){ // deben ser montos muy aproximados para que pueda validar
    if(a > b){
        if(a > b + dife){
            return false
        }
    }

    if(a < b){
        if(a + dife < b){
            return false 
        }
    }

    return true
}

function suma_metodo(){
    var el_total = ($('#monto_paid_by').val()*1) + ($('#monto_paid_by2').val()*1)
    $('#suma_metodo').html(el_total)
}