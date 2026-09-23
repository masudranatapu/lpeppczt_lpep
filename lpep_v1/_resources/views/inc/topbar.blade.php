<style>
    .topbar-mobile-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        width: 100%;
        min-height: 60px;
        flex-wrap: nowrap;
    }

    .topbar-title {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .topbar-short-menu {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
        flex-wrap: nowrap;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        flex: 1;
    }

    .topbar-short-menu::-webkit-scrollbar {
        display: none;
    }

    .topbar-short-menu li {
        list-style: none;
        flex-shrink: 0;
    }

    .topbar-short-menu a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 28px;
        min-width: 36px;
        padding: 0 8px;
        color: #fff;
        text-decoration: none;
    }

    .topbar-short-menu i {
        font-size: 15px;
        line-height: 1;
    }

    .topbar-menu-holder {
        margin: 0;
        padding: 0;
        list-style: none;
        flex-shrink: 0;
    }

    .topbar-menu-holder .list-inline-item {
        margin: 0;
        padding: 0;
    }

    .topbar-text {
        display: none;
    }

    .bg-green {
        background: #00a65a;
    }

    .bg-red {
        background: #ff5b5b;
    }

    .bg-blue {
        background: #3c8dbc;
    }

    .bg-yellow {
        background: #b59b00;
    }

    .bg-dark-icon {
        color: #37474f !important;
        background: transparent !important;
    }

    @media (min-width: 768px) {
        .topbar-title {
            font-size: 18px;
        }

        .topbar-short-menu {
            justify-content: flex-end;
            overflow-x: visible;
        }

        .topbar-text {
            display: inline;
            margin-left: 5px;
            font-size: 12px;
        }

        .topbar-short-menu a {
            min-width: auto;
        }
    }

    @media (max-width: 420px) {
        .topbar-short-menu {
            gap: 5px;
        }

        .topbar-short-menu a {
            height: 27px;
            min-width: 34px;
            padding: 0 7px;
        }

        .topbar-title {
            font-size: 15px;
            max-width: 90px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }
</style>

<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
        <a href="" class="logo">
            <span>
                <img src="/{{ currentBranch()->logo }}" height="55px" alt="{{ $business_setting->name }}">
            </span>
            <i class="mdi mdi-layers"></i>
        </a>
    </div>


    <!-- Button mobile view to collapse sidebar menu -->
  <div class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="topbar-mobile-row">

            <h4 class="topbar-title">{{ Auth::user()->name }}</h4>

            <ul class="topbar-short-menu">

                @if (!isRole(ROLE_AGENT) && permission('ac1'))
                    <li>
                        <a href="{{ route('account.receive.report') }}" class="bg-green" title="Customer Receive">
                            <i class="fa fa-money"></i>
                            <small class="topbar-text">{{ __('sidebar.topbar')[1] }}</small>
                        </a>
                    </li>
                @endif

                @if (!isRole(ROLE_AGENT) && permission('ac2'))
                    <li>
                        <a href="{{ route('account.payment.report') }}" class="bg-red" title="Supplier Payment">
                            <i class="fa fa-money"></i>
                            <small class="topbar-text">{{ __('sidebar.topbar')[2] }}</small>
                        </a>
                    </li>
                @endif

                @if (!isRole(ROLE_AGENT))
                    <li>
                        <a href="javascript:void(0);" class="bg-blue" title="Today's Summary" id="summery">
                            <i class="ti-bar-chart"></i>
                            <small class="topbar-text">{{ __('sidebar.topbar')[3] }}</small>
                        </a>
                    </li>
                @endif

                @if (!isRole(ROLE_AGENT))
                    <li>
                        <a href="{{ route('sale.in.return') }}" class="bg-yellow" title="Return Order">
                            <i class="fa fa-shopping-bag"></i>
                            <small class="topbar-text">{{ __('sidebar.topbar')[5] }}</small>
                        </a>
                    </li>
                @endif

                @if (permission('sa1'))
                    <li>
                        <a href="{{ route('pos') }}" class="bg-green" title="POS">
                            <i class="mdi mdi-cart-plus"></i>
                            <small class="topbar-text">{{ __('sidebar.topbar')[4] }}</small>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="javascript:void(0);" class="right-bar-toggle bg-dark-icon" title="Settings">
                        <i class="mdi mdi-account-settings"></i>
                    </a>
                </li>

            </ul>

            <ul class="nav navbar-nav list-inline navbar-left topbar-menu-holder">
                <li class="list-inline-item">
                    <button class="button-menu-mobile open-left">
                        <i class="mdi mdi-menu"></i>
                    </button>
                </li>
            </ul>

        </div>
    </div>
</div>
</div>
