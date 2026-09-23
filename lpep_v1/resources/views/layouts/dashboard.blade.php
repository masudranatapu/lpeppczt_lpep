@include('inc.header')

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">

            @yield('content')

        </div> <!-- container -->

    </div> <!-- content -->

    <footer class="footer text-right">
        System Developed By <a href="https://www.l-pep.org/" class="text-success" >LPEP Renewable Energy Bangladesh Ltd.</a>
    </footer>

</div>



<!-- Right Sidebar -->
<div class="side-bar right-bar">
    <a href="javascript:void(0);" class="right-bar-toggle">
        <i class="mdi mdi-close-circle-outline"></i>
    </a>
    <h4 class="">{{ Auth::user()->name }}</h4>
        <div class=" notification-list nicescroll">
        <ul class="list-group list-no-border user-list">

            <li class="list-group-item">
                <a class="user-list-item">
                    <div class="icon bg-warning">
                        <i class="fa fa-language"></i>
                    </div>
                    <div class="user-desc">
                        <select class="name changeLang form-control" style="margin-top: 0px; height: 32px;">
                            <option value="en" {{ session()->get('locale') == 'en' ? 'selected' : '' }}>English
                            </option>
                            <option value="bn" {{ session()->get('locale') == 'bn' ? 'selected' : '' }}>Bangla
                            </option>
                        </select>
                        <span class="desc"></span>
                    </div>
                </a>
            </li>


            <li class="list-group-item">
                <a href="{{ route('change.password') }}" class="user-list-item">
                    <div class="icon bg-warning">
                        <i class="mdi mdi-settings"></i>
                    </div>
                    <div class="user-desc">
                        <span class="name">Change Password</span>
                        <span class="desc"></span>
                    </div>
                </a>
            </li>

            <li class="list-group-item">
                <a href="{{ route('logout') }}" class="user-list-item" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                    <div class="icon bg-warning">
                        <i class="dripicons-power"></i>
                    </div>
                    <div class="user-desc">
                        <span class="name">Logout</span>
                        <span class="desc"></span>
                    </div>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>




        </ul>
</div>
</div>
<!-- /Right-bar -->
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

@include('inc.footer')
