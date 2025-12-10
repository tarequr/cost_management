<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="slimscroll-menu" id="remove-scroll">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu" id="side-menu">
                <li class="menu-title">Menu</li>
                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect">
                        <i class="icon-accelerator"></i><span> Dashboard </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('projects.index') }}" class="waves-effect"><i class="icon-folder"></i><span>
                            Projects </span></a>
                </li>
                <li>
                    <a href="{{ route('budget-estimate.index') }}" class="waves-effect"><i
                            class="icon-diamond"></i><span> Budget Estimate </span></a>
                </li>
                <li>
                    <a href="{{ route('budgets.draft', ['project' => 1]) }}" class="waves-effect"><i
                            class="icon-bar-chart"></i><span> Draft Budget </span></a>
                </li>
                <li>
                    <a href="{{ route('budgets.final', ['project' => 1]) }}" class="waves-effect"><i
                            class="icon-pie-chart"></i><span> Final Budget (EVM) </span></a>
                </li>
                {{-- <li>
                    <a href="calendar.html" class="waves-effect"><i class="icon-calendar"></i><span> Calendar </span></a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="waves-effect"><i class="icon-paper-sheet"></i><span> Pages <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span> </span></a>
                    <ul class="submenu">
                        <li><a href="pages-pricing.html">Pricing</a></li>
                        <li><a href="pages-invoice.html">Invoice</a></li>
                    </ul>
                </li>
                <li class="menu-title">lookup</li>
                <li>
                    <a href="javascript:void(0);" class="waves-effect"><i class="icon-pencil-ruler"></i> <span> UI Elements <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span> </span> </a>
                    <ul class="submenu">
                        <li><a href="ui-alerts.html">Alerts</a></li>
                        <li><a href="ui-badge.html">Badge</a></li>
                        <li><a href="ui-buttons.html">Buttons</a></li>
                        <li><a href="ui-cards.html">Cards</a></li>
                        <li><a href="ui-dropdowns.html">Dropdowns</a></li>
                        <li><a href="ui-navs.html">Navs</a></li>
                        <li><a href="ui-tabs-accordions.html">Tabs &amp; Accordions</a></li>
                        <li><a href="ui-modals.html">Modals</a></li>
                        <li><a href="ui-images.html">Images</a></li>
                        <li><a href="ui-progressbars.html">Progress Bars</a></li>
                        <li><a href="ui-pagination.html">Pagination</a></li>
                        <li><a href="ui-popover-tooltips.html">Popover & Tooltips</a></li>
                        <li><a href="ui-spinner.html">Spinner</a></li>
                        <li><a href="ui-carousel.html">Carousel</a></li>
                        <li><a href="ui-video.html">Video</a></li>
                        <li><a href="ui-typography.html">Typography</a></li>
                        <li><a href="ui-grid.html">Grid</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:void(0);" class="waves-effect"><i class="icon-diamond"></i> <span> Advanced UI <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span> </span> </a>
                    <ul class="submenu">
                        <li><a href="advanced-alertify.html">Alertify</a></li>
                        <li><a href="advanced-rating.html">Rating</a></li>
                        <li><a href="advanced-nestable.html">Nestable</a></li>
                        <li><a href="advanced-rangeslider.html">Range Slider</a></li>
                        <li><a href="advanced-sweet-alert.html">Sweet-Alert</a></li>
                        <li><a href="advanced-lightbox.html">Lightbox</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:void(0);" class="waves-effect"><i class="icon-share"></i><span> Multi Level <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span> </span></a>
                    <ul class="submenu">
                        <li><a href="javascript:void(0);"> Menu 1</a></li>
                        <li>
                            <a href="javascript:void(0);">Menu 2  <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="submenu">
                                <li><a href="javascript:void(0);">Menu 2.1</a></li>
                                <li><a href="javascript:void(0);">Menu 2.1</a></li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
            </ul>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->
</div>
<!-- Left Sidebar End -->
