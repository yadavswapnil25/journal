@extends('admin.master')
@php $breadcrumbs = Breadcrumbs::generate('manageAnnouncements'); @endphp
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
                    @if (Session::has('message'))
                        <div class="toast-holder">
                            <flash_messages :message="'{{{ Session::get('message') }}}'" :message_class="'success'" v-cloak></flash_messages>
                        </div>
                    @endif
                    <div id="sj-contentvtwo" class="sj-content sj-addarticleholdcontent sj-addarticleholdvtwo">
                        <div class="sj-dashboardboxtitle sj-titlewithform">
                            <h2>{{ trans('prs.marquee_announcements') }}</h2>
                        </div>
                        <div class="sj-manageallsession">
                            <div class="sj-formtheme sj-managesessionform">
                                <fieldset>
                                    <div class="form-group">
                                        <a href="{{ route('createAnnouncement') }}" class="sj-btn sj-btnactive">{{ trans('prs.announcement_add') }}</a>
                                    </div>
                                </fieldset>
                            </div>
                            <ul class="sj-allcategorys">
                                @forelse ($announcements as $row)
                                    <li>
                                        <div class="sj-categorysinfo">
                                            <div class="sj-title">
                                                <h3>{{ $row->message }}</h3>
                                            </div>
                                            <div class="sj-description">
                                                <p>{{ trans('prs.announcement_link_slug') }}: {{ $row->link_slug ?: 'announcements' }} → {{ $row->publicUrl() }}</p>
                                                <p>{{ trans('prs.announcement_sort_order') }}: {{ $row->sort_order }}</p>
                                                <p>{{ trans('prs.announcement_active') }}: {{ $row->is_active ? trans('prs.yes') : trans('prs.no') }}</p>
                                                <p>{{ trans('prs.announcement_image') }}: {{ !empty($row->image) ? trans('prs.yes') : trans('prs.no') }}</p>
                                            </div>
                                        </div>
                                        <div class="sj-categorysbtns">
                                            <a href="{{ route('editAnnouncement', ['id' => $row->id]) }}" class="sj-pencilbtn"><i class="fa fa-pencil"></i></a>
                                            {!! Form::open(['url' => route('deleteAnnouncement', ['id' => $row->id]), 'method' => 'post', 'class' => 'sj-formtheme', 'onsubmit' => "return confirm('Delete this announcement?');"]) !!}
                                                <button type="submit" class="sj-trashbtn"><i class="fa fa-trash"></i></button>
                                            {!! Form::close() !!}
                                        </div>
                                    </li>
                                @empty
                                    <li><p>{{ trans('prs.announcement_none') }}</p></li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
