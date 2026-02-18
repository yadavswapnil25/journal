@php
    $user_roles_type = null;
    if (!empty(Auth::user()->id)) {
        $user_roles_type = App\Models\User::getUserRoleType(Auth::user()->id);
        $user_roles_type = !empty($user_roles_type) && is_object($user_roles_type) ? $user_roles_type : null;
    }
@endphp

<div class="figma-admin-back-button-wrapper">
    {{-- Submit New Article button (authors only) --}}
    @if (!empty($user_roles_type) && $user_roles_type->role_type === 'author')
        <a href="{{ route('checkAuthor') }}" class="figma-back-button figma-back-button-secondary">
            <i class="lnr lnr-pencil"></i>
            <span>{{ trans('prs.add_article') }}</span>
        </a>
    @endif

    {{-- Back button --}}
    <a href="javascript:void(0);" onclick="window.history.back();" class="figma-back-button">
        <i class="lnr lnr-arrow-left"></i>
        <span>Back</span>
    </a>
</div>

