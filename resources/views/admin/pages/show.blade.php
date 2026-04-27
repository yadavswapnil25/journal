@extends('master')
@section('title'){{ $page->title }} @stop
@section('description', "$meta_desc")
@php 
    $breadcrumbs = Breadcrumbs::generate('showPage',$page,$slug);
    $is_about_page = strtolower($page->title) === 'about' || 
                     strtolower($slug) === 'about' || 
                     strtolower($slug) === 'about-the-journal' ||
                     strpos(strtolower($page->title), 'about') !== false;

    $is_aims_scope_page = strtolower($page->title) === 'aims & scope' || 
                          strtolower($page->title) === 'aims and scope' ||
                          strtolower($slug) === 'aims-scope' || 
                          strtolower($slug) === 'aims-&-scope' ||
                          strtolower($slug) === 'aims-and-scope' ||
                          (strpos(strtolower($page->title), 'aims') !== false && strpos(strtolower($page->title), 'scope') !== false);

    $is_publication_info_page = strtolower($page->title) === 'publication information' || 
                                strtolower($page->title) === 'publication info' ||
                                strtolower($slug) === 'publication-information' || 
                                strtolower($slug) === 'publication-info' ||
                                (strpos(strtolower($page->title), 'publication') !== false && strpos(strtolower($page->title), 'information') !== false);

    $is_submission_guidelines_page = strtolower($page->title) === 'submission guidelines' ||
                                     strtolower($page->title) === 'submissions guidelines' ||
                                     strtolower($slug) === 'submission-guidelines' ||
                                     strtolower($slug) === 'submissions-guidelines' ||
                                     (strpos(strtolower($page->title), 'submission') !== false && strpos(strtolower($page->title), 'guideline') !== false);

    $is_call_for_submissions_page = strtolower($page->title) === 'call for submissions' ||
                                    strtolower($page->title) === 'call for submission' ||
                                    strtolower($slug) === 'call-for-submissions' ||
                                    strtolower($slug) === 'call-for-submission' ||
                                    strtolower($slug) === 'call-for-papers' ||
                                    (strpos(strtolower($page->title), 'call') !== false && strpos(strtolower($page->title), 'submission') !== false);

    $is_journal_policies_page = strtolower($page->title) === 'journal policies' ||
                                strtolower($page->title) === 'policies' ||
                                strtolower($slug) === 'journal-policies' ||
                                strtolower($slug) === 'policies' ||
                                strtolower($slug) === 'policy' ||
                                (strpos(strtolower($page->title), 'journal') !== false && strpos(strtolower($page->title), 'polic') !== false);
    
    // Get the active policy section from query parameter
    $active_policy = request()->get('section', 'review-policy');

    $is_editor_in_chief_page = strtolower($page->title) === 'editor-in-chief' ||
                               strtolower($slug) === 'editor-in-chief' ||
                               strtolower($slug) === 'editor-in-chief-page' ||
                               (strpos(strtolower($page->title), 'editor') !== false && strpos(strtolower($page->title), 'chief') !== false);

    $is_editorial_board_page = strtolower($page->title) === 'editorial board' ||
                               strtolower($slug) === 'editorial-board' ||
                               strtolower($slug) === 'editorial-team' ||
                               strtolower($slug) === 'editorial-board-page' ||
                               (strpos(strtolower($page->title), 'editorial') !== false && strpos(strtolower($page->title), 'board') !== false);

    $is_advisory_board_page = strtolower($page->title) === 'advisory board' ||
                              strtolower($slug) === 'advisory-board' ||
                              strtolower($slug) === 'advisory-board-page' ||
                              (strpos(strtolower($page->title), 'advisory') !== false && strpos(strtolower($page->title), 'board') !== false);

    $is_special_issues_page = strtolower($page->title) === 'special issues and guest editors' ||
                              strtolower($slug) === 'special-issues' ||
                              strtolower($slug) === 'special-issues-and-guest-editors' ||
                              (strpos(strtolower($page->title), 'special issues') !== false);

    $is_announcements_page = strtolower($page->title) === 'announcements' ||
                             strtolower($slug) === 'announcements' ||
                             strtolower($slug) === 'announcement';
