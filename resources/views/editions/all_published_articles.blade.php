@extends('master')
@section('title')@php echo 'All Published Editions'; @endphp@stop
@section('description', 'This is description tag')
@section('content')
    @include('partials.figma-header')

    <div id="sj-twocolumns" class="sj-twocolumns">
        @php
            $keyword = "";
            $requested_category = array();
            $requested_edition = array();
            $show_records = "";
            !empty($_GET['s']) ? $keyword = $_GET['s'] : '';
            !empty($_GET['show']) ? $show_records = $_GET['show'] : '';
            !empty($_GET['category']) ? $requested_category = $_GET['category'] : array();
            !empty($_GET['edition']) ? $requested_edition = $_GET['edition'] : array();
        @endphp
        <div class="container py-4" id="public_publish_articles">
            <div class="row g-4">
                <div class="col-12 col-lg-9">
                    {!! Form::open(['url' => url('published/editions/filters'), 'method' => 'get', 'id' => 'edition_filters']) !!}
                        <div class="row g-4">
                            <div class="col-12 col-md-5 col-lg-4">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <input type="search" name="s" value="{!! $keyword !!}" class="form-control mb-4" placeholder="{!! trans('prs.ph_search_here') !!}">

                                        @if (!empty($categories))
                                            <h4 class="mb-3">{!! trans('prs.article_type') !!}</h4>
                                            @foreach ($categories as $category)
                                                @php $checked = !empty($requested_category) && in_array($category->id, $requested_category) ? 'checked' : ''; @endphp
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" id="checkbox-{!! $category->id !!}" type="checkbox" name="category[]" value="{!! $category->id !!}" {!! $checked !!}>
                                                    <label class="form-check-label" for="checkbox-{!! $category->id !!}">{!! $category->title !!}</label>
                                                </div>
                                            @endforeach
                                        @endif

                                        @if(!empty($editions))
                                            <h4 class="mt-4 mb-3">{!! trans('prs.by_edition') !!}</h4>
                                            @foreach($editions as $edition)
                                                @php $checked = !empty($requested_edition) && in_array($edition->id, $requested_edition) ? 'checked' : ''; @endphp
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" id="checkbox-{!! $edition->id !!}{!! $edition->id !!}" type="checkbox" name="edition[]" value="{!! $edition->id !!}" {!! $checked !!}>
                                                    <label class="form-check-label" for="checkbox-{!! $edition->id !!}{!! $edition->id !!}">{!! html_entity_decode($edition->title, ENT_QUOTES, 'UTF-8') !!}</label>
                                                </div>
                                            @endforeach
                                        @endif

                                        <button type="submit" class="btn btn-primary w-100 mt-3">{!! trans('prs.apply_filter') !!}</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-7 col-lg-8">
                                @if (Auth::user())
                                    @php
                                        $user_id = Auth::user()->id;
                                        $user_role_type = App\Models\User::getUserRoleType($user_id);
                                        $user_role_type = !empty($user_role_type) && is_object($user_role_type) ? $user_role_type : null;
                                    @endphp
                                    @if (!empty($user_role_type) && $user_role_type->role_type == 'author')
                                        <div class="card shadow-sm border-0 mb-4">
                                            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                                                <div>
                                                    <h5 class="mb-1">{!! trans('prs.upload_article') !!}</h5>
                                                    <p class="mb-0">{!! trans('prs.online_presence') !!}</p>
                                                </div>
                                                <a class="btn btn-outline-primary mt-2 mt-md-0" href="{!! route('checkAuthor') !!}">{!! trans('prs.btn_submit') !!}</a>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="card shadow-sm border-0 mb-4">
                                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                                            <div>
                                                <h5 class="mb-1">{!! trans('prs.upload_article') !!}</h5>
                                                <p class="mb-0">{!! trans('prs.online_presence') !!}</p>
                                            </div>
                                            <a class="btn btn-outline-primary mt-2 mt-md-0" href="{!! route('checkAuthor') !!}">{!! trans('prs.btn_submit') !!}</a>
                                        </div>
                                    </div>
                                @endif

                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                                            <div class="mb-2 mb-md-0">
                                                <label class="mr-2 mb-0">{!! trans('prs.sort_by') !!}</label>
                                                <select name="sort" class="form-control d-inline-block w-auto" @change="onChange()">
                                                    <option value="date">{!! trans('prs.sort_by') !!}</option>
                                                    <option value="title">{!! trans('prs.lbl_name') !!}</option>
                                                    <option value="updated_at">{!! trans('prs.date') !!}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="mr-2 mb-0">{!! trans('prs.show') !!}</label>
                                                <select name="show" class="form-control d-inline-block w-auto" @change="onChange()">
                                                    <option @if ($show_records == 10) selected @endif>10</option>
                                                    <option @if ($show_records == 20) selected @endif>20</option>
                                                    <option @if ($show_records == 30) selected @endif>30</option>
                                                    <option @if ($show_records == 40) selected @endif>40</option>
                                                    <option @if ($show_records == 50) selected @endif>50</option>
                                                </select>
                                            </div>
                                        </div>

                                        @if (!empty($published_articles))
                                            @foreach ($published_articles as $article)
                                                @php $edition_image = App\Helper::getEditionImage($article->edition_id, 'medium'); @endphp
                                                <div class="card mb-3 border">
                                                    <div class="row no-gutters">
                                                        @if (!empty($edition_image))
                                                            <div class="col-md-4">
                                                                <img src="{!! asset($edition_image) !!}" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="{!! trans('prs.article_img') !!}">
                                                            </div>
                                                        @endif
                                                        <div class="{{ !empty($edition_image) ? 'col-md-8' : 'col-12' }}">
                                                            <div class="card-body">
                                                                <p class="mb-1 text-muted">{!! App\Models\User::getUserNameByID($article->corresponding_author_id) !!}</p>
                                                                <h5 class="card-title">
                                                                    <a href="{!! url('article/'.$article->slug) !!}">{!! $article->title !!}</a>
                                                                </h5>
                                                                <p class="card-text mb-3">
                                                                    @php echo \Illuminate\Support\Str::limit($article->excerpt, 120); @endphp
                                                                </p>
                                                                <a class="btn btn-sm btn-primary" href="{!! url('article/'.$article->slug) !!}">
                                                                    {!! trans('prs.btn_view_full_articles') !!}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif (Session::has('message'))
                                            <div class="alert alert-warning mb-0">
                                                {!! Session::get('message') !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}

                    @if (is_object($published_articles) && method_exists($published_articles,'links'))
                        <div class="mt-4">
                            {!! $published_articles->links('pagination.custom') !!}
                        </div>
                    @endif
                </div>

                <div class="col-12 col-lg-3">
                    @include('includes.widgetsidebar')
                </div>
            </div>
        </div>
    </div>

    @include('partials.figma-footer')
@endsection
