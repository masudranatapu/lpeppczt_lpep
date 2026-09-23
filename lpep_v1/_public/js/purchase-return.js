function returnQuantity(purchase_product_id, purchase_quantity) {
    let return_quantity = $(`#return_quantity${purchase_product_id}`);
    let price = $(`#price${purchase_product_id}`).val();
    let current_stock = $(`#return_quantity${purchase_product_id}`).data(
        "stock_quantity"
    );
    if (Number(current_stock) < Number(return_quantity.val())) {
        alert(
            `Can't return more than stock quantity! Your current stock = ${current_stock}`
        );
        return_quantity.val(0);
    }
    if (Number(purchase_quantity) < Number(return_quantity.val())) {
        alert("Can't return more than purchase quantity");
        return_quantity.val(0);
    } else {
        let return_price = Number(price) * Number(return_quantity.val());
        $(`#returnTotal${purchase_product_id}`).text(return_price);
        $(`.returnTotalPice${purchase_product_id}`).val(return_price);
    }
    total();
}

function total() {
    var sum = 0;
    $(".returnprice").each(function () {
        var price = $(this);
        sum += Number(price.val());
    });
    $("input[name=sale_return_amount]").val(sum);
}

function returnPrice(purchase_product_id, purchase_quantity) {
    returnQuantity(purchase_product_id, purchase_quantity);
}

$("body").on("change", "#pay_by", function () {
    let account_type = $(this).val();
    account(account_type);
});

function account(account_type, account_id = "") {
    $.post(
        URL_PAYMENT_ACCOUNT,
        { _token: TOKEN, account_type: account_type },
        function (data) {
            $("#account_list").html(data);
            $(".select3").val(account_id);
            $(".select3").select2();
        }
    );
}

$("body").on("click", "#deleteReturnPurchase", function () {
    let id = $(this).data("id");
    swal({
        title: "Are you Want to Delete?",
        text: "Once Delete, This will be permanently Delete!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.post(
                URL_PURCHASE_RETURN_DELETE,
                { _token: TOKEN, id: id },
                function (data) {
                    toastr.success("Delete Successfully");
                    var oTable = $("#data-table").dataTable();
                    oTable.fnDraw(false);
                }
            ).fail((response) => {
                toastr.error("Failed To Delete");
            });
        } else {
            swal("Cancelled", "Your Data Is Safe :)", "error");
        }
    });
});

$(function () {
    if (IS_RETURN_LIST_CALL) {
        $("#data-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: URL_RETURN_LIST,
            columns: [
                {
                    data: "DT_RowIndex",
                    orderable: false,
                    searchable: false,
                },
                { data: "id", name: "id" },
                { data: "date", name: "date" },
                {
                    data: "purchase_return_type.name",
                    name: "purchase_return_type.name",
                },
                { data: "business.name", name: "business.name" },
                { data: "created", name: "created" },
                { data: "updated", name: "updated" },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: true,
                },
            ],
        });
    }
});