@endphp
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
    @include('partials.figma-header')
    @if($is_about_page)
        
        {{-- Announcements Banner --}}

        {{-- About Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>About</h1>
                </div>

                {{-- Main Content --}}
                <div class="figma-about-content">
                    <h2 class="figma-about-title">About the Journal</h2>
                    <p class="figma-about-description">
                        Studies in History of English Language Education (SHELE) is an online, open access, 
                        double blind peer reviewed journal devoted to the history and historiography of the 
                        teaching and learning of English. It offers a forum for intellectual exchange and sharing 
                        of ideas, resources, insights and expertise in HELE in India and globally.
                    </p>

                    {{-- Two Column Layout --}}
                    <div class="figma-about-columns">
                        {{-- Left Column: Aims & Scope --}}
                        <div class="figma-about-column figma-aims-scope">
                            <div class="figma-column-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14 2V8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h3>Aims & Scope</h3>
                            <p>
                                SHELE is primarily dedicated to the history of English language education (HELE) 
                                in India, and takes HELE in a broad sense. It offers a forum for intellectual 
                                exchange and sharing of ideas, resources, insights and expertise in HELE in 
                                India and globally. The journal welcomes contributions that explore historical 
                                perspectives on English language teaching and learning, curriculum development, 
                                pedagogical innovations, and the socio-cultural contexts of English education.
                            </p>
                        </div>

                        {{-- Right Column: Publication Information --}}
                        <div class="figma-about-column figma-publication-info">
                            <div class="figma-column-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 17L12 22L22 17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 12L12 17L22 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h3>Publication Information</h3>
                            <ul class="figma-publication-list">
                                <li><strong>Format</strong> - online and open access</li>
                                <li><strong>Language</strong> - English</li>
                                <li><strong>Publication frequency</strong> - Half-yearly</li>
                                <li><strong>Publication dates</strong> - January and June</li>
                                <li><strong>Submissions</strong> - open round the year</li>
                                <li><strong>Publisher</strong> - AINET Association of English Teachers, in collaboration with HELE-India Group and HELE Society of India</li>
                                <li><strong>ISSN</strong> - awaited</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_aims_scope_page)

        {{-- Aims & Scope Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>Aims & Scope</h1>
                </div>

                {{-- Main Content --}}
                <div class="figma-about-content">
                    <div class="figma-aims-scope-full">
                        <p class="figma-about-description">
                            SHELE is primarily dedicated to the history of English language education (HELE) in India. 
                            HELE is taken in a broad sense of the teaching and learning of English and through English 
                            in India and includes other aspects of the history of education which may have a direct 
                            bearing on HELE. Though the thrust area of the journal is India, it also welcomes contributions 
                            from outside India, convinced that HELE studies from other contexts can offer useful ideas 
                            and insights to enrich the understanding of HELE in India and in the process such sharing 
                            will benefit the domain as a whole. Similarly, the central focus is history and historiography 
                            of ELE, but the editors would be happy to consider contributions from allied fields such as 
                            linguistics, anthropology, sociology, culture studies and so on, which may aid our explorations 
                            in HELE.
                        </p>
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_submission_guidelines_page)
        <!-- @include('partials.figma-header') -->

        {{-- Submission Guidelines Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>Submission Guidelines</h1>
                </div>

                {{-- Main Content --}}
                <div class="figma-about-content">
                    <h2 class="figma-about-title">Submission Guidelines</h2>

                    <p class="figma-about-description">
                        The following types of contributions are invited for the issues of the journal:
                    </p>

                    <div class="figma-submission-guidelines">
                        <ul class="figma-submission-list">
                            <li><strong>Feature article</strong> – a detailed and substantial piece of writing (about 7000–8000 words) based on rigorous academic work focusing on important research issues in and/or theoretical approaches to HELE.</li>
                            <li><strong>Conceptual article</strong> – a shorter piece of writing (about 3000–5000 words) discussing specific theoretical, conceptual or interpretative aspects of HELE.</li>
                            <li><strong>Research/Working paper</strong> – reports (about 3000–5000 words) based on research studies.</li>
                            <li><strong>Book reviews</strong> – critical reviews of recent and relevant publications (about 1000–1500 words).</li>
                            <li><strong>Field notes</strong> – short descriptive notes (about 500–800 words) reporting on particular historical resources, archives, collections, persons, agencies, micro-histories or any field observations specifically related to HELE.</li>
                        </ul>

                        <p style="margin-top: 1.25rem;">
                            Submissions for feature and conceptual articles and research/working papers will undergo blind peer review by at least two reviewers.
                        </p>

                        <h3 style="margin-top: 2rem;">Guidelines for writing</h3>
                        <p>All manuscripts must be submitted as Word files, conforming to the following requirements:</p>
                        <ul class="figma-submission-list">
                            <li><strong>Entire document:</strong> Times New Roman fonts, 1.5 spacing and left aligned.</li>
                            <li><strong>Title:</strong> Max. 15 words, size 14, bold.</li>
                            <li><strong>Section headings:</strong> size 12, bold.</li>
                            <li><strong>Section sub-headings:</strong> size 12, italics.</li>
                            <li><strong>Section headings and sub-headings</strong> NOT to be numbered.</li>
                            <li><strong>Body text:</strong> size 12.</li>
                            <li>No footnotes please; kindly use endnotes.</li>
                            <li>For referencing, in-text citations and other matters, please follow APA style (7th Edn.). More details can be found <a href="https://apastyle.apa.org" target="_blank" rel="noopener noreferrer">here</a> or <a href="https://owl.purdue.edu/owl/research_and_citation/apa_style/apa_formatting_and_style_guide/index.html" target="_blank" rel="noopener noreferrer">here</a>.</li>
                        </ul>

                        <p style="margin-top: 1rem;">Manuscripts of feature and conceptual articles and research/working papers should include in the beginning –</p>
                        <ul class="figma-submission-list">
                            <li>An abstract (max. 200 words)</li>
                            <li>Keywords (between 4 and 8)</li>
                            <li>But NOT include author name(s), affiliations, contact details or any other personal information.</li>
                        </ul>
                        <p>Abstracts and keywords are NOT required for book reviews and field notes.</p>

                        <h3 style="margin-top: 2rem;">File format</h3>
                        <ul class="figma-submission-list">
                            <li>Only Word or Text (RTF) files are accepted.</li>
                            <li>Authors may be asked to submit separate files of tables, graphs, images or any visuals used in the article during or after the review.</li>
                        </ul>

                        <div class="figma-call-for-submissions-note" style="margin-top: 2rem;">
                            <h3>How to Submit</h3>
                            <p>
                                <a href="{{ route('showPage', ['slug' => 'submission-guidelines']) }}">CLICK HERE</a> to see the Submissions Guidelines.<br>
                                <a href="{{ route('checkAuthor') }}">CLICK HERE</a> to go to the Submissions page.<br>
                                First-time submitters will need to create an author account through a very easy process.
                            </p>
                        </div>

                        <div class="figma-call-for-submissions-note" style="margin-top: 2rem;">
                            <h3>Note</h3>
                            <p>
                                By making the submission the authors confirm that they have read, understood and agreed to respect the policies of the journal regarding the <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=review-policy">review process</a>, <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=anti-plagiarism-policy">anti-plagiarism</a>, <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=data-protection">data protection, privacy and digital preservation</a>, <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=ethical-standards">ethics, copyright and conflicts of interest</a>, among others.
                            </p>
                            <p style="margin-top: 1rem; margin-bottom: 0;">
                                Address any queries related to the journal to <a href="mailto:shelejournal@gmail.com">shelejournal@gmail.com</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_call_for_submissions_page)
        <!-- @include('partials.figma-header') -->

        {{-- Call for Submissions Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>Call for Submissions</h1>
                </div>

                {{-- Main Content --}}
                <div class="figma-about-content">
                    <h2 class="figma-about-title">Call for Submissions</h2>
                    <p class="figma-about-description">
                        The following types of contributions are invited for publication in the issues of the journal:
                    </p>

                    <div class="figma-submission-guidelines">
                        <div class="figma-submission-type">
                            <h3>Feature article</h3>
                            <p>A detailed and substantial piece of writing (about 7000–8000 words) based on rigorous academic work focusing on important research issues in and/or theoretical approaches to HELE.</p>
                        </div>

                        <div class="figma-submission-type">
                            <h3>Conceptual article</h3>
                            <p>A shorter piece of writing (about 3000–5000 words) discussing specific theoretical, conceptual or interpretative aspects of HELE.</p>
                        </div>

                        <div class="figma-submission-type">
                            <h3>Research/Working paper</h3>
                            <p>Reports (about 3000–5000 words) based on research studies.</p>
                        </div>

                        <div class="figma-submission-type">
                            <h3>Book reviews</h3>
                            <p>Critical reviews of recent and relevant publications (about 1000–1500 words).</p>
                        </div>

                        <div class="figma-submission-type">
                            <h3>Field notes</h3>
                            <p>Short descriptive notes (about 500–800 words) reporting on particular historical resources, archives, collections, persons, agencies, micro-histories or any field observations specifically related to HELE.</p>
                        </div>
                    </div>

                    <p class="figma-about-description" style="margin-top: 1.25rem;">
                        Submissions for feature and conceptual articles and research/working papers will undergo blind peer review by at least two reviewers.
                    </p>

                    <div class="figma-call-for-submissions-note">
                        <h3>How to Submit</h3>
                        <p>
                            <a href="{{ route('showPage', ['slug' => 'submission-guidelines']) }}">CLICK HERE</a> to see the Submissions Guidelines.<br>
                            <a href="{{ route('checkAuthor') }}">CLICK HERE</a> to go to the Submissions page.<br>
                            First-time submitters will need to create an author account through a very easy process.
                        </p>
                    </div>

                    <div class="figma-call-for-submissions-note">
                        <h3>Timelines</h3>
                        <ul class="figma-submission-list" style="margin-bottom: 0;">
                            <li>Call for submissions is open round the year.</li>
                            <li>Special calls for any other kinds of contributions will be separately announced from time to time.</li>
                            <li>Each volume has two issues published at half-yearly intervals.</li>
                            <li>Submissions received until 31st January will be considered for the first issue of the year. Those received by 30th June will be considered for the second issue.</li>
                            <li>Processing timeline is normally four months from submission to the final decision.</li>
                        </ul>
                    </div>

                    <div class="figma-call-for-submissions-note">
                        <h3>Important Notes</h3>
                        <ul class="figma-submission-list" style="margin-bottom: 0;">
                            <li>Submission of an article is taken to imply that it has not been published previously and is not under consideration for publication elsewhere.</li>
                            <li>SHELE does not charge any fee for publication in the journal.</li>
                        </ul>
                        <p style="margin-top: 1rem; margin-bottom: 0;">
                            Any queries related to the journal may be sent to <a href="mailto:shelejournal@gmail.com">shelejournal@gmail.com</a>.
                        </p>
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_special_issues_page)
        {{-- Special Issues and Guest Editors (under Submissions) --}}
        <section class="figma-policies-section">
            <div class="figma-policies-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>Special Issues and Guest Editors</h1>
                </div>

                {{-- Main Content with Sidebar --}}
                <div class="figma-policies-wrapper">
                    {{-- Left Sidebar Navigation --}}
                    <div class="figma-policies-sidebar">
                        <nav class="figma-policies-nav">
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=review-policy" 
                               class="figma-policy-nav-item {{ $active_policy === 'review-policy' ? 'active' : '' }}">
                                <span class="figma-policy-number">1.</span>
                                <span class="figma-policy-text">Review Policy</span>
                            </a>
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=anti-plagiarism-policy" 
                               class="figma-policy-nav-item {{ $active_policy === 'anti-plagiarism-policy' ? 'active' : '' }}">
                                <span class="figma-policy-number">2.</span>
                                <span class="figma-policy-text">Anti-plagiarism Policy</span>
                            </a>
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=ethical-standards" 
                               class="figma-policy-nav-item {{ $active_policy === 'ethical-standards' ? 'active' : '' }}">
                                <span class="figma-policy-number">3.</span>
                                <span class="figma-policy-text">Ethical Standards, Copyright And Conflicts Of Interest</span>
                            </a>
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=data-protection" 
                               class="figma-policy-nav-item {{ $active_policy === 'data-protection' ? 'active' : '' }}">
                                <span class="figma-policy-number">4.</span>
                                <span class="figma-policy-text">Data Protection, Privacy, Digital Preservation</span>
                            </a>
                            <!-- <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=subscription-policy" 
                               class="figma-policy-nav-item {{ $active_policy === 'subscription-policy' ? 'active' : '' }}">
                                <span class="figma-policy-number">5.</span>
                                <span class="figma-policy-text">Subscription Policy</span>
                            </a> -->
                        </nav>
                    </div>

                    {{-- Right Content Area --}}
                    <div class="figma-policies-content">
                        @if($active_policy === 'review-policy')
                            <h2 class="figma-policy-title">REVIEW POLICY</h2>
                            <div class="figma-policy-text-content">
                                <p>1. Preliminary selection: The editors will make an initial assessment of the manuscript to determine whether it fits the scope of the journal and meets basic quality standards. Manuscripts that do not meet these criteria will be rejected without external review.</p>
                                
                                <p>2. Blind review: Manuscripts that pass the initial screening will be sent for blind review to at least two external reviewers who are experts in the relevant field. Reviewers will evaluate the manuscript based on criteria such as originality, significance, methodology, clarity, and relevance to the journal's scope.</p>
                                
                                <p>3. Revision: Based on the reviewers' comments, authors may be asked to revise their manuscript. Revised manuscripts will be re-evaluated by the editors and, if necessary, sent back to the reviewers for further assessment.</p>
                                
                                <p>4. Final decision: The final decision on acceptance or rejection will be made by the editors based on the reviewers' recommendations and the manuscript's overall quality. Before publication, the manuscript will be checked for ethics, plagiarism, conflict of interest, and copyright.</p>
                            </div>
                        @elseif($active_policy === 'anti-plagiarism-policy')
                            <h2 class="figma-policy-title">ANTI-PLAGIARISM POLICY</h2>
                            <div class="figma-policy-text-content">
                                <p>SHELE is committed to maintaining the highest standards of academic integrity. All submitted manuscripts are screened for plagiarism using appropriate software tools. Authors are expected to ensure that their work is original and properly cited. Any instances of plagiarism will result in immediate rejection of the manuscript and may lead to further action.</p>
                                
                                <p>Authors must:</p>
                                <ul>
                                    <li>Ensure all sources are properly cited and referenced</li>
                                    <li>Obtain permission for any copyrighted material used</li>
                                    <li>Disclose any previous publication of the work or parts thereof</li>
                                    <li>Not submit the same work to multiple journals simultaneously</li>
                                </ul>
                            </div>
                        @elseif($active_policy === 'ethical-standards')
                            <h2 class="figma-policy-title">ETHICAL STANDARDS, COPYRIGHT AND CONFLICTS OF INTEREST</h2>
                            <div class="figma-policy-text-content">
                                <p><strong>Ethical Standards:</strong> Authors must adhere to ethical guidelines in research and publication. This includes obtaining informed consent from participants, ensuring data confidentiality, and reporting research findings accurately and honestly.</p>
                                
                                <p><strong>Copyright:</strong> Upon acceptance, authors grant SHELE the right to publish their work. Authors retain copyright and may use their published work for educational and research purposes. All content published in SHELE is licensed under Creative Commons Attribution License.</p>
                                
                                <p><strong>Conflicts of Interest:</strong> Authors, reviewers, and editors must disclose any potential conflicts of interest that could influence the research, review process, or editorial decisions. Conflicts may include financial relationships, personal relationships, or professional affiliations.</p>
                            </div>
                        @elseif($active_policy === 'data-protection')
                            <h2 class="figma-policy-title">DATA PROTECTION, PRIVACY, DIGITAL PRESERVATION</h2>
                            <div class="figma-policy-text-content">
                                <p><strong>Data Protection:</strong> SHELE is committed to protecting the personal data of authors, reviewers, and readers. All personal information is collected and processed in accordance with applicable data protection laws.</p>
                                
                                <p><strong>Privacy:</strong> Personal information provided during submission and review processes is kept confidential and used only for editorial purposes. Reviewers' identities are not disclosed to authors, and authors' identities are not disclosed to reviewers during the blind review process.</p>
                                
                                <p><strong>Digital Preservation:</strong> SHELE ensures long-term digital preservation of all published content through appropriate archiving systems. Published articles are archived and made accessible through various digital repositories and library systems.</p>
                            </div>
                        @elseif($active_policy === 'subscription-policy')
                            <h2 class="figma-policy-title">SUBSCRIPTION POLICY</h2>
                            <div class="figma-policy-text-content">
                                <p>SHELE is an open-access journal. The content of the journal is freely available to readers without any subscription fees as of now. There are no charges for accessing, downloading, or reading articles published in SHELE.</p>
                                
                                <p><strong>Open Access:</strong> All articles published in SHELE are available online upon publication. Readers can access, download, and share articles without restrictions, subject to proper attribution.</p>
                                
                                <p><strong>Article Processing Charges:</strong> SHELE does not charge authors for publication. The journal is does not ask for any article processing charges (APCs) or publication fees.</p>
                                
                                <p><strong>Archiving:</strong> All published content is archived and preserved for long-term access through digital repositories and library systems.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_journal_policies_page)
        <!-- @include('partials.figma-header') -->

        {{-- Journal Policies Page Content --}}
        <section class="figma-policies-section">
            <div class="figma-policies-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>Journal Policies</h1>
                </div>

                {{-- Main Content with Sidebar --}}
                <div class="figma-policies-wrapper">
                    {{-- Left Sidebar Navigation --}}
                    <div class="figma-policies-sidebar">
                        <nav class="figma-policies-nav">
                            <!-- <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=review-policy" 
                               class="figma-policy-nav-item {{ $active_policy === 'review-policy' ? 'active' : '' }}">
                                <span class="figma-policy-number">1.</span>
                                <span class="figma-policy-text">Review Policy</span>
                            </a>
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=anti-plagiarism-policy" 
                               class="figma-policy-nav-item {{ $active_policy === 'anti-plagiarism-policy' ? 'active' : '' }}">
                                <span class="figma-policy-number">2.</span>
                                <span class="figma-policy-text">Anti-plagiarism Policy</span>
                            </a>
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=ethical-standards" 
                               class="figma-policy-nav-item {{ $active_policy === 'ethical-standards' ? 'active' : '' }}">
                                <span class="figma-policy-number">3.</span>
                                <span class="figma-policy-text">Ethical Standards, Copyright And Conflicts Of Interest</span>
                            </a>
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=data-protection" 
                               class="figma-policy-nav-item {{ $active_policy === 'data-protection' ? 'active' : '' }}">
                                <span class="figma-policy-number">4.</span>
                                <span class="figma-policy-text">Data Protection, Privacy, Digital Preservation</span>
                            </a> -->
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=subscription-policy" 
                               class="figma-policy-nav-item {{ $active_policy === 'subscription-policy' ? 'active' : '' }}">
                                <span class="figma-policy-number">1.</span>
                                <span class="figma-policy-text">Subscription Policy</span>
                            </a>
                        </nav>
                    </div>

                    {{-- Right Content Area --}}
                    <div class="figma-policies-content">
                        @if($active_policy === 'review-policy')
                        <h2 class="figma-policy-title">SUBSCRIPTION POLICY</h2>
                            <div class="figma-policy-text-content">
                                <p>SHELE is an open-access journal. The content of the journal is freely available to readers without any subscription fees as of now. There are no charges for accessing, downloading, or reading articles published in SHELE.</p>
                                
                                <p><strong>Open Access:</strong> All articles published in SHELE are available online upon publication. Readers can access, download, and share articles without restrictions, subject to proper attribution.</p>
                                
                                <p>The journal is does not ask for any article processing charges (APCs) or publication fees.</p>
                                
                                <p><strong>Archiving:</strong> All published content is archived and preserved for long-term access through digital repositories and library systems.</p>
                            </div>
                            <!-- <h2 class="figma-policy-title">REVIEW POLICY</h2>
                            <div class="figma-policy-text-content">
                                <p>1. Preliminary selection: The editors will make an initial assessment of the manuscript to determine whether it fits the scope of the journal and meets basic quality standards. Manuscripts that do not meet these criteria will be rejected without external review.</p>
                                
                                <p>2. Blind review: Manuscripts that pass the initial screening will be sent for blind review to at least two external reviewers who are experts in the relevant field. Reviewers will evaluate the manuscript based on criteria such as originality, significance, methodology, clarity, and relevance to the journal's scope.</p>
                                
                                <p>3. Revision: Based on the reviewers' comments, authors may be asked to revise their manuscript. Revised manuscripts will be re-evaluated by the editors and, if necessary, sent back to the reviewers for further assessment.</p>
                                
                                <p>4. Final decision: The final decision on acceptance or rejection will be made by the editors based on the reviewers' recommendations and the manuscript's overall quality. Before publication, the manuscript will be checked for ethics, plagiarism, conflict of interest, and copyright.</p>
                            </div> -->
                        @elseif($active_policy === 'anti-plagiarism-policy')
                            <h2 class="figma-policy-title">ANTI-PLAGIARISM POLICY</h2>
                            <div class="figma-policy-text-content">
                                <p>SHELE is committed to maintaining the highest standards of academic integrity. All submitted manuscripts are screened for plagiarism using appropriate software tools. Authors are expected to ensure that their work is original and properly cited. Any instances of plagiarism will result in immediate rejection of the manuscript and may lead to further action.</p>
                                
                                <p>Authors must:</p>
                                <ul>
                                    <li>Ensure all sources are properly cited and referenced</li>
                                    <li>Obtain permission for any copyrighted material used</li>
                                    <li>Disclose any previous publication of the work or parts thereof</li>
                                    <li>Not submit the same work to multiple journals simultaneously</li>
                                </ul>
                            </div>
                        @elseif($active_policy === 'ethical-standards')
                            <h2 class="figma-policy-title">ETHICAL STANDARDS, COPYRIGHT AND CONFLICTS OF INTEREST</h2>
                            <div class="figma-policy-text-content">
                                <p><strong>Ethical Standards:</strong> Authors must adhere to ethical guidelines in research and publication. This includes obtaining informed consent from participants, ensuring data confidentiality, and reporting research findings accurately and honestly.</p>
                                
                                <p><strong>Copyright:</strong> Upon acceptance, authors grant SHELE the right to publish their work. Authors retain copyright and may use their published work for educational and research purposes. All content published in SHELE is licensed under Creative Commons Attribution License.</p>
                                
                                <p><strong>Conflicts of Interest:</strong> Authors, reviewers, and editors must disclose any potential conflicts of interest that could influence the research, review process, or editorial decisions. Conflicts may include financial relationships, personal relationships, or professional affiliations.</p>
                            </div>
                        @elseif($active_policy === 'data-protection')
                            <h2 class="figma-policy-title">DATA PROTECTION, PRIVACY, DIGITAL PRESERVATION</h2>
                            <div class="figma-policy-text-content">
                                <p><strong>Data Protection:</strong> SHELE is committed to protecting the personal data of authors, reviewers, and readers. All personal information is collected and processed in accordance with applicable data protection laws.</p>
                                
                                <p><strong>Privacy:</strong> Personal information provided during submission and review processes is kept confidential and used only for editorial purposes. Reviewers' identities are not disclosed to authors, and authors' identities are not disclosed to reviewers during the blind review process.</p>
                                
                                <p><strong>Digital Preservation:</strong> SHELE ensures long-term digital preservation of all published content through appropriate archiving systems. Published articles are archived and made accessible through various digital repositories and library systems.</p>
                            </div>
                        @elseif($active_policy === 'subscription-policy')
                            <h2 class="figma-policy-title">SUBSCRIPTION POLICY</h2>
                            <div class="figma-policy-text-content">
                                <p>SHELE is an open-access journal. The content of the journal is freely available to readers without any subscription fees as of now. There are no charges for accessing, downloading, or reading articles published in SHELE.</p>
                                
                                <p><strong>Open Access:</strong> All articles published in SHELE are available online upon publication. Readers can access, download, and share articles without restrictions, subject to proper attribution.</p>
                                
                                <p>The journal is does not ask for any article processing charges (APCs) or publication fees.</p>
                                
                                <p><strong>Archiving:</strong> All published content is archived and preserved for long-term access through digital repositories and library systems.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_editor_in_chief_page)
        <!-- @include('partials.figma-header') -->  

        {{-- Editor-in-Chief Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>Editor-in-Chief</h1>
                </div>

                {{-- Main Content --}}
                <div class="figma-about-content">
                    <div class="figma-editor-in-chief-content">
                        <div class="figma-editor-profile">
                            <h2 class="figma-editor-name">Prof. Amol Padwad</h2>
                            <div class="figma-editor-details">
                                <p class="figma-editor-position"><strong>President</strong>, AINET Association of English Teachers</p>
                                <p class="figma-editor-position"><strong>Former Director</strong>, Centre for English Language Education, Dr. B. R. Ambedkar University Delhi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_editorial_board_page)
        <!-- @include('partials.figma-header') -->

        {{-- Editorial Board Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>Editorial Board</h1>
                </div>

                {{-- Main Content --}}
                <div class="figma-about-content">
                    <div class="figma-editorial-board-content">
                        <div class="figma-editorial-board-list">
                            <div class="figma-board-member">
                                <span class="figma-member-number">1.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Dr. Atanu Bhattacharya</h3>
                                    <p class="figma-member-position">Professor and Head, Department of English Studies, Central University Gujarat</p>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">2.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Dr. Krishna Dixit</h3>
                                    <p class="figma-member-position">Deputy Dean, School of Letters, Dr. B. R. Ambedkar University Delhi</p>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">3.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Dr. Prithvirajsingh Thakur</h3>
                                    <p class="figma-member-position">Professor, Department of English, G. S. College, Khamgaon, Maharashtra</p>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">4.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Dr. R. Vennela</h3>
                                    <p class="figma-member-position">Department of Humanities and Social Sciences, National Institute of Technology, Warangal</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_advisory_board_page)
        {{-- Advisory Board Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                <div class="figma-page-banner">
                    <h1>Advisory Board</h1>
                </div>
                <div class="figma-about-content">
                    <p class="figma-about-description" style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem;">
                        Studies in History of English Language Education (SHELE)
                    </p>
                    <ul class="figma-publication-info-list" style="margin: 0; padding-left: 1.25rem;">
                        <li><strong>Prof. Shreesh Chaudhary</strong> - (Retd.) Department of HSS, IIT Madras</li>
                        <li><strong>Prof. Santosh Mahapatra</strong> - Department of HSS, BITS-Pilani, Hyderabad Campus</li>
                        <li><strong>Dr. Leya Matthew</strong> - School of Arts &amp; Sciences, Ahmedabad University</li>
                        <li><strong>Prof. Sunita Mishra</strong> - Centre for English Language Studies, University of Hyderabad</li>
                        <li><strong>Prof. Parimala Rao</strong> - ZHCES, Jawaharlal Nehru University Delhi</li>
                        <li><strong>Prof. Asma Rasheed</strong> - Department of ELT, EFL University Hyderabad</li>
                        <li><strong>Prof. Richard Smith</strong> - ELT &amp; Applied Linguistics, University of Warwick, UK</li>
                        <li><strong>Prof. M. Sridhar</strong> - (Retd.) Department of English, University of Hyderabad</li>
                    </ul>
                </div>
            </div>
        </section>

    @elseif($is_announcements_page)
        {{-- Announcements Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                <div class="figma-page-banner">
                    <h1>Announcements</h1>
                </div>
                <div class="figma-about-content">
                    @if (isset($siteAnnouncementList) && $siteAnnouncementList->count())
                        @foreach ($siteAnnouncementList as $item)
                            <article class="figma-announcement-item" style="margin-bottom: 2rem; padding: 1.75rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #0066FF; display: flex; flex-wrap: wrap; gap: 20px;">
                                @if (! empty($item->image))
                                    <div class="figma-announcement-image" style="flex: 0 0 260px; max-width: 100%;">
                                        <img src="{{ asset($item->image) }}" alt="Announcement" style="width: 100%; height: auto; border-radius: 6px;">
                                    </div>
                                @endif
                                <div class="figma-announcement-text" style="flex: 1 1 260px; min-width: 0;">
                                    <h2 style="margin: 0 0 0.75rem 0; font-size: 1.25rem;">{{ $item->message }}</h2>
                                    @if (! empty($item->body))
                                        <div class="sj-description" style="margin: 0;">{!! $item->body !!}</div>
                                    @endif
                                    <p style="margin: 1rem 0 0 0;">
                                        <a href="{{ $item->publicUrl() }}">{{ trans('prs.announcement_more_link') }}</a>
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    @else
                        <p>{{ trans('prs.announcement_none') }}</p>
                    @endif
                </div>
            </div>
        </section>

    @elseif($is_publication_info_page)
        <!-- @include('partials.figma-header') -->

        {{-- Publication Information Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>Publication Information</h1>
                </div>

                {{-- Main Content --}}
                <div class="figma-about-content">
                    <div class="figma-publication-info-full">
                        <ul class="figma-publication-info-list">
                            <li><strong>Format</strong> – online and open access</li>
                            <li><strong>Language</strong> – English</li>
                            <li><strong>Publication frequency</strong> – Half-yearly</li>
                            <li><strong>Publication dates</strong> – January and June</li>
                            <li><strong>Submissions</strong> – open round the year</li>
                            <li><strong>Publisher</strong> – AINET Association of English Teachers, in collaboration with HELE-India Group and HELE Society of India.</li>
                            <li><strong>ISSN</strong> – awaited</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

    @else
        {{-- Old design for other pages --}}
        <div id="sj-twocolumns" class="sj-twocolumns">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-8 col-lg-9 float-left" id="article">
                        <div class="sj-aboutus">
                            <div class="sj-introduction sj-sectioninnerspace">
                                <span>{{{$page->sub_title}}}</span>
                                <h4>{{{$page->title}}}</h4>
                            </div>
                            <div class="sj-description">
                                @php echo htmlspecialchars_decode(stripslashes($page->body)); @endphp
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-8 col-lg-3 float-left" id="article">
                        @include('includes.detailsidebar')
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('partials.figma-footer')
@endsection
