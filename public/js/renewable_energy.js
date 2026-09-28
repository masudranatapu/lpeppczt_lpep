$(function () {
    const table = $("#data-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: ROUTE_RENEWABLE_ENERGY_JSON_ALL,
            data: function (d) {
                d.area_id = $('#filter_area_id').val();
                d.agent_id = $('#filter_agent_id').val();
                d.start_date = $('#filter_start_date').val();
                d.end_date = $('#filter_end_date').val();
            }
        },
        order: [[0, "desc"]],
        columns: [
            {
                width: "5%",
                data: "DT_RowIndex",
                name: "id",
                orderable: true,
                searchable: false,
            },
            {
                data: "type",
                name: "type",
                render: function(data) {
                    return data.charAt(0).toUpperCase() + data.slice(1);
                }
            },
            {
                data: "client_name",
                name: "client_name",
            },
            {
                data: "client_number",
                name: "client_number",
                defaultContent: 'N/A'
            },
            {
                data: "visit_date",
                name: "visit_date",
                render: function(data, type, row) {
                    if (!data || data === '0000-00-00') return 'N/A';
                    const hasTime = /[T ]\d{2}:\d{2}/.test(data);
                    if (!hasTime && !row.created_at) return data.split('-').reverse().join('-');
                    const date = new Date((hasTime ? data : (row.created_at || data)).replace(' ', 'T'));
                    if (Number.isNaN(date.getTime())) return data.split('-').reverse().join('-');
                    return date.toLocaleDateString('en-GB').replace(/\//g, '-') + ' ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                }
            },
            {
                data: "area.name",
                name: "area.name",
            },
            {
                data: "agent.name",
                name: "agent.name",
                defaultContent: 'N/A'
            },
            {
                data: "mobile_no",
                name: "mobile_no",
                defaultContent: 'N/A'
            },
            {
                data: "division.name",
                name: "division.name",
                defaultContent: 'N/A'
            },
            {
                data: "district.name",
                name: "district.name",
                defaultContent: 'N/A'
            },
            {
                data: "upazila.name",
                name: "upazila.name",
                defaultContent: 'N/A'
            },
            {
                data: "union.name",
                name: "union.name",
                defaultContent: 'N/A'
            },
            {
                data: "ward_number",
                name: "ward_number",
                defaultContent: 'N/A'
            },
            {
                data: "village",
                name: "village",
                defaultContent: 'N/A'
            },
            {
                data: "livestock_details",
                name: "livestock_details",
                defaultContent: 'N/A'
            },
            {
                data: "po_name",
                name: "po_name",
                defaultContent: 'N/A'
            },
            {
                data: "plant_size",
                name: "plant_size",
                render: function(data, type, row) {
                    return data ? data + ' ' + row.plant_size_unit : 'N/A';
                }
            },
            {
                data: "document",
                name: "document",
                render: function(data) {
                    if (data) {
                        return `<a href="/${data}" download class="btn btn-xs btn-primary"><i class="fa fa-file"></i></a>`;
                    }
                    return 'N/A';
                }
            },
            {
                data: "plant_start_date",
                name: "plant_start_date",
                render: function(data) {
                    if (!data || data === '0000-00-00') return 'N/A';
                    return data.split('-').reverse().join('-');
                }
            },
            {
                data: "plant_end_date",
                name: "plant_end_date",
                render: function(data) {
                    if (!data || data === '0000-00-00') return 'N/A';
                    return data.split('-').reverse().join('-');
                }
            },
            {
                data: "remarks",
                name: "remarks",
                defaultContent: 'N/A'
            },
            {
                width: "15%",
                data: "action",
                name: "action",
                orderable: false,
                searchable: true,
            },
        ],
        dom: 'Blfrtip',
        buttons: ["excel", "pdf", "print"],
        drawCallback: function (settings) {
            $("[data-toggle=popover]").popover();
        },
    });

    table.buttons().container().appendTo('#buttons-container');

    $('#filter_btn').on('click', function() {
        table.draw();
    });

    $('#reset_btn').on('click', function() {
        $('#filter-form')[0].reset();
        $('.select2').val('').trigger('change');
        table.draw();
    });
});

$(document).ready(function () {
    $("body").on("click", "#viewData", function () {
        let id = $(this).data("id");
        $.get(ROUTE_RENEWABLE_ENERGY_SHOW.replace("#id", id), function (data) {
            $("#modalContent").html(data);
            $("#tableModal").modal("show");
        });
    });

    $("body").on("click", "#addNew", function () {
        $.get(ROUTE_RENEWABLE_ENERGY_CREATE, function (data) {
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
        $.get(ROUTE_RENEWABLE_ENERGY_EDIT.replace("#id", id), function (data) {
            $("#modalContent").html(data);
            $("#tableModal").modal("show");
            $(".datepicker").datepicker({
                dateFormat: "yy-mm-dd",
            });
            window.select2Hook("#tableModal");
        });
    });

    $("body").on("submit", "#renewable-energy-form-store", function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this)[0].action,
            data: new FormData(this),
            method: "POST",
            processData: false,
            contentType: false,
        })
            .then(function (data) {
                toastr.success(data);
                const oTable = $("#data-table").dataTable();
                oTable.fnDraw(false);
                $("#tableModal").modal("hide");
            })
            .catch((error) => {
                toastr.error(error.responseJSON.error || "Something went wrong!");
            });
    });

    $("body").on("submit", "#renewable-energy-form-update", function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this)[0].action,
            data: new FormData(this),
            method: "POST",
            processData: false,
            contentType: false,
        })
            .then(function (data) {
                toastr.success(data);
                const oTable = $("#data-table").dataTable();
                oTable.fnDraw(false);
                $("#tableModal").modal("hide");
            })
            .catch((error) => {
                toastr.error(error.responseJSON.error || "Something went wrong!");
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
                    url: ROUTE_RENEWABLE_ENERGY_DESTROY.replace("#id", id),
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

    // Geocode handling
    $("body").on("change", "select.division_id", function () {
        const id = $(this).val();
        if (id < 1) return 0;
        const districtSelect = $("select.district_id");
        districtSelect.empty();
        $.get(ROUTE_DISTRICTS_FIND_BY_DIVISION.replace("#id", id), function (res) {
            districtSelect.append(`<option value="">Select</option>`);
            res.data.forEach(function (item) {
                districtSelect.append(`<option value="${item.id}">${item.name}</option>`);
            });
        });
    });

    $("body").on("change", "select.district_id", function () {
        const id = $(this).val();
        if (id < 1) return 0;
        const upazilaSelect = $("select.upazila_id");
        upazilaSelect.empty();
        $.get(ROUTE_UPAZILAS_FIND_BY_DISTRICT.replace("#id", id), function (res) {
            upazilaSelect.append(`<option value="">Select</option>`);
            res.data.forEach(function (item) {
                upazilaSelect.append(`<option value="${item.id}">${item.name}</option>`);
            });
        });
    });

    $("body").on("change", "select.upazila_id", function () {
        const id = $(this).val();
        if (id < 1) return 0;
        const unionSelect = $("select.union_id");
        unionSelect.empty();
        $.get(ROUTE_UNIONS_FIND_BY_UPAZILA.replace("#id", id), function (res) {
            unionSelect.append(`<option value="">Select</option>`);
            res.data.forEach(function (item) {
                unionSelect.append(`<option value="${item.id}">${item.name}</option>`);
            });
        });
    });
});
