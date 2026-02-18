@extends('master')
@php $breadcrumbs = Breadcrumbs::generate('reviewerArticleDetail',$article, $reviewer_id, $status,$id); @endphp
@section('breadcrumbs')
    @if (count($breadcrumbs))
        <ol class="sj-breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($breadcrumb->url && !$loop->last)
                    <li>
                        <a href="{{{ $breadcrumb->url }}}">
                            @if ($breadcrumb->title == "Home")
                                {{{ $breadcrumb->title }}}
                            @else
                                {{{App\Helper::displayArticleBreadcrumbsTitle($breadcrumb->title)}}}
                            @endif
                        </a>
                    </li>
                @else
                    <li class="active">{{{$breadcrumb->title}}}</li>
                @endif
            @endforeach
        </ol>
    @endif
@endsection
@section('content')
    @include('partials.figma-header')
    @include('partials.admin-back-button')
    
    <div class="container figma-admin-content-wrapper" id="reviewer_feedback">
        <div class="row">
            <div id="sj-twocolumns" class="sj-twocolumns">
                <div class="provider-site-wrap" v-show="loading" v-cloak>
                    <div class="provider-loader">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                </div>
                @include('includes.side-menu')
                <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-9 float-right" id="assign_article">
                    @if ($errors->any())
                        <div class="toast-holder">
                            @foreach ($errors->all() as $error)
                                <div id="toast-container">
                                    <div class="alert toast-danger alart-message alert-dismissible fade show fixed_message">
                                        <div class="toast-message">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            {{{$error}}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div id="sj-content" class="sj-content sj-addarticleholdcontent">
                        <div class="sj-dashboardboxtitle sj-titlewithform">
                            <h2>{{{$article[0]->title}}}</h2>
                        </div>
                        <ul id="accordion" class="sj-articledetails sj-articledetailsvtwo">
                            @php
                                $category = App\Models\Category::getCategoryByID($article[0]->article_category_id);
                                $author = App\Models\User::getUserDataByID($article[0]->corresponding_author_id);
                            @endphp
                            <li class="sj-articleheader">
                                <div class="sj-detailstime">
                                    <span><i class="ti-calendar"></i>{{{ Carbon\Carbon::parse($article[0]->created_at)->format('M j H:i:s') }}}</span>
                                    @if (!empty($category))
                                        <span><i class="ti-layers"></i>{{{$category->title}}}</span>
                                    @endif
                                    <span><i class="ti-bookmark"></i>{{ trans('prs.id') }} {{{$article[0]->unique_code}}}</span>
                                </div>
                            </li>
                            <li>
                                @if ($article[0]->status != 'articles_under_review')
                                    <div class="sj-feedbacktitle">
                                        <h2>{{{trans('prs.reviewer_feedback')}}}</h2>
                                    </div>
                                    <div id="subaccordion" class="sj-statusholder">
                                        @foreach ($comments as $comment)
                                            <div id="subheadingOne-{{{$comment->id}}}" class="sj-statusheaderholder sj-statuspadding"
                                                data-toggle="collapse" data-target="#subcollapseOne-{{{$comment->id}}}"
                                                aria-expanded="true" aria-controls="subcollapseOne-{{{$comment->id}}}" role="button">
                                                <figure class="sj-statusimg">
                                                    <img src="{{{asset('images/thumbnails/img-03.jpg')}}}" alt="{{{trans('prs.user_img')}}}">
                                                </figure>
                                                <div class="sj-statusheader">
                                                    <div class="sj-statusasidetitle">
                                                        <span>{{{ Carbon\Carbon::parse($comment->created_at)->format('d-m-Y') }}}</span>
                                                        <h4>
                                                            @if ($comment->status == "major_revisions" )
                                                                {{{trans('prs.major_revisions')}}}
                                                            @else
                                                                {{{trans('prs.minor_revisions')}}}
                                                            @endif
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="subcollapseOne-{{{$comment->id}}}" class="sj-statusdescription collapse sj-active"
                                                aria-labelledby="subheadingOne-{{{$comment->id}}}" data-parent="#subaccordion">
                                                <div class="sj-description">
                                                    {!! App\Helper::formatReviewerComment($comment->comment) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                {!! Form::open(['url' => '/reviewer/user/submit-feedback/'.$article[0]->id, 'class'=>'sj-formtheme sj-formsearchvthree']) !!}
                                    <fieldset>
                                        <div class="sj-dashboardboxtitle sj-titlewithform">
                                            <h2>{{{trans('prs.reply_revision')}}}</h2>
                                        </div>
                                        <div class="form-group sj-firstformgroup">
                                            <span class="sj-select">
                                                {!! Form::select('status', [
                                                    'accepted_articles' => trans('prs.publish_as_it_is'),
                                                    'minor_revisions' => trans('prs.publish_with_minor_revisions'),
                                                    'major_revisions' => trans('prs.publish_after_substantial_revisions'),
                                                    'rejected' => trans('prs.not_recommended_for_publishing'),
                                                    null
                                                ], null, ['class' => 'form-control']) !!}
                                            </span>
                                        </div>

                                        {{-- Detailed reviewer comments --}}
                                        <div class="form-group">
                                            <label><strong>1. Please comment on the quality of the language of the article.</strong></label>
                                            {!! Form::textarea('q_language', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                        </div>

                                        <div class="form-group">
                                            <label><strong>2. Please comment on the originality and overall quality of the content of the article.</strong></label>
                                            {!! Form::textarea('q_originality', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                        </div>

                                        <div class="form-group">
                                            <label><strong>3. Does the article follow appropriate norms of referencing, citation and presentation?</strong></label>
                                            {!! Form::textarea('q_norms', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                        </div>

                                        <div class="form-group">
                                            <label><strong>4. Any comments for the editors? (These will not be shared with the author/s)</strong></label>
                                            {!! Form::textarea('q_editors', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                        </div>

                                        <div class="form-group">
                                            <label><strong>5. Comments for the author/s.</strong></label>
                                            {!! Form::textarea('q_authors', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                        </div>

                                        <div class="form-group">
                                            <label><strong>6. Will you be willing to review a revised manuscript of this article (where revision is required)?</strong></label>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    {!! Form::radio('willing_to_review', 'yes', false, ['class' => 'form-check-input']) !!} YES
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    {!! Form::radio('willing_to_review', 'no', false, ['class' => 'form-check-input']) !!} NO
                                                </label>
                                            </div>
                                        </div>

                                        {!! Form::hidden('article', $article[0]->id) !!}
                                    </fieldset>
                                    <div class="sj-popupbtn sj-popupbtnvtwo">
                                        {!! Form::submit(trans('prs.btn_submit'), ['class' => 'sj-btn sj-btnactive','v-on:click' => 'showloading']) !!}
                                    </div>
                                {!! Form::close() !!}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @include('partials.figma-footer')
@endsection
