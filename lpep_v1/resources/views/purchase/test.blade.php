@extends('layouts.dashboard')

@section('content')
    
<div class="row">
    <div class="col-md-6 m-auto">
        <br>
        <input type="text" class="form-control" id='employee_search'>
    </div>
</div>

@endsection

@section('script')
    <!-- Script -->
    <script type="text/javascript">

        // CSRF Token
        // var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            $( "#employee_search" ).autocomplete({
                source: "/data",
                minLength: 3,
                minLength: 3,
                select: function(event, ui) {
                    $('#employee_search').val(ui.item.value);
                }
	        });
        //   $( "#employee_search" ).autocomplete({
        //     source: function( request, response ) {
        //       // Fetch data
        //       $.ajax({
        //         url:"/data",
        //         type: 'get',
        //         dataType: "json",
        //         success: function( data ) {
        //            response( data.product_name );
        //         }
        //       });
        //     },
        //   });
    
        });
        </script>
@endsection