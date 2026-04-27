@extends('admin.master')
@php $breadcrumbs = Breadcrumbs::generate('createAnnouncement'); @endphp
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
    <div class="container">
        <div class="row">
            <div id="sj-twocolumns" class="sj-twocolumns">
                @include('includes.side-menu')
                <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-9 float-right">
                    @if ($errors->any())
                        <div class="toast-holder">
                            @foreach ($errors->all() as $error)
                                <flash_messages :message="'{{ $error }}'" :message_class="'danger'" v-cloak></flash_messages>
                            @endforeach
                        </div>
                    @endif
                    <div id="sj-contentvtwo" class="sj-content sj-addarticleholdcontent sj-addarticleholdvtwo">
                        <div class="sj-dashboardboxtitle sj-titlewithform">
                            <h2>{{ trans('prs.announcement_add') }}</h2>
                        </div>
                        <div class="sj-manageallsession sj-manageallsessionvtwo">
                            {!! Form::open(['route' => 'storeAnnouncement', 'class' => 'sj-formtheme sj-managesessionform', 'files' => true]) !!}
                                <fieldset>
                                    <div class="form-group">
                                        <label>{{ trans('prs.announcement_message') }}</label>
                                        {!! Form::textarea('message', old('message'), ['class' => 'form-control', 'rows' => 3, 'required' => true, 'placeholder' => trans('prs.announcement_message_ph')]) !!}
                                    </div>
                                    <div class="form-group">
                                        <label>{{ trans('prs.announcement_body') }}</label>
                                        <p class="text-muted small">{{ trans('prs.announcement_body_help') }}</p>
                                        {!! Form::textarea('body', old('body'), ['class' => 'form-control page-textarea', 'rows' => 8]) !!}
                                    </div>
                                    <div class="form-group">
                                        <label>{{ trans('prs.announcement_image') }}</label>
                                        <p class="text-muted small">{{ trans('prs.announcement_image_help') }}</p>
                                        {!! Form::file('image', ['accept' => '.jpg,.jpeg,.png,.webp']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label>{{ trans('prs.announcement_link_slug') }}</label>
                                        <p class="text-muted small">{{ trans('prs.announcement_link_slug_help') }}</p>
                                        {!! Form::text('link_slug', old('link_slug'), ['class' => 'form-control', 'placeholder' => 'announcements']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label>{{ trans('prs.announcement_sort_order') }}</label>
                                        {!! Form::number('sort_order', old('sort_order', 0), ['class' => 'form-control', 'min' => 0]) !!}
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="is_active" value="0">
                                        <span class="sj-checkbox">
                                            {!! Form::checkbox('is_active', '1', old('is_active', true), ['id' => 'is_active']) !!}
                                            {!! Form::label('is_active', trans('prs.announcement_active')) !!}
                                        </span>
                                    </div>
                                </fieldset>
                                <div class="sj-popupbtn">
                                    {!! Form::submit(trans('prs.btn_submit'), ['class' => 'sj-btn sj-btnactive']) !!}
                                    <a href="{{ route('manageAnnouncements') }}" class="sj-btn">{{ trans('prs.btn_cancel') }}</a>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
