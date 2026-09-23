const productCounter = {};

(function () {
    let textField = document.getElementById("search_product");

    if (textField) {
        textField.addEventListener("keydown", function (mozEvent) {
            let event = window.event || mozEvent;
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        });
    }
})();

let shouldSubmit = false;
$("#purchaseForm").on("submit", function (e) {
    if (!shouldSubmit) {
        e.preventDefault();
        shouldSubmit = true;
        document.getElementById("purchaseForm").submit();
        $("#savePurchase").attr("disabled", true);
    }
});

let timer = null;
$("#search_product").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: ROUTE_TRANSFER_AUTOCOMPLETE,
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
                                () => addPurchaseProductRow(item),
                                10
                            );
                        } else {
                            return {
                                label: item.product_name,
                                value: item.barcode,
                                id: item.id,
                                product_name: item.product_name,
                                batches: item.batches,
                            };
                        }
                    })
                );
            },
        });
    },
    minLength: 3,
    select: function (event, ui) {
        addPurchaseProductRow(ui.item);
        return false;
    },
    open: function () {
        $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
    },
    close: function () {
        $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
    },
});

function batchDropdownTemplate(item) {
    const options = item.batches.reduce((c, batch, index) => {
        return (
            c +
            `<option value="${batch.id}"
            data-quantity="${batch.available_quantity}"
            ${index == 0 ? "selected" : ""}>
            ${batch.batch_id} (${batch.available_quantity})
        </option>`
        );
    }, ``);

    return item.batches.length
        ? `<select class="form-control batch_id" name="purchase_product_id[${item.id}]" required>${options}</select>`
        : `<span style="color: red">Batch Not Found!</span>`;
}

function addPurchaseProductRow(item) {
    const productId = item.id;
    if (!productCounter.hasOwnProperty(productId)) {
        productCounter[productId] = 1;
    } else {
        const row = $("tr.pr-" + productId);
        const inpQty = row.find("input.quantity");

        if (inpQty.attr("max") >= inpQty.val() + 1) {
            const prevQty = Number(inpQty.val());
            inpQty.val(prevQty + 1);
            $("#search_product").val("");
        }
        return 0;
    }

    /*html*/
    const markup = `<tr class="pr-${productId}">
                        <td style="vertical-align: middle; padding-left: 10px;">
                            <input type="hidden"
                                value="${productId}"
                                class="product_id"
                                name="product_id[${productId}]"
                                data-barcode="${item.barcode}"
                                data-has-model="${item.has_model ? "1" : "0"}">
                            ${item.product_name}
                        </td>

                        <td class="text-center" class="tx-right tx-medium tx-inverse td-style">
                        ${batchDropdownTemplate(item)}
                        </td>

                        <td style="vertical-align: middle">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button"
                                        onclick="qtyDecrement(this,${productId})"
                                        style="cursor: pointer;"
                                        class="quantity-left-minus btn btn-danger btn-number"
                                        data-type="minus"
                                        data-field="">
                                            <span class="glyphicon glyphicon-minus"><i class="fa fa-minus"></i></span>
                                    </button>
                                </span>

                                <input oninput="transferQty(this, ${productId})"
                                    id="qty${productId}"
                                    style="width: 150px; margin: auto;"
                                    type="number"
                                    class="form-control quantity text-center"
                                    value="1"
                                    data-is-decimal="${item.is_decimal}"
                                    autocomplete="off"
                                    min="${1}"
                                    name="quantity[${productId}]">

                                <span class="input-group-btn">
                                    <button type="button"
                                        onclick="qtyIncrement(this,${productId})"
                                        style="cursor: pointer;"
                                        class="quantity-right-plus btn btn-success btn-number"
                                        data-type="plus"
                                        data-field="">
                                            <span class="glyphicon glyphicon-plus"><i class="fa fa-plus"></i></span>
                                    </button>
                                </span>
                            </div>
                        </td>

                        <td style="vertical-align: middle">
                            <button id="DeleteButton" type="button" class="btn btn-sm btn-danger">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>`;

    $("#search_product").val("");
    $("#mytab1").append(markup);
    $("#mytab1").find(`input.quantity`).trigger("input");
}

function total() {
    let sum = 0;
    $(".purchase_total").each(function () {
        sum += Number($(this).val());
    });
    return formatNumber(sum);
}

