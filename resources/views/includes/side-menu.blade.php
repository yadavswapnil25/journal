@php
    $user_role = "";
    $dashboard = "";
    $role_name = "";
    $user_image = "";
    $user_name = "";
    if (!empty(Auth::user()->id)) {
        $user = Auth::user();
        $user_image = $user->user_image;
        $user_roles_type = App\Models\User::getUserRoleType($user->id);
        $user_roles_type = !empty($user_roles_type) && is_object($user_roles_type) ? $user_roles_type : null;
        $user_role = !empty($user_roles_type) ? $user_roles_type->role_type : '';
        $active_dashboard_role = session('active_dashboard_role');
        if (in_array($active_dashboard_role, ['superadmin', 'editor', 'reviewer', 'author', 'reader'], true)) {
            $user_role = $active_dashboard_role;
        }
        $segment_role = Request::segment(1);
        if (in_array($segment_role, ['superadmin', 'editor', 'reviewer', 'author', 'reader'], true)) {
            $user_role = $segment_role;
        }
        $role_name = ucfirst(str_replace('_', ' ', $user_role));
        $user_name = $user->name;
        if ($user_role == 'superadmin' || $user_role == 'editor') {
            $dashboard = 'dashboard';
        } else {
            $dashboard = 'user';
        }
        if ($user_role == 'superadmin') {
            $page_author = 'superadmin';
        } else {
            $page_author = 'editor';
        }
    }
@endphp
<div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-3 float-left">
    <aside id="sj-asidebar" class="sj-asidebar sj-widgetbox">
        <div class="sj-widgetprofile">
            <div class="sj-widgetcontent">
                <figure>
                    <img id="user_image_sidebar" src="{{url(App\Helper::getUserImage(Auth::user()->id, $user_image) )}}" alt="{{trans('prs.user_img')}}">
                    <a class="sj-btnedite" href="{{url('/dashboard/general/settings/account-settings')}}"><i class="lnr lnr-pencil"></i></a>
                </figure>
                <div class="sj-admininfo">
                    {{!empty($user) ? $user_name : trans('prs.user_name') }}
                    <h4>{{{$role_name}}}</h4>
                </div>
            </div>
        </div>
        <div class="sj-widgetdashboard">
            <nav id="sj-dashboardnav" class="sj-dashboardnav">
                <ul>
                    @if (!($user_role == 'reader'))
                        @php $page_id = Request::segment(4); @endphp
                        {{-- Author/Editor article status menu --}}
                        {{App\Helper::displayArticleMenu($page_id)}}

                        {{-- Submit New Article (authors only) --}}
                        @if ($user_role == 'author')
                            <li class="{{ \Request::route()->getName() === 'checkAuthor' ? 'sj-active' : '' }}">
                                <a href="{{ route('checkAuthor') }}">
                                    <i class="lnr lnr-pencil"></i>
                                    <span>{{ trans('prs.add_article') }}</span>
                                </a>
                            </li>
                        @endif
                    @endif
                    @if ($user_role === 'superadmin')
                    @can ('View Categories')
                        <li class="{{\Request::route()->getName() === 'editionSetting' ? 'sj-active' : ''}}">
                            <a href="{{url('/dashboard/edition/settings')}}">
                                <i class="lnr lnr-cog"></i><span>{{trans('prs.edition_settings')}}</span>
                            </a>
                        </li>
                        <li class="{{\Request::route()->getName() === 'categorySetting' ? 'sj-active' : ''}}">
                            <a href="{{url('/dashboard/category/settings')}}">
                                <i class="lnr lnr-layers"></i><span>{{trans('prs.category_settings')}}</span>
                            </a>
                        </li>
                    @endcan
                    @endif
                    @if ($user_role === 'superadmin')
                    @can ('Manage Users')
                        <li class="{{\Request::route()->getName() === 'manageUsers' ? 'sj-active' : ''}}">
                            <a href="{{url('superadmin/users/manage-users')}}">
                                <i class="lnr lnr-users"></i><span>{{trans('prs.manage_users')}}</span>
                            </a>
                        </li>
                    @endcan
                    @endif
                        <li class="{{\Request::route()->getName() === 'accountSetting' ? 'sj-active' : ''}}">
                            <a href="{{url('dashboard/general/settings/account-settings')}}">
                                <i class="lnr lnr-cog"></i><span>{{trans('prs.account_settings')}}</span>
                            </a>
                        </li>
                    @if (in_array($user_role, ['superadmin', 'editor'], true))
                    @can ('Manage Pages')
                        <li class="{{\Request::route()->getName() === 'managePages' ? 'sj-active' : ''}}">
                            <a href="{{url('/'.$page_author.'/dashboard/pages')}}">
                                <i class="lnr lnr-menu"></i><span>{{trans('prs.manage_pages')}}</span>
                            </a>
                        </li>
                    @endcan
                    @endif
                    @if (in_array($user_role, ['superadmin', 'editor'], true))
                    @can ('Site Management')
                        <li class="{{\Request::route()->getName() === 'manageSite' ? 'sj-active' : ''}}">
                            <a href="{{url('/dashboard/'.$page_author.'/site-management/settings')}}">
                                <i class="lnr lnr-code"></i><span>{{trans('prs.manage_site')}}</span>
                            </a>
                        </li>
                    @endcan
                    @endif
                    @if ($user_role == 'reader')
                        <li class="{{\Request::route()->getName() === 'downloads' ? 'sj-active' : ''}}">
                            <a href="{{url('/user/products/downloads')}}">
                                <i class="lnr lnr-download"></i><span>{{trans('prs.downloads')}}</span>
                            </a>
                        </li>
                    @elseif ($user_role == 'superadmin')
                        <li class="{{\Request::route()->getName() === 'orders' ? 'sj-active' : ''}}">
                            <a href="{{url('/superadmin/downloads')}}">
                                <i class="lnr lnr-download"></i><span>{{trans('prs.downloads')}}</span>
                            </a>
                        </li>
                        <li class="{{\Request::route()->getName() === 'paymentSettings' ? 'sj-active' : ''}}">
                            <a href="{{url('/dashboard/superadmin/site-management/payment/settings')}}">
                                <i class="lnr lnr-cart"></i><span>{{trans('prs.payment_settings')}}</span>
                            </a>
                        </li>
                        <li class="{{\Request::route()->getName() === 'emailSettings' ? 'sj-active' : ''}}">
                            <a href="{{url('/dashboard/superadmin/site-management/settings/email')}}">
                                <i class="lnr lnr-envelope"></i><span>{{trans('prs.email_settings')}}</span>
                            </a>
                        </li>
                        <li class="{{\Request::route()->getName() === 'emailTemplates' ? 'sj-active' : ''}}">
                            <a href="{{url('/dashboard/superadmin/emails/templates')}}">
                                <i class="lnr lnr-envelope"></i><span>{{trans('prs.email_templates')}}</span>
                            </a>
                        </li>
                        <li class="{{ in_array(\Request::route()->getName(), ['manageAnnouncements', 'createAnnouncement', 'editAnnouncement'], true) ? 'sj-active' : '' }}">
                            <a href="{{ route('manageAnnouncements') }}">
                                <i class="lnr lnr-bullhorn"></i><span>{{ trans('prs.marquee_announcements') }}</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-sidebarform').submit();">
                            <i class="lnr lnr-exit"></i>
                            <span>{{trans('prs.logout')}}</span>
                        </a>
                        <form id="logout-sidebarform" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>
                <div class="sj-navdashboard-footer">
                    <span class="version-area">{{ config('app.version') }}</span>
                </div>
            </nav>
        </div>
    </aside>
</div>
