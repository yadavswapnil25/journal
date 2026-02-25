@extends('master')
@section('title'){{ config('app.name') }} @stop
@section('description', 'International Journal of Advanced Research in English Studies')
@section('content')
    
    @php
    if (Schema::hasTable('users')){
        $published_articles = collect(App\Models\Article::getPublishedArticle());
        $page_slug  = App\Models\SiteManagement::getMetaValue('pages');
        $page_data = !empty($page_slug) ? App\Models\Page::getPageData($page_slug[0]) : null;
    }
    @endphp

    @include('partials.figma-header')

    {{-- Hero Slider (Banner) --}}
    <section class="figma-hero-slider">
        <div class="figma-slider-container">
            {{-- Slide 1 --}}
            <div class="figma-slide active">
                <img src="{{ asset('images/banner.jpeg') }}" alt="Banner" class="figma-banner-img" width="1200" height="600">
                <div class="figma-hero-content">
                    <!-- Optional overlay text can go here -->
                </div>
            </div>
            {{-- Slide 2 --}}
            <!-- <div class="figma-slide" style="background-image: url({{ asset('images/slider-2.png') }});">
                <div class="figma-hero-content">
                    <h1>International Journal of Advanced Research in English Studies</h1>
                    <p>Publish high-quality research papers, review articles, and case studies with global visibility and fast review process.</p>
                    <div class="figma-hero-buttons">
                        <a href="{{route('checkAuthor')}}" class="figma-btn-primary">Submit Your Article</a>
                        <a href="{{url('published/editions/articles')}}" class="figma-btn-secondary">View Current Issue</a>
                    </div>
                </div>
            </div> -->
        </div>
    </section>

    {{-- Action buttons just below banner --}}
    <section class="figma-banner-actions">
        <div class="figma-banner-actions-container">
            <a href="{{ route('checkAuthor') }}" class="figma-btn-primary">Add New Article</a>
            <a href="{{ url('published/editions/articles') }}" class="figma-btn-secondary">View Current Issue</a>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.figma-slide');
            let currentSlide = 0;
            const slideDelay = 5000; // 5 seconds

            function nextSlide() {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }

            if (slides.length > 1) {
                setInterval(nextSlide, slideDelay);
            }
        });
    </script>

    {{-- Current highlights (Below Hero Slider) --}}
    <section class="figma-trending">
        <h2>Current highlights</h2>
        <!-- <p class="figma-trending-subtitle">
            Literature Review Related to Cultural Identity Challenges of ESL Learners in African Rural Contexts
        </p>
        <a href="{{ url('published/editions/articles') }}" class="figma-trending-readmore">
            Read more
            <span class="arrow">→</span>
        </a> -->

        <div class="figma-trending-grid">
            {{-- Large Featured Card --}}
            <div class="figma-trending-card large">
                <img src="{{ asset('images/img-01.png') }}" alt="Featured Publication">
                <div class="content">
                </div>
            </div>

            {{-- Aims & Scope Card --}}
            <div class="figma-trending-card">
                <div class="icon-circle">📚</div>
                <h3>Aims &amp; Scope</h3>
                <p>
                    SHELE is primarily dedicated to the history of English language education (HELE)
                    in India, and takes HELE in a broad sense.
                </p>
            </div>

            {{-- Publication Information Card --}}
            <div class="figma-trending-card blue">
                <div class="icon-circle light">ℹ️</div>
                <h3>Publication Information</h3>
                <ul class="figma-trending-list">
                    <li><strong>Format</strong>: Online and open access</li>
                    <li><strong>Language</strong>: English</li>
                    <li><strong>Frequency</strong>: Half-yearly</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Issues Section --}}
    @if (!empty($published_articles))
    <section class="figma-issues-section">
        <div class="figma-issues-container">
            <div class="figma-issue-item">
                <div class="figma-issue-header">
                    <h3>2026 - Issues Vol. 1 No. 5 (2026)</h3>
                    <a href="{{ route('archives') }}" class="view-all">View All</a>
                </div>
                <ul class="figma-article-list">
                    @foreach ($published_articles->take(3) as $article)
                    @php
                        $authors = App\Models\Article::getArticleAuthors($article->id);
                        $authorNames = !empty($authors) ? implode(', ', array_column($authors, 'name')) : App\Models\User::getUserNameByID($article->corresponding_author_id);
                    @endphp
                    <li class="figma-article-item">
                        <div class="figma-article-info">
                            <div class="figma-article-title">{{$article->title}}</div>
                            <div class="figma-article-meta">
                                <div class="figma-article-meta-row">
                                    <span class="figma-article-icon">👥</span>
                                    <span>{{$authorNames}}</span>
                                </div>
                                <div class="figma-article-meta-row">
                                    <span class="figma-article-icon">👁️</span>
                                    <span>Abstract views: {{$article->hits ?? 0}}</span>
                                </div>
                                <div class="figma-article-meta-row">
                                    <span class="figma-article-icon">📄</span>
                                    <span>1-15</span>
                                </div>
                            </div>
                        </div>
                        @if(!empty($article->publish_document))
                            <a href="{{route('getPublishFile', $article->publish_document)}}" class="figma-download-btn">Download</a>
                        @else
                            <a href="{{url('article/'.$article->slug)}}" class="figma-download-btn">View</a>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    @endif

    {{-- Newsletter Section --}}
    <section class="figma-newsletter-section">
        <div class="figma-newsletter-content">
            <h2>Subscribe To Our Newsletter</h2>
            <form class="figma-newsletter-form" action="#" method="POST">
                @csrf
                <div class="figma-newsletter-input-wrapper">
                    <input type="email" placeholder="Email" required>
                    <button type="submit" class="figma-newsletter-submit-btn">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 12L10 8L6 4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

    @include('partials.figma-footer')

@endsection