function due() {
    let total = $("#total").val();
    let paying_amount = $("#paying_amount").val();
    let due = total - paying_amount;
    $("#total_due").val(formatNumber(due));
}

$("#purchase_entry_table").on("click", "#DeleteButton", function () {
    const row = $(this).closest("tr");
    row.remove();
    calculatePurchaseRow();
});

function transferQty(el, id) {
    const row = $(el).closest("tr");
    const batchQuantity = row
        .find("select.batch_id option:selected")
        .data("quantity");
    const input = $(el);

    input.val(Math.min(input.val(), batchQuantity));

    if (
        input.val() > 0 &&
        !input.data("is-decimal") &&
        !_.isInteger(input.val())
    )
        input.val(parseInt(input.val()));

    calculatePurchaseRow(row);
}

function qtyIncrement(el, id) {
    const row = $(el).closest("tr");
    const quantity = row.find(`input.quantity`);
    const batchQuantity = row
        .find("select.batch_id option:selected")
        .data("quantity");

    if (Number(quantity.val()) + 1 <= batchQuantity) {
        quantity.val(Number(quantity.val()) + 1);
        transferQty(el, id);
    }
}

function qtyDecrement(el, id) {
    const tr = $(el).closest("tr");
    const transferQuantity = tr.find(`input.quantity`);
    let value = Number(transferQuantity.val()) - 1;
    if (value < 0) value = 0;
    transferQuantity.val(value);

    transferQty(el, id);
}

function formatNumber(n) {
    return Number(n) === n && n % 1 === 0
        ? Number(n)
        : Number(Number(n).toFixed(6));
}

function calculatePurchaseRow(row) {
    if (row) {
        const quantity = Number(row.find("input.quantity").val());
        const rate = Number(row.find("input.purchase_rate").val());
        const subTotal = formatNumber(quantity * rate);
        const otherCost = Number(row.find("input.purchase_other_cost").val());
        const totalCost = formatNumber(subTotal + otherCost);

        row.find(`input.purchase_subtotal`).val(subTotal);
        row.find(`input.purchase_total`).val(totalCost);
        row.find(`span.purchase_total`).text(totalCost);
    }

    $("#total").val(total());
    due();
}

$(document).on("input", ".purchase_subtotal", function () {
    const row = $(this).closest("tr");
    const subTotal = formatNumber(Number($(this).val()));
    const quantity = Number(row.find("input.quantity").val());
    const rate = formatNumber(subTotal / quantity);
    const otherCost = Number(row.find("input.purchase_other_cost").val());
    const totalCost = formatNumber(subTotal + otherCost);

    row.find(`input.purchase_rate`).val(rate);
    row.find(`input.purchase_total`).val(totalCost);
    row.find(`span.purchase_total`).text(totalCost);

    $("#total").val(total());
    due();
});

$(document).on("input", ".purchase_other_cost", function () {
    const row = $(this).closest("tr");
    const subTotal = Number(row.find("input.purchase_subtotal").val());
    const otherCost = Number($(this).val());
    const totalCost = formatNumber(subTotal + otherCost);

    row.find(`input.purchase_total`).val(totalCost);
    row.find(`span.purchase_total`).text(totalCost);

    $("#total").val(total());
    due();
});

$("body").on("change", "#pay_by", function () {
    let account_type = $(this).val();
    let _token = CSRF_TOKEN;
    $.post(
        ROUTE_PURCHASE_PAYEMENT_ACCOUNT,
        {
            _token: _token,
            account_type: account_type,
        },
        function (data) {
            $("#account_info").html(data);
            $(".select3").select2();
        }
    );
});

$("body").on("click", ".remove", function () {
    $(this)
        .closest(`.payment_information-${$(this).data("remove")}`)
        .remove();
    payingAmount();
});

function payingAmount() {
    let total_amount = $("input[name=total]").val();
    let sum = 0;
    $(".paying_amount").each(function () {
        sum += Number($(this).val());
    });
    $("#total_due").val(Number(total_amount) - Number(sum));
}

$("body").on("input", ".paying_amount", function () {
    payingAmount();
});

$("body").on("change", "select.batch_id", function () {
    const row = $(this).closest("tr");
    const input = row.find("input.quantity");
    const batchQuantity = $(this).find("option:selected").data("quantity");
    console.log(input.val(), batchQuantity);
    input.val(Math.min(input.val(), batchQuantity));
});
