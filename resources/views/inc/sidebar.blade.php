<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul>
                <li class="text-muted menu-title"></li>

                <li>
                    <a href="{{ route('home') }}" class="waves-effect"><i class="mdi mdi-view-dashboard"></i> <span>
                            {{ __('sidebar.dashboard') }} </span> </a>
                </li>

                @if (permission('s1'))
                    <li class="has_sub">
                        <a href="{{ url('/contact?type=supplier') }}" class="waves-effect"><i class="fa fa-car"></i>
                            <span>
                                {{ __('sidebar.supplier') }} </span>
                        </a>
                    </li>
                @endif
                @if (!isRole(ROLE_AGENT) && permission('sup'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-address-book"></i> <span>
                                {{ __('sidebar.customer')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('c1'))
                                <li><a href="{{ url('/contact?type=customer') }}">{{ __('sidebar.customer_list') }}</a>
                                </li>
                            @endif
                            @if (permission('cg1'))
                                <li><a href="{{ route('customer-group.index') }}">{{ __('sidebar.customer')[1] }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (!isRole(ROLE_AGENT) && permission('pro'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="fa fas fa-cubes"></i> <span>
                                {{ __('sidebar.products')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('p1'))
                                <li><a href="{{ route('product.index') }}">{{ __('sidebar.products')[1] }}</a>
                                </li>
                            @endif
                            @if (permission('p2'))
                                <li><a href="{{ route('product.create') }}">{{ __('sidebar.products')[2] }}</a>
                                </li>
                            @endif
                            @if (permission('uni1'))
                                <li><a href="{{ route('unit.index') }}">{{ __('sidebar.products')[3] }}</a></li>
                            @endif
                            @if (permission('cat1'))
                                <li><a href="{{ route('category.index') }}">{{ __('sidebar.products')[4] }}</a>
                                </li>
                            @endif
                            @if (permission('bra1'))
                                <li><a href="{{ route('brand.index') }}">{{ __('sidebar.products')[5] }}</a></li>
                            @endif
                            @if (permission('pro'))
                                <li><a href="{{ route('product.barcode') }}">{{ __('sidebar.products')[6] }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>

                @else
                    <!--<li class="has_sub">-->
                    <!--    <a href="{{ route('product.index') }}" class="waves-effect"><i class="fa fas fa-cubes"></i>-->
                    <!--        <span>-->
                    <!--            {{ __('sidebar.products')[0] }} </span></a>-->
                    <!--</li>-->
                @endif




                @if (permission('app'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="ti-mobile"></i>
                            <span> {{ __('sidebar.app') }} </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul class="list-unstyled">
                             <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect">
                                    <span> {{ __('sidebar.points.wallet') }} </span>
                                    <span class="menu-arrow"></span>
                                </a>

                                <ul class="list-unstyled">
                                    @if (permission('pu2') && !isRole(ROLE_AGENT))
                                        <li>
                                            <a href="{{ route('agent-points.index') }}">
                                                <span> {{ __('sidebar.points.agent') }} </span>
                                            </a>
                                        </li>
                                    @endif

                                    @if (permission('pu2') && isRole(ROLE_AGENT))
                                        <li>
                                            <a href="{{ route('customer-points.index') }}">
                                                <span> {{ __('sidebar.points.customer') }} </span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                            @if (permission('app'))
                                <li>
                                    <a href="{{ route('farms.index') }}">
                                        <span> {{ __('sidebar.app.farm') }} </span>
                                    </a>
                                </li>
                            @endif

                            @if (!isRole(ROLE_AGENT) && permission('app'))
                                <li>
                                    <a href="{{ route('seed-companies.index') }}">
                                        <span> {{ __('sidebar.app.seed-company') }} </span>
                                    </a>
                                </li>
                            @endif

                            @if (!isRole(ROLE_AGENT) && permission('app'))
                                <li class="has_sub">
                                    <a href="javascript:void(0);"
                                        class="waves-effect {{ request()->routeIs('cattle*') ? 'active subdrop' : '' }}">
                                        <span> {{ __('sidebar.app.cattle') }} </span>
                                        <span class="menu-arrow"></span>
                                    </a>

                                    <ul class="list-unstyled"
                                        style="{{ request()->routeIs('cattle-*') ? 'display: block' : '' }}">
                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('cattle.index') }}">
                                                    <span> {{ __('sidebar.app.cattle') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (!isRole(ROLE_AGENT) && permission('app'))
                                            <li>
                                                <a href="{{ route('cattle-groups.index') }}">
                                                    <span> {{ __('sidebar.app.cattle-group') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (!isRole(ROLE_AGENT) && permission('app'))
                                            <li>
                                                <a href="{{ route('cattle-breeds.index') }}">
                                                    <span> {{ __('sidebar.app.cattle-breed') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (!isRole(ROLE_AGENT) && permission('app'))
                                            <li>
                                                <a href="{{ route('cattle-diseases.index') }}">
                                                    <span> {{ __('sidebar.app.cattle-disease') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (!isRole(ROLE_AGENT) && permission('app'))
                                            <li>
                                                <a href="{{ route('cattle-vaccines.index') }}">
                                                    <span> {{ __('sidebar.app.cattle-vaccine') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (!isRole(ROLE_AGENT) && permission('app'))
                                            <li>
                                                <a href="{{ route('cattle-disease-histories.index') }}">
                                                    <span> {{ __('sidebar.app.disease-history') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (!isRole(ROLE_AGENT) && permission('app'))
                                            <li>
                                                <a href="{{ route('cattle-health-info.index') }}">
                                                    <span> {{ __('sidebar.app.health-info') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (!isRole(ROLE_AGENT) && permission('app'))
                                            <li>
                                                <a href="{{ route('cattle-foods.index') }}">
                                                    <span> {{ __('sidebar.app.cattle-food') }} </span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @else
                                <li class="has_sub">
                                    <a href="{{ route('cattle.index') }}"
                                        class="waves-effect {{ request()->routeIs('cattle*') ? 'active subdrop' : '' }}">
                                        <span> {{ __('sidebar.app.cattle') }} </span>
                                    </a>
                                </li>
                            @endif


                            @if (!isRole(ROLE_AGENT) && permission('app'))
                                <li class="has_sub">
                                    <a href="javascript:void(0);"
                                        class="waves-effect {{ request()->routeIs('calve*', 'calf*') ? 'active subdrop' : '' }}">
                                        <span> {{ __('sidebar.app.calf') }} </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul class="list-unstyled"
                                        style="{{ request()->routeIs('calf-*') ? 'display: block' : '' }}">
                                        <li>
                                            <a href="{{ route('calves.index') }}">
                                                <span> {{ __('sidebar.app.calf') }} </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('calf-birth-problems.index') }}">
                                                <span> {{ __('sidebar.app.calf-birth-problem') }} </span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @else
                                <li class="has_sub">
                                    <a href="{{ route('calves.index') }}" class="waves-effect">
                                        <span> {{ __('sidebar.app.calf') }} </span>
                                    </a>
                                </li>
                            @endif


                            @if (!isRole(ROLE_AGENT) && permission('app'))
                                <li class="has_sub">
                                    <a href="javascript:void(0);"
                                        class="waves-effect {{ request()->routeIs('m-account*') ? 'active subdrop' : '' }}">
                                        <span> {{ __('sidebar.app.pl') }} </span>
                                        <span class="menu-arrow"></span>
                                    </a>

                                    <ul class="list-unstyled"
                                        style="{{ request()->routeIs('m-accoun*') ? 'display: block' : '' }}">
                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('m-accounts.index') }}">
                                                    <span> {{ __('sidebar.app.pl.account') }} </span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            @if (!isRole(ROLE_AGENT) && permission('app'))
                                <li class="has_sub">
                                    <a href="javascript:void(0);"
                                        class="waves-effect {{ request()->routeIs('insurance-*') ? 'active subdrop' : '' }}">
                                        <span> {{ __('sidebar.app.insurance') }} </span>
                                        <span class="menu-arrow"></span>
                                    </a>

                                    <ul class="list-unstyled"
                                        style="{{ request()->routeIs('insurance-*') ? 'display: block' : '' }}">
                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('insurance-companies.index') }}">
                                                    <span> {{ __('sidebar.app.insurance-company') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('insurance-types.index') }}">
                                                    <span> {{ __('sidebar.app.insurance-type') }} </span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            @if (permission('appreport'))
                                <li class="has_sub">
                                    <a href="javascript:void(0);"
                                        class="waves-effect {{ request()->routeIs('app-report.*') ? 'active subdrop' : '' }}">
                                        <span> {{ __('sidebar.app.report') }} </span>
                                        <span class="menu-arrow"></span>
                                    </a>

                                    <ul class="list-unstyled"
                                        style="{{ request()->routeIs('app-report.milk-production') ? 'display: block' : '' }}">

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('app-report.farm') }}">
                                                    <span> {{ __('sidebar.app.report.farm') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('app-report.milk-production') }}">
                                                    <span> {{ __('sidebar.app.report.milk-production') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('app-report.vaccine') }}">
                                                    <span> {{ __('sidebar.app.report.vaccine') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('app-report.disease') }}">
                                                    <span> {{ __('sidebar.app.report.disease') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('app-report.impregnation') }}">
                                                    <span> {{ __('sidebar.app.report.impregnation') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('app-report.pregnancy') }}">
                                                    <span> {{ __('sidebar.app.report.pregnancy') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('app-report.abortion') }}">
                                                    <span> {{ __('sidebar.app.report.abortion') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('app-report.food-consumption') }}">
                                                    <span> {{ __('sidebar.app.report.food-consumption') }} </span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (permission('pu2'))
                                            <li>
                                                <a href="{{ route('app-report.weight-info') }}">
                                                    <span> {{ __('sidebar.app.report.weight-info') }} </span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                        </ul>
                    </li>
                @endif

                @if (Auth::check() && Auth::user()?->userPermission?->role?->role_name === 'Admin')
                    <li class="has_sub">
                        <a href="javascript:void(0);"
                            class="waves-effect {{ request()->routeIs('warehouses.*', 'warehouse-salesmen.*', 'warehouse-purchases.*', 'warehouse-stock-transfers.*', 'admin-stock-summary.*', 'warehouse-salesman-assignments.*', 'warehouse-salesman-returns.*', 'report.lsp.due') ? 'active subdrop' : '' }}">
                            <i class="mdi mdi-store"></i>
                            <span> Area Office Management</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul class="list-unstyled"
                            style="{{ request()->routeIs('warehouses.*', 'warehouse-salesmen.*', 'warehouse-purchases.*', 'warehouse-stock-transfers.*', 'admin-stock-summary.*', 'warehouse-salesman-assignments.*', 'warehouse-salesman-returns.*', 'report.lsp.due') ? 'display: block' : '' }}">
                            <li><a href="{{ route('warehouses.index') }}">Area Office List</a></li>
                            <li><a href="{{ route('warehouses.stock') }}">Stock Summary</a></li>
                            <li><a href="{{ route('admin-stock-summary.index') }}">Admin Stock Summary</a></li>
                            <li><a href="{{ route('warehouse-purchases.index') }}">Admin Purchase</a></li>
                            <li><a href="{{ route('warehouse-stock-transfers.index') }}">Transferred List</a></li>
                            <li><a href="{{ route('warehouse-salesman-assignments.transfer-list') }}">Area Office Transfer List</a></li>
                            <li><a href="{{ route('warehouses.area-manager-daily') }}">Area Manager Daily Report</a></li>
                            <li>
                                <a href="javascript:void(0);"
                                    class="waves-effect {{ request()->routeIs('warehouse-salesmen.*', 'warehouse-salesman-assignments.*', 'warehouse-salesman-returns.*', 'report.lsp.due', 'report.lsp.sale') ? 'active subdrop' : '' }}">
                                    <span> Manage LSP </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="list-unstyled"
                                    style="{{ request()->routeIs('warehouse-salesmen.*', 'warehouse-salesman-assignments.*', 'warehouse-salesman-returns.*', 'report.lsp.due', 'report.lsp.sale') ? 'display: block' : '' }}">
                                    <li><a href="{{ route('warehouse-salesmen.index') }}">LSP List</a></li>
                                    <li><a href="{{ route('warehouse-salesman-assignments.index') }}">Assign Stock to LSP</a></li>
                                    <li><a href="{{ route('warehouse-salesman-returns.index') }}">LSP Return History</a></li>
                                    <li><a href="{{ route('report.lsp.due') }}">LSP Due Report</a></li>
                                    <li><a href="{{ route('report.lsp.sale') }}">LSP Sales Report</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (permission('app'))
                    <li>
                        <a href="{{ route('app-customer.index') }}" class="waves-effect"><i class="fa fas fa-user"></i>
                            <span> {{ __('sidebar.app.beneficiary') }} </span>
                        </a>
                    </li>
                @endif



                 @if (permission('app'))
                    <li>
                        <a href="{{ route('renewable-energy.index') }}" class="waves-effect"><i class="fa fa-sun"></i>
                            <span> Renewable Energy </span>
                        </a>
                    </li>
                @endif






                 @if (permission('pur'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="fa fas fa-pen"></i><span>
                               Daily Visit</span><span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('pur'))
                                <li><a href="{{ route('visit-info.index') }}">
                                        Visit List
                                    </a>
                                </li>
                            @endif
                            @if (permission('pur'))
                                <li><a href="{{ route('visit-info.create') }}">
                                        Manualy Entry
                                    </a>
                                </li>
                            @endif
                            @if (permission('pur'))
                                <li><a href="{{ route('qr.scan') }}">QR Code Scan</a>
                                </li>
                            @endif
                            @if (!isRole(ROLE_AGENT) && permission('pur'))
                                <li><a href="{{ route('visit-info.report') }}">Report</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if (!isRole(ROLE_AGENT) &&permission('app'))
                    <li>
                        <a href="{{ route('cow-feeds.index') }}" class="waves-effect"><i class="fa fa-solid fa-seedling"></i></i>
                            <span> Cow Feed </span>
                        </a>
                    </li>
                @endif

                @if (!isRole(ROLE_AGENT) && permission('pur'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-shopping-cart"></i> <span>
                                {{ __('sidebar.purchase')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('pu2'))
                                <li><a href="{{ route('purchase.create') }}">
                                        {{ __('sidebar.purchase')[1] }}
                                    </a>
                                </li>
                            @endif
                            @if (permission('pu1'))
                                <li><a href="{{ route('purchase.index') }}">{{ __('sidebar.purchase')[2] }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif


                @if (permission('pu2'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="ti-bar-chart"></i>
                            <span> {{ __('sidebar.stock.stock') }} </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('stock') }}">
                                    <span> {{ __('sidebar.stock.current_stock') }} </span>
                                </a>
                            </li>
                            @if (!isRole(ROLE_AGENT))
                                <li>
                                    <a href="{{ route('stock-transfer.create') }}">
                                        <span> {{ __('sidebar.stock.transfer') }} </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('stock-transfer.index', ['type' => 'transferred']) }}">
                                        <span> {{ __('sidebar.stock.transfer_list') }} </span>
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route('stock-transfer.index', ['type' => 'received']) }}">
                                        <span> {{ __('sidebar.stock.receive_list') }} </span>
                                    </a>
                                </li>
                                <!--<li>-->
                                <!--    <a href="{{ route('stock-transfer.agent.create') }}">-->
                                <!--        <span> {{ __('page.stock_transfer.stock_transfer') }} </span>-->
                                <!--    </a>-->
                                <!--</li>-->
                                <!--<li>-->
                                <!--    <a href="{{ route('agent-stock-transfer.create') }}">-->
                                <!--        <span> {{ __('page.stock_transfer.stock_transfer') }} </span>-->
                                <!--    </a>-->
                                <!--</li>-->
                                <!--<li>-->
                                <!--    <a href="{{ route('agent-stock-transfer.index') }}">-->
                                <!--        <span> {{ __('page.stock_transfer.stock_transfer_list') }} </span>-->
                                <!--    </a>-->
                                <!--</li>-->
                            @endif
                        </ul>
                    </li>
                @endif

                @if (permission('sale'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-shopping-cart"></i> <span>
                                {{ __('sidebar.sale')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('sa1'))
                                <li><a href="{{ route('pos') }}"> {{ __('sidebar.sale')[1] }} </a></li>
                            @endif
                            @if (permission('sa2'))
                                <li><a href="{{ route('sale') }}"> {{ __('sidebar.sale')[2] }} </a></li>
                            @endif
                            @if (!isRole(ROLE_AGENT) && permission('sr1'))
                                <li><a href="{{ route('return.sale.index') }}"> {{ __('sidebar.sale')[3] }} </a>
                                </li>
                            @endif

                            @if (!isRole(ROLE_AGENT))
                            <li><a href="{{ route('agentsalelist.index') }}"> {{ __('Agent Sales List') }} </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                @endif


                @if (permission('report'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-book"></i> <span>
                                {{ __('sidebar.report')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('re1'))
                                <li><a href="{{ route('report.stock') }}">{{ __('Stock Report') }}</a>
                                </li>
                                  <li><a href="{{ route('report.profitLoss') }}">{{ __('sidebar.report')[1] }}</a>
                                </li>
                                <li><a href="{{ route('report.product') }}">{{ __('sidebar.report')[2] }}</a>
                                </li>
                                <li><a href="{{ route('report.category') }}">{{ __('sidebar.report')[3] }}</a>
                                </li>
                                <li><a href="{{ route('report.due.sale') }}">Agent Sale Due Report</a></li>

                                <li><a href="{{ route('report.sale') }}">{{ __('sidebar.report')[4] }}</a></li>
                                <li><a href="{{ route('agent.sale.report') }}">{{ __('sidebar.report')[10] }}</a></li>
                                <li><a href="{{ route('report.lsp.due') }}">Lsp Sales Due Report</a></li>
                                <li><a href="{{ route('report.lsp.sale') }}">Lsp Sales Report</a></li>
                                <li><a href="{{ route('report.dailySaleReport') }}">{{ __('sidebar.report')[5] }}</a></li>

                                <li><a
                                        href="{{ route('report.monthlySaleReport') }}">{{ __('sidebar.report')[6] }}</a>
                                </li>
                                <li><a href="{{ route('report.purchase') }}">{{ __('sidebar.report')[7] }}</a>
                                </li>
                                <li><a href="{{ route('report.customer') }}">{{ __('sidebar.report')[8] }}</a>
                                </li>
                                <li><a href="{{ route('report.supplier') }}">{{ __('sidebar.report')[9] }}</a>
                                </li>
                                <li><a href="{{ route('report.sold.product') }}">{{ __('sidebar.soldproductreport') }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (permission('expense'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-briefcase"></i> <span>
                                {{ __('sidebar.expense')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('ex1'))
                                <li><a href="{{ route('expense') }}">{{ __('sidebar.expense')[1] }}</a></li>
                            @endif
                            @if (permission('ex2'))
                                <li><a href="{{ route('expense.type') }}">{{ __('sidebar.expense')[2] }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif


                @if (permission('expense'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-briefcase"></i> <span>
                                {{ __('sidebar.income')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('ex1'))
                                <li><a href="{{ route('income.index') }}">{{ __('sidebar.income')[1] }}</a></li>
                            @endif
                            @if (permission('ex2'))
                                <li><a href="{{ route('incomeType.index') }}">{{ __('sidebar.income')[2] }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (permission('asset'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-briefcase"></i> <span>
                                {{ __('sidebar.asset')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('as1'))
                                <li><a href="{{ route('asset') }}">{{ __('sidebar.asset')[1] }}</a></li>
                            @endif
                            @if (permission('as2'))
                                <li><a href="{{ route('asset.category') }}">{{ __('sidebar.asset')[2] }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (permission('accounts'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-briefcase"></i> <span>
                                {{ __('sidebar.account')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('ac1'))
                                <li><a
                                        href="{{ route('account.receive.report') }}">{{ __('sidebar.account')[1] }}</a>
                                </li> <!-- customer all report -->
                                <li><a
                                        href="{{ route('account.received.report') }}">{{ __('sidebar.account')[2] }}</a>
                                </li> <!-- customer paid -->
                                <li><a
                                        href="{{ route('account.receiveable.report') }}">{{ __('sidebar.account')[3] }}</a>
                                </li> <!-- customer due -->
                            @endif
                            @if (permission('ac2'))
                                <li><a
                                        href="{{ route('account.payment.report') }}">{{ __('sidebar.account')[4] }}</a>
                                </li> <!-- supplier all report -->
                                <li><a href="{{ route('account.paid.report') }}">{{ __('sidebar.account')[5] }}</a>
                                </li> <!-- supplier paid -->
                                <li><a
                                        href="{{ route('account.payable.report') }}">{{ __('sidebar.account')[6] }}</a>
                                </li> <!-- supplier due -->
                                <li><a
                                        href="{{ route('account.salary.report') }}">{{ __('sidebar.account')[7] }}</a>
                                </li> <!-- supplier due -->
                            @endif
                            @if (permission('ac3'))
                                <li><a
                                        href="{{ route('account.expense.report') }}">{{ __('sidebar.account')[8] }}</a>
                                </li>
                            @endif
                            @if (permission('accountTypeeAdd'))
                                <li><a href="{{ route('account.type') }}">{{ __('sidebar.account')[9] }}</a>
                                </li>
                            @endif
                            @if (permission('accountTypeList'))
                                <li><a href="{{ route('account.type.list') }}">{{ __('sidebar.account')[10] }}</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (permission('employee'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-users"></i> <span>
                                {{ __('sidebar.employee')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('em1'))
                                <li><a href="{{ route('employee.index') }}">{{ __('sidebar.employee')[1] }}</a>
                                </li>
                            @endif
                            @if (permission('em2'))
                                <li><a href="{{ route('employee.create') }}">{{ __('sidebar.employee')[2] }}</a>
                                </li>
                            @endif
                            @if (permission('sal2'))
                                <li><a href="{{ route('all.salary') }}">{{ __('sidebar.employee')[3] }}</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (permission('user'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-users"></i> <span>
                                {{ __('sidebar.user')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('u1'))
                                <li><a href="{{ route('user.index') }}">{{ __('sidebar.user')[1] }}</a></li>
                            @endif
                            @if (permission('u2'))
                                <li><a href="{{ route('user.create') }}">{{ __('sidebar.user')[2] }}</a></li>
                            @endif
                            @if (permission('ro1'))
                                <li><a href="{{ route('role.index') }}">{{ __('sidebar.user')[3] }}</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (permission('setting'))
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="mdi mdi-settings"></i> <span>
                                {{ __('sidebar.setting')[0] }} </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            @if (permission('st1'))
                                <li><a href="{{ route('settings') }}">{{ __('sidebar.setting')[1] }}</a></li>
                            @endif

                            @if (permission('admin-deposit.index'))
                                <a href="{{ route('admin-deposit.index') }}">
                                    <span> {{ __('sidebar.admin_deposit.deposit') }} </span>
                                </a>
                            @endif
                            @if (permission('st3'))
                                <li><a href="{{ route('vat-group') }}">{{ __('sidebar.setting')[4] }}</a></li>
                            @endif
                        </ul>
                    </li>
                @endif



            </ul>
            <div class="clearfix"></div>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>

</div>
