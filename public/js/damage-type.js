$(function () {
    $("#data-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: URL_DAMAGE_TYPE_LIST,
        columns: [
            {
                data: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            { data: "name", name: "name" },
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
});

$("body").on("click", "#addNew", function () {
    $.get(URL_DAMAGE_TYPE_CREATE, function (data) {
        $("#modalcontent").html(data);
        $("#unitModal").modal("show");
    });
});

$("body").on("submit", "#addDamageType", function (e) {
    e.preventDefault();
    let form = $(this).serialize();
    $.post(URL_DAMAGE_TYPE_STORE, form, function (data) {
        toastr.success("New Damage Add");
        var oTable = $("#data-table").dataTable();
        oTable.fnDraw(false);
        $("#unitModal").modal("hide");
    });
});

$("body").on("submit", "#updateDamageType", function (e) {
    e.preventDefault();
    let id = $("input[name=id]").val();
    let form = $(this).serialize();
    $.post(URL_DAMAGE_TYPE_UPDATE.replace(100, id), form, function (data) {
        toastr.success("Damage Type Update");
        var oTable = $("#data-table").dataTable();
        oTable.fnDraw(false);
        $("#unitModal").modal("hide");
    });
});

$("body").on("click", "#unitEdit", function () {
    let id = $(this).data("id");
    $.get(URL_DAMAGE_TYPE_EDIT.replace(100, id), function (data) {
        $("#modalcontent").html(data);
        $("#unitModal").modal("show");
    });
});

$("body").on("click", "#deleteData", function () {
    let id = $(this).data("id");
    swal({
        title: "Are you Want to Delete?",
        text: "Once Delete, This will be permanently Delete!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.get(URL_DAMAGE_TYPE_DELETE.replace(100, id), function (data) {
                toastr.success("Damage Delete");
                var oTable = $("#data-table").dataTable();
                oTable.fnDraw(false);
            }).fail((response) => {
                toastr.error(
                    "Can't delete .. because conflict with damage purchase"
                );
            });
        } else {
            swal("Cancelled", "Your Data Is Safe :)", "error");
        }
    });
});
