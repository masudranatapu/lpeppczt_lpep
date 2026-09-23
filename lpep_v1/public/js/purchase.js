const productCounter = {};

(function () {
    var textField = document.getElementById("search_product");

    if (textField) {
        textField.addEventListener("keydown", function (mozEvent) {
            var event = window.event || mozEvent;
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
            url: "/autocomplete/purchase?origin=" + $("#product_origin").val(),
            dataType: "json",
            data: {
                q: request.term,
            },
            success: function (data) {
                response(
                    $.map(data, function (item) {
                        if (data.length == 1) {
                            addPurchaseProductRow(item);
                        } else {
                            return {
                                label: item.product_name,
                                value: item.barcode,
                                id: item.id,
                                is_medicine: item.is_medicine,
                                product_name: item.product_name,
                                barcode: item.barcode,
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

const TABLE_REGULAR_PURCHASE = "#regular-purchase";
const TABLE_MEDICINE_PURCHASE = "#medicine-purchase";

function addPurchaseProductRow(item) {
    const productId = item.id;
    if (!productCounter.hasOwnProperty(productId)) {
        productCounter[productId] = Math.max(
            0,
            PRODUCTS.filter((p) => p.id == productId).length
        );
    } else if (productCounter[productId] > 0) {
        const purchaseQuantity = $(
            `tr.pr-${productId}-${productCounter[productId]}`
        ).find("input.purchase_quantity");
        purchaseQuantity.val(Number(purchaseQuantity.val()) + 1);
        purchaseQuantity.trigger("input");
        $("#search_product").val("");
        return 0;
    }

    productCounter[productId] += 1;

    const counter = productCounter[productId];

    const markup = item.is_medicine
        ? medicineProductRowMarkup(productId, counter, item)
        : regularProductRowMarkup(productId, counter, item);

    $("#search_product").val("");
    $(item.is_medicine ? TABLE_MEDICINE_PURCHASE : TABLE_REGULAR_PURCHASE)
        .find("tbody")
        .append(markup);
    $(item.is_medicine ? TABLE_MEDICINE_PURCHASE : TABLE_REGULAR_PURCHASE)
        .find(`input.purchase_quantity`)
        .trigger("input");
    $(item.is_medicine ? TABLE_MEDICINE_PURCHASE : TABLE_REGULAR_PURCHASE).css(
        "display",
        "block"
    );

    if (item.is_medicine) {
        $(TABLE_MEDICINE_PURCHASE)
            .find("tbody")
            .find("tr:last")
            .find(".expiry_date")
            .datepicker({
                autoclose: true,
                todayHighlight: true,
                dateFormat: "yyyy-MM-dd",
                format: "yyyy-mm-dd",
            });
    }
}

function regularProductRowMarkup(productId, counter, item) {
    /*html*/
    return `<tr data-id="${productId}" class="pr-${productId}-${counter}" style="font-size: 13px; ">
                <td style="vertical-align: middle; padding: 8px;">
                    <input type="hidden"
                        value="${productId}"
                        class="product_id"
                        name="reg_product_id[${productId}-${counter}]"
                        data-counter="${counter}"
                        data-barcode="${item.barcode}"
                        data-is-medicine="${item.is_medicine ? "1" : "0"}">
                    ${item.product_name}
                </td>

                <td style="vertical-align: middle">
                    <input type="text"
                        style="width: 120px; margin: auto;"
                        class="form-control text-center batch_id"
                        value=""
                        autocomplete="off"
                        placeholder="Batch Id"
                        oninput="doesBatchIdExist(this)"
                        name="reg_batch_id[${productId}-${counter}]"
                        required>
                </td>

                <td style="vertical-align: middle">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button"
                                onclick="purchaseQtyDecrement(this,${productId})"
                                style="cursor: pointer; padding: 8px; height: 94%; margin-top: 1px;"
                                class="quantity-left-minus btn btn-danger btn-number"
                                data-type="minus"
                                data-field="">
                                    <span class="glyphicon glyphicon-minus"><i class="fa fa-minus"></i></span>
                            </button>
                        </span>

                        <input oninput="purchaseQty(this, ${productId})"
                            id="qty${productId}-${counter}"
                            style="width: 120px; margin: auto;"
                            type="text"
                            class="form-control purchase_quantity text-center"
                            value="1"
                            data-is-decimal="${item.is_decimal}"
                            autocomplete="off"
                            name="reg_purchase_quantity[${productId}-${counter}]">

                        <span class="input-group-btn">
                            <button type="button"
                                onclick="purchaseQtyIncrement(this,${productId})"
                                style="cursor: pointer; padding: 8px; height: 94%; margin-top: 1px;"
                                class="quantity-right-plus btn btn-success btn-number"
                                data-type="plus"
                                data-field="">
                                    <span class="glyphicon glyphicon-plus"><i class="fa fa-plus"></i></span>
                            </button>
                        </span>
                    </div>
                </td>

                <td style="vertical-align: middle">
                    <input oninput="purchasePrice(this)"
                        type="text"
                        id="purchase_price${productId}"
                        style="width: 120px; margin: auto;"
                        class="form-control text-center purchase_rate"
                        value="${item.purchase_price}"
                        autocomplete="off"
                        name="reg_purchase_price[${productId}-${counter}]">
                </td>

                <td style="vertical-align: middle">
                    <input type="text"
                        style="width: 120px; margin: auto;"
                        class="form-control text-center purchase_subtotal"
                        value="${item.purchase_price}"
                        autocomplete="off"
                        name="reg_purchase_subtotal[${productId}-${counter}]">
                </td>

                <td style="vertical-align: middle">
                    <input type="text"
                        style="width: 120px; margin: auto;"
                        class="form-control text-center purchase_other_cost"
                        value="0"
                        autocomplete="off"
                        name="reg_purchase_other_cost[${productId}-${counter}]">
                </td>

                <td style="vertical-align: middle;">
                    <div style="width: 80px">
                        <span class="text-center purchase_total">
                        0
                        </span>
                        <input style="margin: auto;"
                            type="hidden"
                            class="form-control purchase_total"
                            value="${item.purchase_price}"
                            name="reg_purchase_total[${productId}-${counter}]">
                    </div>
                </td>

                <td style="vertical-align: middle;">
                    <button id="DeleteButton" type="button" class="btn btn-sm btn-danger">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>`;
}

function medicineProductRowMarkup(productId, counter, item) {
    /*html*/
    return `<tr data-id="${productId}" class="pr-${productId}-${counter}" style="font-size: 13px; ">
                <td style="vertical-align: middle; padding: 8px;">
                    <input type="hidden"
                        value="${productId}"
                        class="product_id"
                        name="med_product_id[${productId}-${counter}]"
                        data-counter="${counter}"
                        data-barcode="${item.barcode}"
                        data-is-medicine="${item.is_medicine ? "1" : "0"}">
                    ${item.product_name}
                </td>

                <td style="vertical-align: middle">
                    <input type="text"
                        class="form-control text-center batch_id"
                        autocomplete="off"
                        placeholder="Batch Id"
                        oninput="doesBatchIdExist(this)"
                        name="med_batch_id[${productId}-${counter}]"
                        value=""
                        required>
                </td>

                <td style="vertical-align: middle">
                    <input type="text"
                        class="form-control text-center expiry_date"
                        value=""
                        autocomplete="off"
                        placeholder="Expiry Date"
                        name="med_expiry_date[${productId}-${counter}]"
                        required>
                </td>

                <td style="vertical-align: middle">
                    <select class="form-control box_pattern"
                        style="width: 115px; margin: auto;"
                        name="med_box_pattern[${productId}-${counter}]"
                        required>
                        ${BOX_PATTERNS.map(
                            (b) =>
                                `<option value="${b.id}" data-quantity="${b.quantity}">${b.name}</option>`
                        ).join("")}
                    </select>
                </td>

                <td style="vertical-align: middle">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button"
                                onclick="purchaseQtyDecrement(this,${productId})"
                                style="cursor: pointer; padding: 8px; height: 94%; margin-top: 1px;"
                                class="quantity-left-minus btn btn-danger btn-number"
                                data-type="minus"
                                data-field="">
                                    <span class="glyphicon glyphicon-minus"><i class="fa fa-minus"></i></span>
                            </button>
                        </span>

                        <input oninput="purchaseQty(this, ${productId})"
                            id="qty${productId}-${counter}"
                            style="width: 80px; margin: auto;"
                            type="text"
                            class="form-control purchase_quantity text-center"
                            value="1"
                            data-is-decimal="${item.is_decimal}"
                            autocomplete="off"
                            name="med_box_pattern_quantity[${productId}-${counter}]">

                        <span class="input-group-btn">
                            <button type="button"
                                onclick="purchaseQtyIncrement(this,${productId})"
                                style="cursor: pointer; padding: 8px; height: 94%; margin-top: 1px;"
                                class="quantity-right-plus btn btn-success btn-number"
                                data-type="plus"
                                data-field="">
                                    <span class="glyphicon glyphicon-plus"><i class="fa fa-plus"></i></span>
                            </button>
                        </span>
                    </div>
                </td>

                <td>
                    <input
                        style="width: 90px; margin: auto;"
                        type="text"
                        class="form-control text-center multiplied-quantity"
                        value="1"
                        disabled>
                </td>

                <td style="vertical-align: middle">
                    <input oninput="purchasePrice(this)"
                        type="text"
                        id="purchase_price${productId}"
                        style="width: 110px; margin: auto;"
                        class="form-control text-center purchase_rate"
                        value="${item.purchase_price}"
                        autocomplete="off"
                        name="med_purchase_price[${productId}-${counter}]">
                </td>

                <td style="vertical-align: middle">
                    <input type="text"
                        style="width: 110px; margin: auto;"
                        class="form-control text-center purchase_subtotal"
                        value="${item.purchase_price}"
                        autocomplete="off"
                        name="med_purchase_subtotal[${productId}-${counter}]">
                </td>

                <td style="vertical-align: middle">
                    <input type="text"
                        style="width: 110px; margin: auto;"
                        class="form-control text-center purchase_other_cost"
                        value="0"
                        autocomplete="off"
                        name="med_purchase_other_cost[${productId}-${counter}]">
                </td>

                <td style="vertical-align: middle;">
                    <div style="width: 80px">
                        <span class="text-center purchase_total">
                        0
                        </span>
                        <input style="margin: auto;"
                            type="hidden"
                            class="form-control purchase_total"
                            value="${item.purchase_price}"
                            name="med_purchase_total[${productId}-${counter}]">
                    </div>
                </td>

                <td style="vertical-align: middle;">
                    <button id="DeleteButton" type="button" class="btn btn-sm btn-danger">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>`;
}

function doesBatchIdExist(el) {
    $.get(ROUTE_DOES_BATCH_ID_EXIST + "?batch_id=" + $(el).val()).then(
        (res) => {
            if (res.exists) {
                $(el).val("");
                toastr.error("Batch Id Already Exists");
            }
        }
    );
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

$("table").on("click", "#DeleteButton", function () {
    const row = $(this).closest("tr");
    if (row.closest("tbody").find("tr").length == 1) {
        row.closest(".table-responsive").css("display", "none");
    }
    productCounter[row.data("id")] -= 1;
    row.remove();
    calculatePurchaseRow();
});

function purchaseQty(el, id) {
    const row = $(el).closest("tr");
    const hiddenInput = row.find("input.product_id");
    const input = $(el);
    if (
        input.val() > 0 &&
        !input.data("is-decimal") &&
        !_.isInteger(input.val())
    )
        input.val(parseInt(input.val()));

    calculatePurchaseRow(row);

    if (hiddenInput.data("has-model")) {
        syncModelRows(row, hiddenInput, id);
    }
}

function purchaseQtyIncrement(el, id) {
    const tr = $(el).closest("tr");
    const purchaseQuantity = tr.find(`input.purchase_quantity`);
    purchaseQuantity.val(Number(purchaseQuantity.val()) + 1);

    purchaseQty(el, id);
}

function purchaseQtyDecrement(el, id) {
    const tr = $(el).closest("tr");
    const purchaseQuantity = tr.find(`input.purchase_quantity`);
    let value = Number(purchaseQuantity.val()) - 1;
    if (value < 0) value = 0;
    purchaseQuantity.val(value);

    purchaseQty(el, id);
}

function purchasePrice(el) {
    calculatePurchaseRow($(el).closest("tr"));
}

function formatNumber(n) {
    return Number(n) === n && n % 1 === 0
        ? Number(n)
        : Number(Number(n).toFixed(2));
}

function calculatePurchaseRow(row) {
    if (row) {
        const quantity = Number(row.find("input.purchase_quantity").val());
        const rate = Number(row.find("input.purchase_rate").val());
        const subTotal = formatNumber(quantity * rate);
        const otherCost = Number(row.find("input.purchase_other_cost").val());
        const totalCost = formatNumber(subTotal + otherCost);

        const boxPattern = row.find(".box_pattern");
        if (boxPattern.length) {
            row.find(".multiplied-quantity").val(
                formatNumber(
                    quantity *
                        $(".box_pattern option:selected").data("quantity")
                )
            );
        }

        row.find(`input.purchase_subtotal`).val(subTotal);
        row.find(`input.purchase_total`).val(totalCost);
        row.find(`span.purchase_total`).text(totalCost);
    }

    $("#total").val(total());
    due();
}

$(document).on("input", "select.box_pattern", function () {
    const row = $(this).closest("tr");
    row.find("input.purchase_quantity").trigger("input");
});

$(document).on("input", ".purchase_subtotal", function () {
    const row = $(this).closest("tr");
    const subTotal = formatNumber(Number($(this).val()));
    const quantity = Number(row.find("input.purchase_quantity").val());
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
        ROUTE_PURCHASE_PAYMENT_ACCOUNT,
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

$(document).on("change", "#product_origin", function () {
    $("select.supplier-id").empty();
    const type = $(this).val().toString().toLowerCase();
    $("select.supplier-id").select2({
        width: "100%",
        ajax: {
            url: "/contact/ajax?type=" + type,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page,
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items.filter((it) =>
                        type == "customer" ? it.text != "Guest" : true
                    ),
                    pagination: {
                        more: data.pagination.more,
                    },
                };
            },
            cache: true,
        },
        allowClear: true,
        placeholder: "Search Seller",
        minimumInputLength: 0,
    });
});
