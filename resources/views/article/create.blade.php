@extends('master')
@php $breadcrumbs = Breadcrumbs::generate('checkAuthor'); @endphp
@section('title'){{ trans('prs.add_article') }} - {{ config('app.name') }} @stop
@section('description', 'Submit your article to International Journal of Advanced Research in English Studies')
@section('content')

    @include('partials.figma-header')

    {{-- Create Article Section --}}
    <section class="figma-register-section">
        <div class="figma-register-container">
            <div id="new_article" class="figma-article-wrapper">
                {{-- Main Form --}}
                <div class="figma-article-main">
                    <div class="figma-register-card">
                        <h2>{{{trans('prs.add_article')}}}</h2>
                        
                        @if (Session::has('upload_error'))
                            <div class="figma-alert figma-alert-error">
                                {{ Session::get('upload_error') }}
                            </div>
                        @elseif (Session::has('error'))
                            <div class="figma-alert figma-alert-error">
                                {{ Session::get('error') }}
                            </div>
                        @elseif ($errors->any())
                            <div class="figma-alert figma-alert-error">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <transition name="fade">
                            <div class="figma-alert figma-alert-error" v-if="form_errors.length" v-show="custom_error" v-cloak>
                                <div v-for="error in form_errors" :key="error">
                                    @{{error}}
                                </div>
                            </div>
                        </transition>

                        <div class="provider-site-wrap" v-show="loading" v-cloak>
                            <div class="provider-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>

                        <sticky_messages :message="'Article is Submitting'" v-show="progressing" v-cloak></sticky_messages>

                        {!! Form::open(['url' => 'author/store-article', 'enctype' => 'multipart/form-data', 'multiple' => true,
                        'id'=>'article_form', 'class' => 'figma-form', '@submit' => 'checkForm']) !!}
                            
                            @if(($categories != ""))
                                <div class="figma-form-group">
                                    <label class="figma-form-label">{{trans('prs.category')}}</label>
                                    <span class="sj-select">
                                        {!! Form::select('category', $categories, null ,array('class' => 'figma-form-input figma-form-select')) !!}
                                    </span>
                                </div>
                            @endif

                            <div class="figma-form-group" id="title_input">
                                <label class="figma-form-label">{{trans('prs.article_title')}} <span class="figma-required">*</span></label>
                                {!! Form::text('title', null, ['class' => 'figma-form-input', 'placeholder' => trans('prs.ph_article_title'), 'id'=>'article_title', '@keyup' => 'autoComplete' ]) !!}
                            </div>
                            
                            <div class="figma-form-group">
                                <label class="figma-form-label">{{trans('prs.authors')}} <span class="figma-required">*</span></label>
                                <div class="figma-form-row">
                                    <div class="figma-form-group">
                                        {!! Form::text('authors[0][title]', null, ['class' => 'figma-form-input author_title' ,'id'=>'first_author_name', 'placeholder' => trans('prs.ph_author_name'), '@keyup' => 'autoComplete']) !!}
                                    </div>
                                    <div class="figma-form-group">
                                        {!! Form::email('authors[0][email]', null, ['class' => 'figma-form-input author_email','id'=>'first_author_email','placeholder' => trans('prs.ph_author_email'), '@keyup' => 'autoComplete']) !!}
                                    </div>
                                </div>
                                <div class="figma-add-author-btn">
                                    <button type="button" class="figma-btn figma-btn-secondary" @click="addAnother">
                                        <i class="fa fa-plus"></i> {{trans('prs.add_author')}}
                                    </button>
                                </div>
                            </div>

                            <div v-for="(author, index) in authors" v-cloak class="figma-form-group">
                                <div class="figma-form-row">
                                    <div class="figma-form-group">
                                        <input placeholder="{{{trans('prs.ph_author_name')}}}" v-bind:name="'authors['+[author.count]+'][title]'" type="text" class="figma-form-input" v-model="author.author_name">
                                    </div>
                                    <div class="figma-form-group">
                                        <input placeholder="{{{trans('prs.ph_author_email')}}}" v-bind:name="'authors['+[author.count]+'][email]'" type="email" class="figma-form-input author_email" v-model="author.author_email">
                                    </div>
                                    <div class="figma-form-group" style="max-width: 120px;">
                                        <button type="button" class="figma-btn figma-btn-danger" @click="removeAuthor(index)">
                                            <i class="fa fa-trash"></i> {{trans('prs.remove')}}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="figma-form-group">
                                <label class="figma-form-label">{{trans('prs.add_abstract')}} <span class="figma-required">*</span></label>
                                {!! Form::textarea('abstract', null, ['class' => 'figma-form-input figma-form-textarea', 'id' => 'abstract', 'placeholder' => trans('prs.ph_add_abstract'), 'rows' => 8, '@keyup' => 'autoComplete']) !!}
                            </div>

                            <div class="figma-form-group">
                                <label class="figma-form-label">{{trans('prs.ph_upload_article')}} <span class="figma-required">*</span></label>
                                <upload-files-field
                                    :doc_id="create_article"
                                    :field_title="'{{{trans("prs.ph_upload_article")}}}'"
                                    :file_name="this.file_input_name"
                                    :file_placeholder="'{{{trans("prs.ph_upload_file_label")}}}'"
                                    :file_size_label="'{{{trans("prs.ph_article_file_size")}}}'"
                                    :file_not_uploaded_label="'{{{trans("prs.ph_file_not_uploaded")}}}'">
                                </upload-files-field>
                            </div>

                            <div class="figma-form-group">
                                <label class="figma-checkbox">
                                    <input type="checkbox" id="terms_condition" name="terms_condition" value="accepted" required>
                                    <span>{{ trans('prs.accept_note') }} <a href="javascript:void(0);">{{{trans('prs.terms_conditions')}}}</a></span>
                                </label>
                            </div>

                            <div class="figma-form-group">
                                {!! Form::submit(trans('prs.btn_submit'), ['class' => 'figma-btn figma-btn-primary figma-btn-block']) !!}
                            </div>

                        {!! Form::close() !!}
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="figma-article-sidebar">
                    <div class="figma-register-card">
                        <h3>{{ trans('prs.req_fields') }}</h3>
                        <p class="figma-sidebar-description">{{ trans('prs.foll_fields_must') }}</p>
                        <ul class="figma-requirements-list">
                            <li v-bind:class="{ 'figma-error': this.title_error, 'figma-success': this.title_completed }">
                                <i v-bind:class="{ 'fa fa-times': this.title_na, 'fa fa-check': this.title_check }"></i>
                                <span>{{{trans('prs.article_title')}}}</span>
                            </li>
                            <li v-bind:class="{ 'figma-error': this.author_error, 'figma-success': this.author_completed }">
                                <i v-bind:class="{ 'fa fa-times': this.author_na, 'fa fa-check': this.author_check }"></i>
                                <span>{{{trans('prs.first_author')}}}</span>
                            </li>
                            <li v-bind:class="{ 'figma-error': this.abst_error, 'figma-success': this.abst_completed }">
                                <i v-bind:class="{ 'fa fa-times': this.abst_na, 'fa fa-check': this.abst_check }"></i>
                                <span>{{{trans('prs.add_abstract')}}}</span>
                            </li>
                            <li v-bind:class="{ 'figma-error': this.excerpt_error, 'figma-success': this.excerpt_completed }">
                                <i v-bind:class="{ 'fa fa-times': this.excerpt_na, 'fa fa-check': this.excerpt_check }"></i>
                                <span>{{{trans('prs.add_excerpt')}}}</span>
                            </li>
                            <li class="uploadfilestatus" v-bind:class="{ 'figma-error': this.upload_file_error, 'figma-success': this.upload_file_completed }">
                                <i class="uploadstatusinner" v-bind:class="{ 'fa fa-times': this.upload_file_na, 'fa fa-check': this.upload_file_check }"></i>
                                <span>{{{trans('prs.upload_doc')}}}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.figma-footer')

@endsection
