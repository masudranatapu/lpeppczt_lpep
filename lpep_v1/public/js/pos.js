function openInNewTab(url) {
    var win = window.open(url, "_blank");
    if (win) {
        win.focus();
    } else {
        console.error("Popup blocked or failed to open:", url);
    }
}

if (SESSION_SALE_ID) openInNewTab(SESSION_SALE_ID);

function getCustomer() {
    $.get(ROUTE_CONTACT_CUSTOMER_JSON, function (data) {
        $("#contact_id").html(data);
    });
}

function product_add(product_id) {
    $.post(
        ROUTE_SALE_CART,
        {
            _token: CSRF_TOKEN,
            product_id: product_id,
        },
        function (data) {
            if (typeof data === "object") {
                if (data.error) {
                    $("#search_product").val("");
                    toastr.error(data.error);
                } else {
                    $("#search_product").val("");
                    productShowInTable(data);
                }
            } else {
                $("#search_product").attr("list", "colorList");
                $("#showSearchProducts").html(data);
            }
        }
    );
}

getCustomer();

let timer = null;
$("#search_product")
    .autocomplete({
        html: true,
        source: function (request, response) {
            $.ajax({
                url: "/autocomplete/sale",
                dataType: "json",
                data: {
                    q: request.term,
                },
                success: function (data) {
                    response(
                        $.map(data, function (item) {
                            clearInterval(timer);
                            if (data.length == 1) {
                                timer = setTimeout(
                                    () => checkExists(item.product_id),
                                    10
                                );
                            } else {
                                return {
                                    label: item.product_name,
                                    value: item.barcode,
                                    product_id: item.product_id,
                                    quantity: item.quantity,
                                };
                            }
                        })
                    );
                },
            });
        },
        minLength: 3,
        select: function (event, ui) {
            checkExists(ui.item.product_id);
            return false;
        },
        open: function () {
            $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
        },
        close: function () {
            $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
        },
    })
    .data("ui-autocomplete")._renderItem = (ul, item) =>
        $("<li></li>")
            .data("item.autocomplete", item)
            .append(
                `<div style="display:flex; padding: 3px 1em 3px .4em !important; justify-content: space-between; ${item.quantity == 0 ? "background:#ffcece; color:#333" : ""
                }">
                            <span>${item.label}</span> <span>Qty: ${item.quantity
                }</span>
                        </div>`
            )
            .appendTo(ul);

function checkExists(product_id) {
    $.post(
        ROUTE_SALE_CART,
        {
            _token: CSRF_TOKEN,
            product_id: product_id,
        },
        function (data) {
            if (typeof data === "object") {
                if (data.error) {
                    $("#search_product").val("");
                    toastr.error(data.error);
                } else {
                    $("#search_product").val("");
                    productShowInTable(data);
                }
            }
        }
    );
}

function cartList() {
    $.get(ROUTE_CART_LIST, function (data) {
        productShowInTable(data);
    });
}

cartList();

function normalProductTemplate(item) {
    /* html */
    return `
        <tr>
            <td style="padding:4px 0px; line-height:2; margin:0px; font-size: 13px;font-weight:500;padding-left:10px;">
                ${item.product_name} (${item.barcode})
            </td>

            <td class="text-center" class="tx-right tx-medium tx-inverse td-style">
            </td>

            <td class="text-center" class="tx-right tx-medium tx-inverse td-style">
                <div class="cart-info quantity" style="display: flex">
                    <div class="btn-decrement" onClick="decrementQuantity(${item.id})">-</div>
                    <input class="input-quantity qty"
                        id="inputQuantity-${item.id}"
                        data-is-decimal="${item.is_decimal}"
                        onchange="changeQuantity(this)"
                        value="${item.qty}"
                        data-id="${item.id}"
                        autocomplete="off">
                    <div class="btn-increment" onClick="incrementQuantity(${item.id})">+</div>
                </div>
            </td>

            <td class="text-center" class="tx-right tx-medium tx-inverse td-style" id="price-${item.id}">
                <input type="text" value="${item.price}" onchange="editPrice(${item.id})" id="editPricing${item.id}" style="border: 0px;text-align: center;">
            </td>

            <td class="text-center" class="tx-right tx-medium tx-inverse td-style" id="total_price-${item.id}"> ${item.total_price} </td>

            <td class="text-center" class="tx-right tx-medium tx-danger td-style">
                <a onclick="removeCart(${item.id})" style="border: none" class="text-danger"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
    `;
}

