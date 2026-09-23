$(function () {
    $("#data-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: ROUTE_CATTLE_JSON_ALL,
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
});
function handleSubmitUpdate(event) {
    event.preventDefault();
    const disease_history_ids = [];
    $('input[name="disease_history_ids[]"]').each(function () {
        if ($(this).prop("checked")) {
            disease_history_ids.push($(this).val());
        }
    });

    const health_info_ids = [];
    $('input[name="health_info_ids[]"]').each(function () {
        if ($(this).prop("checked")) {
            health_info_ids.push($(this).val());
        }
    });

    const id = $("input[name=cattle_id]").val();
    const farm_id = $("select[name=farm_id]").val();
    const app_customer_id = $("select[name=app_customer_id]").val();
    const tag = $("input[name=tag]").val();
    const name = $("input[name=name]").val();
    const cattle_group_id = $("select[name=cattle_group_id]").val();
    const cattle_breed_id = $("select[name=cattle_breed_id]").val();
    const birth_date = $("input[name=birth_date]").val();
    const weight = $("input[name=weight]").val();
    const gender = $("select[name=gender]").val();
    const health_problem = $("select[name=health_problem]").val();
    const avg_milk_production = $("select[name=avg_milk_production]").val();
    const milk_production_status = $(
        "select[name=milk_production_status]"
    ).val();
    const calf_count = $("select[name=calf_count]").val();
    const last_calf_birth_date = $(
        "input[name=last_calf_birth_date]"
    ).val();
    const genetic_percentage = $("select[name=genetic_percentage]").val();

    const insurance_company_id = $(
        "select[name=insurance_company_id]"
    ).val();
    const insurance_type_id = $("select[name=insurance_type_id]").val();
    const insurance_no = $("input[name=insurance_no]").val();

    if (
        !(
            farm_id &&
            app_customer_id &&
            tag &&
            name &&
            cattle_group_id &&
            cattle_breed_id &&
            birth_date &&
            weight &&
            gender &&
            health_problem &&
            insurance_company_id &&
            insurance_type_id &&
            insurance_no
        )
    )
        return 0;

    $.ajax({
        url: ROUTE_CATTLE_UPDATE.replace("#id", id),
        data: {
            _token: CSRF_TOKEN,
            farm_id: farm_id,
            app_customer_id: app_customer_id,
            tag: tag,
            name: name,
            cattle_group_id: cattle_group_id,
            cattle_breed_id: cattle_breed_id,
            birth_date: birth_date,
            weight: weight,
            gender: gender,
            health_problem: health_problem,
            avg_milk_production: avg_milk_production,
            milk_production_status: milk_production_status,
            calf_count: calf_count,
            last_calf_birth_date: last_calf_birth_date,
            genetic_percentage: genetic_percentage,
            insurance_company_id: insurance_company_id,
            insurance_type_id: insurance_type_id,
            insurance_no: insurance_no,
            disease_history_ids: JSON.stringify(disease_history_ids),
            health_info_ids: JSON.stringify(health_info_ids),
        },
        method: "PUT",
    }).then(function (data) {
        toastr.success(data);
        const oTable = $("#data-table").dataTable();
        oTable.fnDraw(false);
        $("#tableModal").modal("hide");
    });
}
function handleSubmit(event) {
    event.preventDefault();
    const disease_history_ids = [];
    $('input[name="disease_history_ids[]"]').each(function () {
        if ($(this).prop("checked")) {
            disease_history_ids.push($(this).val());
        }
    });

    const health_info_ids = [];
    $('input[name="health_info_ids[]"]').each(function () {
        if ($(this).prop("checked")) {
            health_info_ids.push($(this).val());
        }
    });

    const farm_id = $("select[name=farm_id]").val();

    const tag = $("input[name=tag]").val();
    const app_customer_id = $("select[name=app_customer_id]").val();
    const name = $("input[name=name]").val();
    const cattle_group_id = $("select[name=cattle_group_id]").val();
    const cattle_breed_id = $("select[name=cattle_breed_id]").val();
    const birth_date = $("input[name=birth_date]").val();
    const weight = $("input[name=weight]").val();
    const gender = $("select[name=gender]").val();
    const health_problem = $("select[name=health_problem]").val();
    const avg_milk_production = $("select[name=avg_milk_production]").val();
    const milk_production_status = $(
        "select[name=milk_production_status]"
    ).val();
    const calf_count = $("select[name=calf_count]").val();
    const last_calf_birth_date = $(
        "input[name=last_calf_birth_date]"
    ).val();
    const genetic_percentage = $("select[name=genetic_percentage]").val();

    const insurance_company_id = $(
        "select[name=insurance_company_id]"
    ).val();
    const insurance_type_id = $("select[name=insurance_type_id]").val();
    const insurance_no = $("input[name=insurance_no]").val();

    if (
        !(
            farm_id &&
            app_customer_id &&
            tag &&
            name &&
            cattle_group_id &&
            cattle_breed_id &&
            birth_date &&
            weight &&
            gender &&
            health_problem &&
            insurance_company_id &&
            insurance_type_id &&
            insurance_no
        )
    )
        return 0;

    $.ajax({
        url: ROUTE_CATTLE_STORE,
        data: {
            _token: CSRF_TOKEN,
            farm_id: farm_id,
            app_customer_id: app_customer_id,
            tag: tag,
            name: name,
            cattle_group_id: cattle_group_id,
            cattle_breed_id: cattle_breed_id,
            birth_date: birth_date,
            weight: weight,
            gender: gender,
            health_problem: health_problem,
            avg_milk_production: avg_milk_production,
            milk_production_status: milk_production_status,
            calf_count: calf_count,
            last_calf_birth_date: last_calf_birth_date,
            genetic_percentage: genetic_percentage,
            insurance_company_id: insurance_company_id,
            insurance_type_id: insurance_type_id,
            insurance_no: insurance_no,
            disease_history_ids: JSON.stringify(disease_history_ids),
            health_info_ids: JSON.stringify(health_info_ids),
        },
        method: "POST",
    }).then(function (data) {
        toastr.success(data);
        const oTable = $("#data-table").dataTable();
        oTable.fnDraw(false);
        $("#tableModal").modal("hide");
    });
}
$(document).ready(function () {
    $("body").on("click", "#addNew", function () {
        $.get(ROUTE_CATTLE_CREATE, function (data) {
            $("#modalContent").html(data);
            $("#tableModal").modal("show");
            $(".datepicker").datepicker({
                dateFormat: "yy-mm-dd",
            });
            window.select2Hook("#tableModal");
        });
    });

    $("body").on("click", "#tableEdit", function () {
        let id = $(this).data("id");

        $.get(ROUTE_CATTLE_EDIT.replace("#id", id), function (data) {
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
            title: "Are you sure?",
            text: "You will delete this record permanently!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: ROUTE_CATTLE_DESTROY.replace("#id", id),
                    data: { _token: CSRF_TOKEN },
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

    $("body").on("change", ".select3", function () {
        const id = $(this).val();
        if (id < 1) return 0;

        const farmSelect = $("select.farm_id");
        farmSelect.empty();

        $.get(ROUTE_FARMS_FIND_BY_CUSTOMER.replace("#id", id), function (res) {
            farmSelect.append(`<option value="">Select</option>`);
            res.data.forEach(function (item) {
                farmSelect.append(
                    `<option value="${item.id}">${item.name}</option>`
                );
            });
        });
    });
});
