{{-- Navbar --}}
<nav class="figma-navbar" style="display: block !important; visibility: visible !important; position: sticky !important; top: 0 !important; z-index: 1000 !important; background: #ffffff !important; width: 100% !important;">
    <div class="figma-navbar-container">
        <div class="figma-navbar-left">
            <a href="{{url('/')}}" class="figma-navbar-logo">
                <img src="{{asset('images/logo.png')}}" alt="shele">
            </a>
            <div class="figma-navbar-menu">
                <a href="{{url('/')}}" class="figma-navbar-link {{ Request::is('/') ? 'active' : '' }}">Home</a>
                <div class="figma-navbar-item">
                    <a href="#" class="figma-navbar-link has-dropdown">About</a>
                    <div class="figma-navbar-dropdown">
                        <a href="{{ route('showPage', ['slug' => 'about']) }}">About the Journal</a>
                        <a href="{{ route('showPage', ['slug' => 'aims-scope']) }}">Aims & Scope</a>
                        <a href="{{ route('showPage', ['slug' => 'publication-information']) }}">Publication Info</a>
                    </div>
                </div>
                <div class="figma-navbar-item">
                    <a href="#" class="figma-navbar-link has-dropdown">Submissions</a>
                    <div class="figma-navbar-dropdown">
                        <a href="{{ route('showPage', ['slug' => 'submission-guidelines']) }}">Submissions Guidelines</a>
                        <a href="{{ route('showPage', ['slug' => 'call-for-submissions']) }}">Call for submissions</a>
                        <a href="{{ route('showPage', ['slug' => 'special-issues']) }}">Special issues and guest editors</a>
                    </div>
                </div>
                <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}" class="figma-navbar-link {{ Request::is('page/journal-policies*') || Request::is('page/policies*') ? 'active' : '' }}">Policies</a>
                <div class="figma-navbar-item">
                    <a href="#" class="figma-navbar-link has-dropdown">Editorial Team</a>
                    <div class="figma-navbar-dropdown">
                        <a href="{{ route('showPage', ['slug' => 'editor-in-chief']) }}">Editor-in-Chief</a>
                        <a href="{{ route('showPage', ['slug' => 'editorial-board']) }}">Editorial Board</a>
                        <a href="{{ route('showPage', ['slug' => 'advisory-board']) }}">Advisory Board</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="figma-navbar-actions">
            <svg class="figma-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            @guest
                <a href="{{ route('login') }}" class="figma-login-btn">Log in</a>
                <a href="{{ route('register') }}" class="figma-signup-btn">Sign up</a>
            @else
                @php
                    $dashboardUrl = url('/');
                    if (!empty(Auth::user()->id)) {
                        $userId = Auth::user()->id;
                        $userRoleType = App\Models\User::getUserRoleType($userId);
                        $userRoleType = !empty($userRoleType) && is_object($userRoleType) ? $userRoleType : null;
                        $roleType = !empty($userRoleType) ? $userRoleType->role_type : '';

                        if ($roleType === 'author') {
                            $dashboardUrl = url('author/user/' . $userId . '/articles-under-review');
                        } elseif ($roleType === 'reviewer') {
                            $dashboardUrl = url('reviewer/user/' . $userId . '/articles-under-review');
                        } elseif ($roleType === 'superadmin' || $roleType === 'editor') {
                            $dashboardUrl = url($roleType . '/dashboard/' . $userId . '/articles-under-review');
                        }
                    }
                @endphp
                <a href="{{ $dashboardUrl }}" class="figma-signup-btn">Dashboard</a>
            @endguest
        </div>
    </div>
</nav>

{{-- Announcement Bar (above banner) --}}
<div class="figma-announcement-bar" style="display: block !important; visibility: visible !important; width: 100% !important;">
    <div class="figma-announcement-container">
        <div class="figma-announcement-label">Announcements</div>
        <div class="figma-announcement-content">
            <div class="figma-announcement-marquee">
                <a href="{{ route('showPage', ['slug' => 'submission-guidelines']) }}">Call for submissions is now open. Submit your manuscripts for the first issue by 31st May 2026.</a>
                &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="{{ route('showPage', ['slug' => 'announcements']) }}">HELE Webinar - 3: History of the first adaptation of a Shakespearean play, 20 March 2026, 7.00 PM India time</a>
            </div>
        </div>
    </div>
</div>

