<header id="header" class="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('frontend.index') }}">
                <img src="assets/images/logo/logo-light.png" class="logo-light" alt="logo">
                <img src="assets/images/logo/logo-light.png" class="logo-dark" alt="logo">
            </a>
            <button class="navbar-toggler" type="button">
                <span class="menu-lines"><span></span></span>
            </button>
            <div class="header__top-right d-none d-lg-block">
                <ul class="list-unstyled d-flex justify-content-end align-items-center">
                    <li><i class="fa-solid fa-phone"></i><a href="tel:+8809639132756">+88 096 391 327 56</a></li>
                    <li>
                        <select class="name changeLang form-control" style="margin-top: 0px; height: 32px;">
                            <option value="en" {{ session()->get('locale') == 'en' ? 'selected' : '' }}>English
                            </option>
                            <option value="bn" {{ session()->get('locale') == 'bn' ? 'selected' : '' }}>Bangla
                            </option>
                        </select>
                        <span class="desc"></span>
                    </li>
                </ul>
            </div><!-- /.header-top-right -->
            <div class="collapse navbar-collapse" id="mainNavigation">
                <ul class="navbar-nav ml-auto">
                    <li class="nav__item with-dropdown">
                        <a href="{{ route('frontend.index') }}" class="dropdown-toggle nav__item-link {{ request()->routeIs('frontend.index') ? ' active' : null }} ">{{ __('frontend.home') }}</a>
                    </li><!-- /.nav-item -->
                    <li class="nav__item with-dropdown">
                        <a href="{{ route('about.us') }}" class="nav__item-link  {{ request()->routeIs('about.us') ? ' active' : null }}">{{ __('frontend.about_us') }}</a>
                    </li><!-- /.nav-item -->
                    <li class="nav__item">
                        <a href="{{ route('contact.us') }}" class="nav__item-link  {{ request()->routeIs('contact.us') ? ' active' : null }}">{{ __('frontend.contact_us') }}</a>
                    </li><!-- /.nav-item -->
                    <li class="nav__item">
                        <a href="{{ route('mission') }}" class="nav__item-link  {{ request()->routeIs('mission') ? ' active' : null }}">{{ __('frontend.mission') }}</a>
                    </li><!-- /.nav-item -->
                    <li class="nav__item">
                        <a href="{{ route('vision') }}" class="nav__item-link  {{ request()->routeIs('vision') ? ' active' : null }}">{{ __('frontend.vision') }}</a>
                    </li><!-- /.nav-item -->
                    
                    <!--</li><!-- /.nav-item -->
                    <li class="nav__item">
                        <a href="{{ route('service') }}" class="nav__item-link  {{ request()->routeIs('service') ? ' active' : null }}">{{ __('frontend.service') }}</a>
                    </li><!-- /.nav-item -->
                    
                    <li class="nav__item">
                        <a href="{{ route('login') }}" class="nav__item-link  {{ request()->routeIs('login') ? ' active' : null }}">{{ __('frontend.login') }}</a>
                    </li><!-- /.nav-item -->
                </ul><!-- /.navbar-nav -->
            </div><!-- /.navbar-collapse -->
            <div class="navbar-modules">
                <div class="modules__wrapper d-flex align-items-center">
                    <a href="#" class="module__btn module__btn-search"><i class="fa fa-search"></i></a>
                </div><!-- /.modules-wrapper -->
            </div><!-- /.navbar-modules -->
        </div><!-- /.container -->
    </nav><!-- /.navabr -->
</header><!-- /.Header -->
