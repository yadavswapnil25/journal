@extends('master')
@php $breadcrumbs = Breadcrumbs::generate('checkAuthor'); @endphp
@section('title'){{ trans('prs.add_article') }} - {{ config('app.name') }} @stop
@section('description', 'Submit your article to International Journal of Advanced Research in English Studies')
@section('content')

    @include('partials.figma-header')

    {{-- Create Article Section --}}
    <section class="figma-register-section">
        <div class="figma-register-container">
            <div id="new_article" class="figma-article-wrapper" data-abstract-error-template="{{ trans('prs.ph_article_desc_error_with_count') }}">
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
                            <div class="figma-alert figma-alert-error" v-if="(error_title || error_author_name || error_author_email || error_abstract || error_excerpt || error_doc)" v-show="custom_error" v-cloak>
                                <div>Please fix the errors below.</div>
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
                        'id'=>'article_form', 'class' => 'figma-form', '@submit.prevent' => 'checkForm']) !!}
                            
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
                                {!! Form::text('title', null, ['class' => 'figma-form-input ' . ($errors->has('title') ? 'is-invalid' : ''), 'placeholder' => trans('prs.ph_article_title'), 'id'=>'article_title', '@keyup' => 'autoComplete' ]) !!}
                                @if ($errors->has('title'))
                                    <span class="figma-form-error">{{ $errors->first('title') }}</span>
                                @endif
                                <span class="figma-form-error" v-if="error_title" v-cloak>@{{ error_title }}</span>
                            </div>
                            
                            <div class="figma-form-group">
                                <label class="figma-form-label">{{ trans('prs.main_author') }}</label>
                                <p class="form-control-plaintext" style="margin-bottom: 0; padding: 8px 0;">
                                    <strong>{{ Auth::user()->name }} {{ Auth::user()->sur_name }}</strong> &mdash; {{ Auth::user()->email }}
                                </p>
                                <small class="form-text text-muted">{{ trans('prs.main_author_you') }}</small>
                                <div class="figma-form-group" style="margin-top: 10px;">
                                    <label class="figma-form-label">{{ trans('prs.first_author_bio') }} <span class="figma-required">*</span></label>
                                    @php
                                        $existingBio = Auth::user() && Auth::user()->author_bio ? Auth::user()->author_bio : null;
                                    @endphp
                                    @if ($existingBio)
                                        <small class="form-text text-muted" style="margin-bottom: 6px;">
                                            You already have a BIO saved. You can reuse it as it is, or edit it below to update.
                                        </small>
                                    @endif
                                    <textarea id="first_author_bio" name="authors[0][bio]" class="figma-form-input figma-form-textarea author-bio-field {{ $errors->has('authors.0.bio') ? 'is-invalid' : '' }}" rows="3" placeholder="{{ trans('prs.author_bio_placeholder') }}" maxlength="500" required>{{ old('authors.0.bio', $existingBio ?? '') }}</textarea>
                                    <small class="form-text text-muted"><span id="first_author_bio_words">0</span> / 50 {{ trans('prs.words_max') }}</small>
                                    @if ($errors->has('authors.0.bio'))
                                        <span class="figma-form-error">{{ $errors->first('authors.0.bio') }}</span>
                                    @endif
                                </div>
                                <div class="figma-add-author-btn">
                                    <button type="button" class="figma-btn figma-btn-secondary" @click="addAnother" :disabled="authors.length >= 4">
                                        <i class="fa fa-plus"></i> {{trans('prs.add_author')}}
                                    </button>
                                    <small class="form-text text-muted" style="margin-top: 6px;">{{ trans('prs.max_coauthors_note') }}</small>
                                </div>
                            </div>

                            <div v-for="(author, index) in authors" v-cloak class="figma-form-group" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-top: 12px; background: #fafafa;">
                                <h4 class="figma-form-label" style="margin-bottom: 12px; font-size: 1rem;">{{ trans('prs.co_author') }} @{{ index + 1 }}</h4>
                                <div class="figma-form-row">
                                    <div class="figma-form-group">
                                        <label class="figma-form-label">{{ trans('prs.ph_author_name') }}</label>
                                        <input placeholder="{{ trans('prs.ph_author_name') }}" v-bind:name="'authors['+author.count+'][title]'" type="text" class="figma-form-input" v-model="author.author_name">
                                    </div>
                                    <div class="figma-form-group">
                                        <label class="figma-form-label">{{ trans('prs.ph_author_email') }}</label>
                                        <input placeholder="{{ trans('prs.ph_author_email') }}" v-bind:name="'authors['+author.count+'][email]'" type="email" class="figma-form-input author_email" v-model="author.author_email">
                                    </div>
                                    <div class="figma-form-group" style="max-width: 120px; align-self: flex-end;">
                                        <button type="button" class="figma-btn figma-btn-danger" @click="removeAuthor(index)">
                                            <i class="fa fa-trash"></i> {{ trans('prs.remove') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="figma-form-group" style="margin-top: 12px;">
                                    <label class="figma-form-label">{{ trans('prs.co_author_bio') }} @{{ index + 1 }}</label>
                                    <textarea v-bind:name="'authors['+author.count+'][bio]'" class="figma-form-input figma-form-textarea" rows="3" placeholder="{{ trans('prs.author_bio_placeholder') }}" maxlength="500" v-model="author.author_bio"></textarea>
                                    <small class="form-text text-muted">{{ trans('prs.optional_50_words') }}</small>
                                </div>
                            </div>

                            <div class="figma-form-group">
                                <label class="figma-form-label">{{trans('prs.add_abstract')}} <span class="figma-required">*</span></label>
                                {!! Form::textarea('abstract', null, ['class' => 'figma-form-input figma-form-textarea ' . ($errors->has('abstract') ? 'is-invalid' : ''), 'id' => 'abstract', 'placeholder' => trans('prs.ph_add_abstract'), 'rows' => 8, '@keyup' => 'autoComplete', '@input' => 'autoComplete']) !!}
                                <small class="form-text text-muted">@{{ abstract_word_count }} / {{ trans('prs.abstract_word_range') }}</small>
                                @if ($errors->has('abstract'))
                                    <span class="figma-form-error">{{ $errors->first('abstract') }}</span>
                                @endif
                                <span class="figma-form-error" v-if="error_abstract" v-cloak>@{{ error_abstract }}</span>
                            </div>

                            <div class="figma-form-group">
                                <label class="figma-form-label">{{ trans('prs.add_excerpt') }} <span class="figma-required">*</span></label>
                                {!! Form::textarea('excerpt', null, ['class' => 'figma-form-input figma-form-textarea ' . ($errors->has('excerpt') ? 'is-invalid' : ''), 'id' => 'excerpt', 'placeholder' => trans('prs.add_excerpt'), 'rows' => 3, '@keyup' => 'autoComplete']) !!}
                                @if ($errors->has('excerpt'))
                                    <span class="figma-form-error">{{ $errors->first('excerpt') }}</span>
                                @endif
                                <span class="figma-form-error" v-if="error_excerpt" v-cloak>@{{ error_excerpt }}</span>
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
                                <span class="figma-form-error" v-if="error_doc" v-cloak>@{{ error_doc }}</span>
                            </div>

                            <div class="figma-form-group">
                                <label class="figma-checkbox">
                                    <input type="checkbox" id="terms_condition" name="terms_condition" value="accepted" required>
                                    <span>{{ trans('prs.accept_note') }} <a href="javascript:void(0);">{{{trans('prs.terms_conditions')}}}</a></span>
                                </label>
                            </div>

                            <div class="figma-form-group">
                                <button type="submit" class="figma-btn figma-btn-primary figma-btn-block" :disabled="submitDisabled" v-bind:title="submitDisabled ? (error_abstract || '') : ''">{{ trans('prs.btn_submit') }}</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('first_author_bio');
            const wordCountDisplay = document.getElementById('first_author_bio_words');
            if (!textarea || !wordCountDisplay) return;
            function countWords(text) {
                const t = (text || '').trim();
                return t === '' ? 0 : t.split(/\s+/).filter(function(w) { return w.length > 0; }).length;
            }
            function updateWordCount() {
                const count = countWords(textarea.value);
                wordCountDisplay.textContent = count;
                if (count > 50) {
                    wordCountDisplay.style.color = '#dc3545';
                    textarea.classList.add('is-invalid');
                } else if (count >= 45) {
                    wordCountDisplay.style.color = '#ffc107';
                    textarea.classList.remove('is-invalid');
                } else {
                    wordCountDisplay.style.color = '#0066FF';
                    textarea.classList.remove('is-invalid');
                }
            }
            textarea.addEventListener('input', updateWordCount);
            textarea.addEventListener('paste', function() { setTimeout(updateWordCount, 10); });
            updateWordCount();
        });
    </script>
@endsection