function incrementQuantity(id) {
    $.post(
        ROUTE_CART_INCREMENT,
        {
            _token: CSRF_TOKEN,
            id: id,
        },
        function (data) {
            if (data.error) {
                $("#search_product").val("");
                toastr.error(data.error);
            } else {
                productShowInTable(data);
            }
        }
    );
}

function changeQuantity(el) {
    const input = $(el);
    let val = input.val();

    if (val < 0) {
        input.val(0);
        return 0;
    } else if (val > 0 && input.data("is-decimal") != 1 && !_.isInteger(val)) {
        val = parseInt(val);
        input.val(val);
    }

    $.post(
        ROUTE_CART_QUANTITY_UPDATE,
        {
            _token: CSRF_TOKEN,
            id: input.data("id"),
            value: val,
        },
        function (data) {
            if (data.error) {
                $("#search_product").val("");
                toastr.error(data.error);
            } else {
                productShowInTable(data);
            }
        }
    );
}

function decrementQuantity(id) {
    const input = $("#inputQuantity-" + id);
    if (input.val() - 1 < 0) {
        return 0;
    }

    $.post(
        ROUTE_CART_DECREMENT,
        {
            _token: CSRF_TOKEN,
            id: id,
        },
        function (data) {
            productShowInTable(data);
        }
    );
}

function editPrice(cart_id) {
    let price = $(`#editPricing${cart_id}`).val();
    $.post(
        ROUTE_SALE_CHANGE_PRICE,
        {
            _token: CSRF_TOKEN,
            cart_id: cart_id,
            price: price,
        },
        function (data) {
            if (data) {
                cartList();
            }
        }
    );
}

function productShowInTable(result) {

    $("#myCartList").html(result.cart);

    $("input[name=customer_id]").val($("#contact_id").val());

    $("#ttax2").text(result.vat);
    $("input[name=vat]").val(result.vat);
    $("#vatModal").text(result.vat);

    $("#titems").text(result.items);
    $("#itemModal").text(result.items);

    $("#total").text(result.total);
    $("input[name=sub_total]").val(result.total);
    $("#sub_totalModal").text(result.total);

    const finalTotal = result.total + result.vat;
    $("#finalTotal").text(finalTotal);
    $("input[name=total_amount]").val(finalTotal);
    $("input[name=paying_amount]").val(finalTotal);
    $("#paing_amountModal").text(finalTotal);
    $("#total_amountModal").text(finalTotal);
    $("#total_amountModal2").text(finalTotal);

    if ($("#discount_total_amount").val()) {
        discountExist();
    }
}

function discountExist() {
    let discount_total_amount = $("#discount_total_amount").val();
    let discount_type = $("#discount_type").val();
    let total_amount_get_text = Number($("#total").text());
    let vat_amount = Number($("#ttax2").text());
    let totalAmount = 0;
    let percentage = null;

    if (discount_type == 1) {
        totalAmount = Number(
            total_amount_get_text - discount_total_amount + vat_amount
        ).toFixed(2);
    } else {
        percentage = (discount_total_amount * total_amount_get_text) / 100;
        totalAmount = total_amount_get_text - percentage + vat_amount;
    }

    $("#tds").text(percentage ? percentage : discount_total_amount);
    $("input[name=discount_amount]").val(
        percentage ? percentage : discount_total_amount
    );
    $("#discount_amountModal").text(
        percentage ? percentage : discount_total_amount
    );

    $("#gtotal").text(totalAmount);
    $("#finalTotal").text(totalAmount);
    $("#discount_total_amount").val(discount_total_amount);
    $("#discountModal").modal("hide");
    $("input[name=total_amount]").val(totalAmount);
    $("#total_amountModal").text(totalAmount);
    $("input[name=paying_amount]").val(totalAmount);
    $("#paing_amountModal").text(totalAmount);
    $("#total_amountModal2").text(totalAmount);
}

function removeCart(id) {
    $.post(
        ROUTE_CART_REMOVE,
        {
            _token: CSRF_TOKEN,
            id: id,
        },
        function (data) {
            productShowInTable(data);
        }
    );
}

$("body").on("click", "#discountSave", function (e) {
    e.preventDefault();
    discountExist();
});

$("body").on("input", "input[name=paying_amount]", function () {
    let paying_amount = $(this).val();
    let total_amount = $("input[name=total_amount]").val();
    $("#due_amountModal").text(total_amount - paying_amount);
    $("input[name=total_due]").val(total_amount - paying_amount);
});

