<style>
    @php
        $color = $business_setting->color;
    @endphp #sidebar-menu>ul>li>a.active {
        border-left: 3px solid {{ $color }};
        color: {{ $color }} !important;
        background: #e9f7f0;
    }

    #sidebar-menu .subdrop {
        border-left: 3px solid {{ $color }};
        color: {{ $color }} !important;
    }

    #sidebar-menu ul ul li.active a {
        color: {{ $color }}
    }

    #sidebar-menu>ul>li>a:hover {
        color: {{ $color }}
    }

    .theme-color-set {
        border-top: 3px solid {{ $color }} !important;
    }

    .card-box {
        border-top: 3px solid {{ $color }} !important;
    }

    .navbar-default {
        border-top: 3px solid {{ $color }};
    }

    .topbar .topbar-left {
        border-top: 3px solid {{ $color }};
    }

    #sidebar-menu ul ul a:hover {
        color: {{ $color }};
    }

    .theme-primary {
        background: {{ $color }} !important;
    }

</style>
