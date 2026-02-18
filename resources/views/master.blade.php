<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
	<meta name="description" content="@yield('description')">
	<meta name="keywords" content="@yield('keywords')">
    
    @php
    // Check if current page is admin/dashboard page
    $is_admin_page = false;
    $current_route = Request::path();
    // Pages that should use new design (public pages + create article)
    $new_design_pages = ['/', 'login', 'register', 'article/', 'published/', 'edition/', 'page/', 'author/create-article'];
    $use_new_design = false;
    foreach ($new_design_pages as $page) {
        if ($current_route == '/' || strpos($current_route, $page) === 0 || $current_route == $page) {
            $use_new_design = true;
            break;
        }
    }
    // Admin routes that should use old design
    if (!$use_new_design) {
        $admin_routes = ['dashboard', 'superadmin', 'author/user', 'reviewer', 'editor'];
        foreach ($admin_routes as $route) {
            if (strpos($current_route, $route) !== false) {
                $is_admin_page = true;
                break;
            }
        }
    }
    
    if(Schema::hasTable('users')){
        $user_role = "";
        if(!empty(Auth::user()->id)){
            $user_id = Auth::user()->id;
            $user_role_type = App\Models\User::getUserRoleType($user_id);
            $user_role = !empty($user_role_type) && is_object($user_role_type) ? $user_role_type->role_type : "";
        }
    }
    @endphp
    
    {{-- Load CSS based on page type --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome/fontawesome-all.css') }}" rel="stylesheet">
    
    @if($is_admin_page)
        {{-- Old admin CSS files --}}
        <link href="{{ asset('css/main.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
        <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
        <link href="{{ asset('css/custom-theme.css') }}" rel="stylesheet">
        {{-- Also load new CSS for header/footer --}}
        <link href="{{ asset('css/figma-exact.css') }}?v={{ time() }}" rel="stylesheet">
    @else
        {{-- New public website CSS --}}
        <link href="{{ asset('css/figma-exact.css') }}?v={{ time() }}" rel="stylesheet">
    @endif
    <script type="text/javascript">
        var APP_URL = {!! json_encode(url('/')) !!}
        var USER_ROLE = {!! json_encode($user_role) !!}
    </script>
</head>

<body>
    {{ \App::setLocale(env('APP_LANG')) }}
    
    {{-- No wrapper divs from old theme, just the content --}}
    @yield('content')
    
    <script src="{{ asset('js/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
