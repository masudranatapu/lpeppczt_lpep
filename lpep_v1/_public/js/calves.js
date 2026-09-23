function getData() {
    $("#data-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: URL_CALVES_JSON,
        order: [[0, "desc"]],
        columns: [
            {
                width: "8%",
                data: "DT_RowIndex",
                name: "id",
                orderable: true,
                searchable: false,
            },
            {
                width: "24%",
                data: "farm.name",
                name: "farm.name",
            },
            {
                width: "24%",
                data: "tag",
                name: "tag",
            },
            {
                width: "24%",
                data: "name",
                name: "name",
            },
            {
                width: "24%",
                data: "cattle.name",
                name: "cattle.name",
            },
            {
                width: "20%",
                data: "action",
                name: "action",
                orderable: false,
                searchable: true,
            },
        ],
        drawCallback: function (settings) {
            $("[data-toggle=popover]").popover();
        },
    });
}
getData();

$("body").on("click", "#addNew", function () {
    $.get(URL_CALVES_CREATE, function (data) {
        $("#modalContent").html(data);
        $("#tableModal").modal("show");

        $(".datepicker").datepicker({
            dateFormat: "yy-mm-dd",
        });
        window.select2Hook("#tableModal");
    });
});

$("body").on("click", "#submit", function (e) {
    e.preventDefault();
    console.log('Shakil');
    const calf_birth_problem_ids = [];
    $('input[name="calf_birth_problem_ids[]"]').each(function () {
        if ($(this).prop("checked")) {
            calf_birth_problem_ids.push($(this).val());
        }
    });

    const farm_id = $("select[name=farm_id]").val();
    const cattle_id = $("select[name=cattle_id]").val();
    const tag = $("input[name=tag]").val();
    const name = $("input[name=name]").val();
    const birth_date = $("input[name=birth_date]").val();
    const weight = $("input[name=weight]").val();
    const gender = $("select[name=gender]").val();

    if (!(farm_id && tag && name && birth_date && weight && gender)) return 0;

    let image = $("input[name=image]");
    if (image[0].files.length) {
        base64(image, submit);
    } else {
        submit(null);
    }

    function submit(b64) {
        $.ajax({
            url: URL_CALVES_STORE,
            data: {
                _token: TOKEN,
                user_id: AUTH_ID,
                farm_id: farm_id,
                cattle_id: cattle_id,
                tag: tag,
                name: name,
                birth_date: birth_date,
                weight: weight,
                gender: gender,
                calf_birth_problem_ids: JSON.stringify(calf_birth_problem_ids),
                image: b64,
            },
            method: "POST",
        }).then(function (data) {
            toastr.success(data);
            const oTable = $("#data-table").dataTable();
            oTable.fnDraw(false);
            $("#tableModal").modal("hide");
        });
    }
});

$("body").on("submit", "#calvesEditForm", function (e) {
    e.preventDefault();
    let serialized = parseParams($(this).serialize());
    if (
        !(
            serialized.tag &&
            serialized.name &&
            serialized.birth_date &&
            serialized.weight &&
            serialized.gender
        )
    )
        return 0;
    $.ajax({
        url: URL_CALVES_UPDATE.replace(
            "#id",
            $("input[name=calf_id]").val().toString()
        ),
        data: $(this).serialize(),
        method: "PUT",
    }).then(function (data) {
        toastr.success(data);
        const oTable = $("#data-table").dataTable();
        oTable.fnDraw(false);
        $("#tableModal").modal("hide");
    });
});

$("body").on("click", "#tableEdit", function () {
    let id = $(this).data("id");

    $.get(URL_CALVES_EDIT.replace("#id", id), function (data) {
        $("#modalContent").html(data);
        $("#tableModal").modal("show");

        $(".datepicker").datepicker({
            dateFormat: "yy-mm-dd",
        });
        window.select2Hook("#tableModal");
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
            $.ajax({
                url: URL_CALVES_DELETE.replace("#id", id),
                data: {
                    _token: TOKEN,
                },
                method: "DELETE",
            }).then(function (data) {
                toastr.success(data);
                const oTable = $("#data-table").dataTable();
                oTable.fnDraw(false);
            });
        } else {
            swal("Cancelled", "Your Data Is Safe :)", "error");
        }
    });
});

$("body").on("change", "select.customer_id", function () {
    const id = $(this).val();

    if (id < 1) return 0;

    const farmSelect = $("select.farm_id");
    farmSelect.empty();

    $.get(URL_ARMS_JSON_FIND_BY_CUSTOMER.replace("#id", id), function (res) {
        farmSelect.append(`<option value="">Select</option>`);
        res.data.forEach(function (item) {
            farmSelect.append(
                `<option value="${item.id}">${item.name}</option>`
            );
        });
    });
});

$("body").on("change", "select.farm_id", function () {
    const id = $(this).val();

    if (id < 1) return 0;

    const cattleSelect = $("select.cattle_id");
    cattleSelect.empty();

    $.get(URL_CATTLE_JSON_FIND_BY_FARM.replace("#id", id), function (res) {
        cattleSelect.append(`<option value="">Select</option>`);
        res.data.forEach(function (item) {
            cattleSelect.append(
                `<option value="${item.id}">${item.name}</option>`
            );
        });
    });
});
