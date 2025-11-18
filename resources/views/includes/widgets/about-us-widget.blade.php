@php
    $social_list = App\Helper::getSocialData();
    $social_unserialize_array = App\Models\SiteManagement::getMetaValue('socials');
    $about_us_note = App\Models\SiteManagement::getMetaValue('about_us');
@endphp
<div class="sj-fcol sj-footeraboutus">
    <strong class="sj-logo">
        <a href="{{{url('/')}}}">
            <img id="footer_site_logo" src="{{{asset(App\Models\SiteManagement::getLogo())}}}" alt="{{{trans('prs.scientific_journal')}}}">
        </a>
    </strong> 
    @if (!empty($about_us_note))
        <div class="sj-description">
            {{{$about_us_note}}}
        </div>
    @endif
    <ul class="sj-socialicons sj-socialiconssimple">
        @if (!empty($social_unserialize_array))
            @foreach ($social_unserialize_array as $key => $value) 
                @if (array_key_exists($value['title'],$social_list))
                    @php $socialList = $social_list[$value['title']]; @endphp
                    <li class="sj-{{{$value['title']}}}">
                        <a href="https://{{{$value['url']}}}" target="_blank">
                            <i class="fa {{{$socialList['icon']}}}">
                            </i>
                        </a>
                    </li>
                @endif
            @endforeach 
        @endif
    </ul>
</div>
