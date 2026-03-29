@extends('master')
@section('title')Archives @stop
@section('description', 'Archives - Studies in History of English Language Education')
@section('content')
    @include('partials.figma-header')

    {{-- Breadcrumbs --}}
    <div class="figma-breadcrumbs">
        <div class="figma-breadcrumbs-container">
            <a href="{{url('/')}}">Home</a>
            <span>/</span>
            <span>Archives</span>
        </div>
    </div>

    {{-- Archives Section --}}
    <section class="figma-archives-section">
        <div class="figma-archives-container">
            {{-- Page Title Banner --}}
            <div class="figma-page-banner">
                <h1>Archives</h1>
            </div>

            {{-- Archives List --}}
            <div class="figma-archives-list">
                <!-- @if(!empty($archives))
                    @foreach($archives as $year => $yearEditions)
                        @foreach($yearEditions as $edition)
                            @php
                                $editionYear = date('Y', strtotime($edition->edition_date));
                                $articleCount = $edition->article_count ?? 0;
                                
                                // Determine status
                                $status = '';
                                if ($edition->edition_status == 0) {
                                    $status = 'In Press';
                                } elseif ($edition->edition_status == 1) {
                                    $status = 'Published';
                                }
                                
                                // Extract volume and number from title
                                preg_match('/Vol\.?\s*(\d+).*No\.?\s*(\d+)/i', $edition->title, $matches);
                                $volume = !empty($matches[1]) ? $matches[1] : '1';
                                $number = !empty($matches[2]) ? $matches[2] : '1';
                                
                                // Format: "2025 (6) In Press : Vol. 2 No. 6 (2025)"
                                if (!empty($status)) {
                                    $editionTitle = $editionYear . ' (' . $articleCount . ') ' . $status . ' : Vol. ' . $volume . ' No. ' . $number . ' (' . $editionYear . ')';
                                } else {
                                    $editionTitle = $editionYear . ' (' . $articleCount . ') : Vol. ' . $volume . ' No. ' . $number . ' (' . $editionYear . ')';
                                }
                            @endphp

                            <div class="figma-archive-item">
                                <div class="figma-archive-title">{{ $editionTitle }}</div>
                                <a href="{{ route('editListing', ['slug' => $edition->slug]) }}" class="figma-view-all-btn">View All</a>
                            </div>
                        @endforeach
                    @endforeach
                @else
                    <div class="figma-no-archives">
                        <p>No archives available at this time.</p>
                    </div>
                @endif -->
                <div class="figma-no-archives">
                        <p>No archives available at this time.</p>
                    </div>
            </div>
        </div>
    </section>

    @include('partials.figma-footer')
@endsection
