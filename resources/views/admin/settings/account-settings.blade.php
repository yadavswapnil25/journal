@extends('admin.master') 
@php $breadcrumbs = Breadcrumbs::generate('accountSetting'); @endphp 
@section('breadcrumbs') 
    @if (count($breadcrumbs))
        <ol class="sj-breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb) 
                @if ($breadcrumb->url && !$loop->last)
                    <li><a href="{{{ $breadcrumb->url }}}">{{{ $breadcrumb->title }}}</a></li>
                @else
                    <li class="active">{{{ $breadcrumb->title }}}</li>
                @endif
            @endforeach
        </ol>
    @endif
@endsection
@section('content')
    @php
        $user = Auth::user();
        $user_role_type = App\Models\User::getUserRoleType($user->id);
        $is_author = !empty($user_role_type) && is_object($user_role_type) && $user_role_type->role_type == 'author';
    @endphp
    <div class="container figma-admin-content-wrapper">
        <div class="row">
            <div id="sj-twocolumns" class="sj-twocolumns">
                @include('includes.side-menu')
                <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-9 float-right" id="account_setting">
                    @if (Session::has('message'))
                        <div class="toast-holder">
                            <flash_messages :message="'{{{ Session::get('message') }}}'" :message_class="'success'" v-cloak></flash_messages>
                        </div>
                    @elseif (Session::has('error'))
                        <div class="toast-holder">
                            <flash_messages :message="'{{{ Session::get('error') }}}'" :message_class="'danger'" v-cloak></flash_messages>
                        </div>
                    @elseif ($errors->any())
                        <div class="toast-holder">
                            @foreach ($errors->all() as $error)
                                <flash_messages :message="'{{{$error}}}'" :message_class="'danger'" v-cloak></flash_messages>
                            @endforeach
                        </div>
                   @endif
                    <div id="sj-content" class="sj-content">
                        <div class="sj-addarticleholdcontent">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="sj-dashboardboxtitle" style="margin-bottom: 20px;">
                                        <h2 class="mb-0">{{{trans('prs.change_pswd')}}}</h2>
                                    </div>
                                {!! Form::open(['url' => '/dashboard/general/settings/account-settings/request-new-password', 
                                'class' => 'sj-formtheme sj-formpassword', 'id'=>'request_password']) !!}
                                    <fieldset>
                                        <div class="form-group sj-inputwithicon sj-password">
                                            <i class="lnr lnr-lock"></i> 
                                            @php $old_error = $errors->has("old_password") ? 'is-invalid': "" @endphp 
                                            {!! Form::password('old_password', ['class' => ['.e($old_error).'],'placeholder' => trans('prs.ph_oldpass')]) !!}
                                        </div>
                                        <div class="form-group sj-inputwithicon sj-password">
                                            <i class="lnr lnr-lock"></i> 
                                            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => trans('prs.ph_newpass')]) !!}
                                        </div>
                                        <div class="form-group sj-inputwithicon sj-password">
                                            <i class="lnr lnr-lock"></i> 
                                            {!! Form::password('confirm_password', ['class' => 'form-control','placeholder' => trans('prs.ph_retype_pass')]) !!}
                                        </div>
                                        {!! Form::hidden('user_id', $user_id) !!}
                                    </fieldset>
                                    <div class="sj-btnarea sj-updatebtns">
                                        {!! Form::submit(trans('prs.btn_update'), ['class' => 'sj-btn sj-btnactive']) !!}
                                    </div>
                                {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        @if($is_author)
                        <div class="sj-addarticleholdcontent">
                            <div class="card">
                                <div class="card-body">
                                    <div class="sj-dashboardboxtitle" style="margin-bottom: 20px;">
                                        <h2 class="mb-0">Author Information</h2>
                                    </div>
                                {!! Form::open(['url' => '/dashboard/general/settings/account-settings/update-author-info', 
                                'class' => 'sj-formtheme', 'id'=>'update_author_info']) !!}
                                    <fieldset>
                                        <div class="form-group">
                                            <label>{{trans('prs.ph_firstname')}}</label>
                                            {!! Form::text('name', $user->name, ['class' => 'form-control', 'readonly' => true]) !!}
                                        </div>
                                        <div class="form-group">
                                            <label>{{trans('prs.ph_surname')}}</label>
                                            {!! Form::text('sur_name', $user->sur_name, ['class' => 'form-control', 'readonly' => true]) !!}
                                        </div>
                                        <div class="form-group">
                                            <label>Mobile Number <span class="text-danger">*</span></label>
                                            {!! Form::tel('mobile_number', $user->mobile_number, ['class' => 'form-control', 'required' => true]) !!}
                                        </div>
                                        <div class="form-group">
                                            <label>Institutional Affiliation</label>
                                            {!! Form::text('institutional_affiliation', $user->institutional_affiliation, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            <label>Author's Bio (50 words maximum) <span class="text-danger">*</span></label>
                                            {!! Form::textarea('author_bio', $user->author_bio, ['class' => 'form-control', 'rows' => 4, 'maxlength' => 500, 'required' => true, 'id' => 'author_bio_edit']) !!}
                                            <small class="form-text text-muted">
                                                <span id="word_count_edit">0</span> / 50 words
                                            </small>
                                        </div>
                                        {!! Form::hidden('user_id', $user_id) !!}
                                    </fieldset>
                                    <div class="sj-btnarea sj-updatebtns">
                                        {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
                                        <button type="button" class="btn btn-danger" onclick="deleteAuthorInfo()" style="margin-left: 10px;">Delete</button>
                                    </div>
                                {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($is_author)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('author_bio_edit');
            const wordCountDisplay = document.getElementById('word_count_edit');
            
            function countWords(text) {
                const trimmedText = text.trim();
                if (trimmedText === '') {
                    return 0;
                }
                return trimmedText.split(/\s+/).filter(word => word.length > 0).length;
            }
            
            function updateWordCount() {
                if (textarea && wordCountDisplay) {
                    const text = textarea.value;
                    const wordCount = countWords(text);
                    wordCountDisplay.textContent = wordCount;
                    
                    if (wordCount > 50) {
                        wordCountDisplay.style.color = '#dc3545';
                        textarea.classList.add('is-invalid');
                    } else if (wordCount >= 45) {
                        wordCountDisplay.style.color = '#ffc107';
                        textarea.classList.remove('is-invalid');
                    } else {
                        wordCountDisplay.style.color = '#0066FF';
                        textarea.classList.remove('is-invalid');
                    }
                }
            }
            
            if (textarea) {
                textarea.addEventListener('input', updateWordCount);
                textarea.addEventListener('paste', function() {
                    setTimeout(updateWordCount, 10);
                });
                updateWordCount();
            }
        });
        
        function deleteAuthorInfo() {
            if (confirm('Are you sure you want to delete your author information? This will clear your mobile number, institutional affiliation, and author bio.')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url("/dashboard/general/settings/account-settings/delete-author-info") }}';
                
                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                
                var userId = document.createElement('input');
                userId.type = 'hidden';
                userId.name = 'user_id';
                userId.value = '{{ $user_id }}';
                form.appendChild(userId);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endif
@endsection
