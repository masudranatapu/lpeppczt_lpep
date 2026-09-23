</div>

<!-- Modal -->
<div class="modal fade" id="summeryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="summeryContent">

        </div>
    </div>
</div>
<!-- END wrapper -->


<!-- jQuery  -->
<script src="{!! asset('backend') !!}/js/jquery.min.js"></script>
<script src="{!! asset('backend') !!}/js/popper.min.js"></script>
<script src="{!! asset('backend') !!}/js/bootstrap.min.js"></script>
<script src="{!! asset('backend') !!}/js/detect.js"></script>
<script src="{!! asset('backend') !!}/js/fastclick.js"></script>
<script src="{!! asset('backend') !!}/js/jquery.blockUI.js"></script>
<script src="{!! asset('backend') !!}/js/waves.js"></script>
<script src="{!! asset('backend') !!}/js/jquery.nicescroll.js"></script>
<script src="{!! asset('backend') !!}/js/jquery.slimscroll.js"></script>
<script src="{!! asset('backend') !!}/js/jquery.scrollTo.min.js"></script>

<!-- Required datatable js -->
<script src="{!! asset('backend') !!}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{!! asset('backend') !!}/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<!-- Buttons examples -->
<script src="{!! asset('backend') !!}/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="{!! asset('backend') !!}/plugins/datatables/buttons.bootstrap4.min.js"></script>
<script src="{!! asset('backend') !!}/plugins/datatables/jszip.min.js"></script>
<script src="{!! asset('backend') !!}/plugins/datatables/pdfmake.min.js"></script>
<script src="{!! asset('backend') !!}/plugins/datatables/vfs_fonts.js"></script>
<script src="{!! asset('backend') !!}/plugins/datatables/buttons.html5.min.js"></script>
<script src="{!! asset('backend') !!}/plugins/datatables/buttons.print.min.js"></script>
<!-- Responsive examples -->
<script src="{!! asset('backend') !!}/plugins/datatables/dataTables.responsive.min.js"></script>
<script src="{!! asset('backend') !!}/plugins/datatables/responsive.bootstrap4.min.js"></script>

<script src="{!! asset('backend') !!}/plugins/switchery/switchery.min.js"></script>
<script src="{!! asset('backend') !!}/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript" src="{!! asset('backend') !!}/plugins/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="{!! asset('backend') !!}/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
    integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>

{{-- <script type="text/javascript" src="{{asset('js/echarts.min.js')}}"></script> --}}

<!-- Dashboard init -->
{{-- <script src="{!! asset('backend') !!}/pages/jquery.dashboard.js"></script> --}}
<!--form wysiwig js-->
<script src="{!! asset('backend') !!}/plugins/tinymce/tinymce.min.js"></script>
<!-- App js -->
<script src="{!! asset('backend') !!}/js/jquery.core.js"></script>
<script src="{!! asset('backend') !!}/js/jquery.app.js"></script>

<script src="{{ asset('js/toastr.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('backend') }}/plugins/responsive-table/js/rwd-table.min.js""></script>
{{-- Sweetalert --}}
<script src="{{ asset('js/sweetalert.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<script src="{{ asset('js/lodash.min.js') }}"></script>

<script type="text/javascript">
    @if (session('message'))
        var alertType = "{{ session('type') }}";
        var message = "{{ session('message') }}";
        switch (alertType) {
            case "error":
                toastr.error(message)
                break;
            case "warning":
                toastr.warning(message)
                break;
            case "info":
                toastr.info(message)
                break;
            default:
                toastr.success(message)
                break;
        }
    @endif

    //Delete item
    $(document).on("click", "#delete", function(e) {
        e.preventDefault();
        var link = $(this).attr("href");
        swal({
                title: "Are you sure?",
                text: "This row will be permanently deleted!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location.href = link;
                    event.preventDefault();
                } else {
                    swal("Cancelled", "Your Data Is Safe :)", "error");
                }
            });
    });


    $(document).on("click", ".delete-button", function(e) {
        e.preventDefault();

        // Get the parent form of the clicked delete button
        const form = $(this).closest(".delete-form");

        // Show confirmation dialog
        swal({
            title: "Are you sure?",
            text: "This row will be permanently deleted!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                // Submit the form if user confirms
                form.submit();
            } else {
                // Display cancellation message
                swal("Cancelled", "Your data is safe :)", "error");
            }
        });
    });


    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#datatable').DataTable();

        //Buttons examples
        var table = $('#datatable-buttons').DataTable({
            // lengthChange: false,
            lengthMenu: [
                [25],
                ["All"]
            ],
            ordering: true,
            processing: true,
            buttons: ['csv', 'excel', 'pdf'],
            "bPaginate": false,
            "lengthChange": false
        });

        table.buttons().container()
            .appendTo('#datatable-buttons_wrapper .col-md-6:eq(1)');
    });

    // Select2
    $(".select2").select2({
        ordering: false,
    });
</script>

<script>
    $(document).ready(function() {
        $("body").on('click', "#summery", function() {
            $.get("{{ route('today.summary') }}", function(data) {
                $("#summeryContent").html(data)
                $("#summeryModal").modal('show')
            })
        })
    })
</script>

<script type="text/javascript">
    var url = "{{ route('changeLang') }}";

    $(".changeLang").change(function() {
        window.location.href = url + "?lang=" + $(this).val();
    });

    function verifyNumber(el, min = null, max = null) {
        const elm = $(el);
        let value = Number(elm.val().toString());

        if (isNaN(value)) {
            elm.val('');
        } else if (min != null) {
            if (value < Number(min)) {
                elm.val(min);
                toastr.error('Input can not be lower than ' + min);
            }
        }

        if (max != null) {
            if (Number(value) > Number(max)) {
                elm.val(max);
                toastr.error('Input can not be greater than ' + max);
            }
        }
    }

    function base64(el, callback) {
        if (el[0].files && el[0].files[0]) {

            const FR = new FileReader();

            FR.addEventListener("load", function(e) {
                callback(e.target.result)
            });

            FR.readAsDataURL(el[0].files[0]);
        }
    }

    const parseParams = (querystring) => {
        const params = new URLSearchParams(querystring);
        const obj = {};
        for (const key of params.keys()) {
            if (params.getAll(key).length > 1) {
                obj[key] = params.getAll(key);
            } else {
                obj[key] = params.get(key);
            }
        }
        return obj;
    };

    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        format: "yy-mm-dd"
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeSections = document.querySelectorAll('.row .d-flex');

        employeeSections.forEach(function(section) {
            const inputs = section.querySelectorAll('input[type="number"]');
            const totalInput = section.querySelector('input[name$="[total]"]');

            inputs.forEach(function(input) {
                if (!input.name.endsWith('[total]')) {
                    input.addEventListener('input', function() {
                        let total = 0;
                        inputs.forEach(function(field) {
                            if (!field.name.endsWith('[total]') && !isNaN(
                                    parseFloat(field.value))) {
                                total += parseFloat(field.value);
                            }
                        });

                        totalInput.value = total.toFixed(2);
                    });
                }
            });
        });
    });
</script>

@yield('script')

@stack('js')

</body>

</html>