$("body").on("change", "#contact_id", function () {
    let val = $(this).val();

    if (val == 1) {
        $('.customer-section').show();
    }else{
        $('.customer-section').hide();
    }

    if (val == "") {
        $("#checkout").attr("disabled", true);
        $("input[name=paying_amount]").attr("readonly", true);

    } else {
        $("#checkout").attr("disabled", false);
        $("input[name=paying_amount]").removeAttr("readonly");
        $.post(
            ROUTE_CONTACT_GROUP_CHECK,
            {
                _token: CSRF_TOKEN,
                contact_id: val,
            },
            function (data) {
                let total_amount_get_text = parseInt($("#total").text());
                let vat_amount = parseInt($("#ttax2").text());
                if (data) {
                    let percentage = (data * total_amount_get_text) / 100;
                    $("#gtotal").text(
                        total_amount_get_text - percentage + vat_amount
                    );
                    $("#finalTotal").text(
                        total_amount_get_text - percentage + vat_amount
                    );
                    $("#discount_total_amount").val(data);
                    $("#tds").text(percentage);
                    $("#discountModal").modal("hide");

                    $("input[name=discount_amount]").val(percentage);
                    $("#discount_amountModal").text(percentage);

                    $("#discount_type option[value=2]").attr("selected", true);

                    $("input[name=total_amount]").val(
                        total_amount_get_text - percentage + vat_amount
                    );
                    $("#total_amountModal").text(
                        total_amount_get_text - percentage + vat_amount
                    );
                    $("input[name=paying_amount]").val(
                        total_amount_get_text - percentage + vat_amount
                    );
                    $("#paing_amountModal").text(
                        total_amount_get_text - percentage + vat_amount
                    );
                    $("#total_amountModal2").text(
                        total_amount_get_text - percentage + vat_amount
                    );
                }
            }
        );
    }
    $("input[name=customer_id]").val(val);
});

function cancel() {
    $.get(ROUTE_POS_CLEAR, function (data) {
        productShowInTable(data);
    });
}

$("body").on("change", "#pay_by", function () {
    let account_type = $(this).val();
    $.post(
        ROUTE_SALE_PAYMENT_ACCOUNT,
        {
            _token: CSRF_TOKEN,
            account_type: account_type,
        },
        function (data) {
            $("#account_info").html(data);
            $(".select3").select2();
        }
    );
});

$("body").on("change", "#get_category_id", function () {
    let id = $(this).val();
    $.post(
        ROUTE_FILTER_PRODUCT_CATEGORY,
        {
            _token: CSRF_TOKEN,
            category_id: id,
        },
        function (data) {
            $("#filter_product_list").html(data);
        }
    );
});

$("body").on("change", "#get_brand_id", function () {
    let id = $(this).val();
    $.post(
        ROUTE_FILTER_PRODUCT_BRNAD,
        {
            _token: CSRF_TOKEN,
            brand_id: id,
        },
        function (data) {
            $("#filter_product_list").html(data);
        }
    );
});

$(document).on("change", "select.batch_id", function () {

    $.get(
        `${ROUTE_CART_CHANGE_BATCH_ID}?id=${$(this)
            .closest("tr")
            .data("sc-id")}&batch_id=${$(this).val()}&source_id=${$(
                this
            )
                .find("option:selected")
                .data("purchase-product-id")}&type=${$('select.batch_id option:selected').data('type')}`,
        function (res) {
            productShowInTable(res);
        }
    );
});

// customer create
$("body").on("click", "#addNewCustomer", function () {
    $.get(ROUTE_CONTACT_CUSTOMER_CREATE, function (data) {
        $("#modalcontent").html(data);
        $("#contactModal").modal("show");
    });
});

$('body').on("submit", "#customerStore", function (e) {
    e.preventDefault();
    $.post($(this)[0].action, $(this).serialize(), function (res) {
        toastr.success(res)
        getCustomer();
        $("#contactModal").modal("hide");
    });
})


 // start payment modal
 $(".open-payment-modal").click(function () {
    $(".payment-modal-lg").modal("show");
});

// Close Modal on Button Click
$(".close-payment-modal").click(function () {
    $(".payment-modal-lg").modal("hide");
});

// Close Modal on Escape Key
$(document).on("keydown", function (event) {
    if (event.key === "Escape") {
        $(".payment-modal-lg").modal("hide");
    }
});
 // end payment modal
