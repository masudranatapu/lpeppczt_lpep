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

    window.select2Hook();
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
            url: "/autocomplete/purchase",
            dataType: "json",
            data: {
                q: request.term,
            },
            success: function (data) {
                response(
                    $.map(data, function (item) {
                        if (data.length == 1 && !item.is_medicine) {
                            addPurchaseProductRow(item);
                        } else {
                            return {
                                label: item.product_name,
                                value: item.barcode,
                                id: item.id,
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
    const markup = regularProductRowMarkup(productId, counter, item);

    $("#search_product").val("");
    $(TABLE_REGULAR_PURCHASE).find("tbody").append(markup);
    $(TABLE_REGULAR_PURCHASE).find(`input.purchase_quantity`).trigger("input");
    $(TABLE_REGULAR_PURCHASE).css("display", "block");
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
                        data-barcode="${item.barcode}">
                    ${item.product_name}
                </td>

                <td style="vertical-align: middle">
                        <input
                            style="width: 120px; margin: auto;"
                            type="text"
                            class="form-control batch_id text-center"
                            autocomplete="off"
                            oninput="doesBatchIdExist(this)"
                            name="reg_batch_id[${productId}-${counter}]"
                            placeholder="Batch Id">
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
    const input = $(el);
    if (
        input.val() > 0 &&
        !input.data("is-decimal") &&
        !_.isInteger(input.val())
    )
        input.val(parseInt(input.val()));

    calculatePurchaseRow(row);
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

function purchaseTotal() {
    let sum = 0;
    $(".purchase_total").each(function () {
        sum += Number($(this).val());
    });

    $("#total").val(sum);
    if (sum > Number($("#total-loan").val())) {
        $("#savePurchase").attr("disabled", true);
    } else {
        $("#savePurchase").attr("disabled", false);
    }
}

function calculatePurchaseRow(row) {
    if (row) {
        const quantity = Number(row.find("input.purchase_quantity").val());
        const rate = Number(row.find("input.purchase_rate").val());
        const totalCost = formatNumber(quantity * rate);

        row.find(`input.purchase_total`).val(totalCost);
        row.find(`span.purchase_total`).text(totalCost);
    }

    purchaseTotal();
}

$("#app_customer_id").on("change", function () {
    $.get(
        ROUTE_CUSTOMER_TOTAL_LOANS.replace("0", $(this).val()),
        function (res) {
            $("#total-loan").val(res.amount);
            purchaseTotal();
        }
    );
});
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
