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
                               strtolower($slug) === 'editorial-board-page' ||
                               (strpos(strtolower($page->title), 'editorial') !== false && strpos(strtolower($page->title), 'board') !== false);

    $is_advisory_board_page = strtolower($page->title) === 'advisory board' ||
                              strtolower($slug) === 'advisory-board' ||
                              strtolower($slug) === 'advisory-board-page' ||
                              (strpos(strtolower($page->title), 'advisory') !== false && strpos(strtolower($page->title), 'board') !== false);
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
        @include('partials.figma-header')

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

                    <div class="figma-submission-guidelines">
                        <h3>1. Scope of submissions</h3>
                        <ul class="figma-submission-list">
                            <li>We welcome original research articles, theoretical papers, review essays, practice-based reports and case studies related to the history and historiography of English language education (HELE).</li>
                            <li>Contributions may focus on India or other contexts, provided they offer insights that enrich our understanding of HELE in India and globally.</li>
                        </ul>

                        <h3>2. Manuscript preparation</h3>
                        <ul class="figma-submission-list">
                            <li>Articles are normally between <strong>5,000–8,000 words</strong> (including references). Short reports or notes may be <strong>2,000–3,000 words</strong>.</li>
                            <li>Submit manuscripts in <strong>.doc / .docx</strong> format, double-spaced, with a clear, readable font (e.g. 12pt Times New Roman or similar).</li>
                            <li>Include a separate title page with author name(s), affiliation(s), ORCID (if any), and contact details.</li>
                            <li>The main article file should be suitably anonymised for double blind review.</li>
                            <li>Provide a structured abstract of <strong>150–200 words</strong> and <strong>4–6 keywords</strong>.</li>
                            <li>Use a consistent referencing style (e.g. <strong>APA 7th edition</strong>) throughout the manuscript.</li>
                        </ul>

                        <h3>3. Ethics and permissions</h3>
                        <ul class="figma-submission-list">
                            <li>Authors are responsible for obtaining informed consent and necessary institutional approvals where required.</li>
                            <li>All participant data should be anonymised. Pseudonyms may be used where appropriate.</li>
                            <li>Any use of copyrighted material (figures, tables, long quotations) must be properly acknowledged and used with permission.</li>
                        </ul>

                        <h3>4. Review process & decisions</h3>
                        <ul class="figma-submission-list">
                            <li>All manuscripts undergo an initial editorial screening for fit and basic quality.</li>
                            <li>Suitable manuscripts are sent for <strong>double blind peer review</strong> to at least two reviewers.</li>
                            <li>Based on reviewers’ recommendations, the editor will reach one of the following decisions: accept, minor revisions, major revisions, or reject.</li>
                        </ul>
                    </div>

                    <h2 class="figma-submission-flow-title">Peer Review &amp; Decision Flow</h2>

                    <div class="figma-submission-flow">
                        <div class="figma-submission-step">
                            <h4>Step 1 – Submission</h4>
                            <p>The author creates an account, fills in the online submission form, uploads the manuscript and submits the article.</p>
                        </div>

                        <div class="figma-submission-step">
                            <h4>Step 2 – Initial editorial check</h4>
                            <p>The editor screens the submission for scope, basic quality and ethical compliance, and identifies suitable reviewers.</p>
                        </div>

                        <div class="figma-submission-step">
                            <h4>Step 3 – Reviewer assignment &amp; review</h4>
                            <p>Reviewers are invited and, once they agree, they receive the anonymised manuscript and submit their detailed review reports and recommendations.</p>
                        </div>

                        <div class="figma-submission-step">
                            <h4>Step 4 – Editorial decision</h4>
                            <p>The editor reads the reviews and makes an initial decision: acceptance, minor revisions, major revisions, or rejection.</p>
                        </div>

                        <div class="figma-submission-step">
                            <h4>Step 5 – Minor revisions loop</h4>
                            <p>If minor revisions are required, the editor sends the decision and consolidated comments to the author. The author revises and resubmits. The editor checks the revised file and may accept or request further changes.</p>
                        </div>

                        <div class="figma-submission-step">
                            <h4>Step 6 – Major revisions loop</h4>
                            <p>For major revisions, the author prepares a substantially revised manuscript. The editor may send this version for a second round of review before making a final decision.</p>
                        </div>

                        <div class="figma-submission-step">
                            <h4>Step 7 – Final acceptance &amp; production</h4>
                            <p>Once accepted, the final version is prepared for publication (PDF formatting and proofreading). The editor checks the final files before they are published in the appropriate issue.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_call_for_submissions_page)
        @include('partials.figma-header')

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
                        Submissions are invited for the following kinds of original contributions:
                    </p>

                    <div class="figma-submission-guidelines">
                        <div class="figma-submission-type">
                            <h3>Feature article (6000-8000 words)</h3>
                            <p>A detailed and substantial write-up focused on some significant research issue(s)</p>
                        </div>

                        <div class="figma-submission-type">
                            <h3>Conceptual articles (3000-4000 words)</h3>
                            <p>Critical discussion of theoretical or conceptual positions, arguments or approaches, promoting fresh understandings, interpretations or formulations</p>
                        </div>

                        <div class="figma-submission-type">
                            <h3>Research/ Working papers (2000-3000 words)</h3>
                            <p>Reporting on small-scale research studies and their outcomes</p>
                        </div>

                        <div class="figma-submission-type">
                            <h3>Book reviews (1000 words)</h3>
                            <p>Reviews of recent and significant publications of value and potential for HELE</p>
                        </div>

                        <div class="figma-submission-type">
                            <h3>Field notes (200-800 words)</h3>
                            <p>Brief notes describing interesting and relevant observations or findings from field work</p>
                        </div>
                    </div>

                    <div class="figma-call-for-submissions-note">
                        <h3>Brief Notes</h3>
                        <p>
                            SHELE also welcomes brief notes (max. 500 words) on happenings related to HELE, initiatives to promote HELE studies and research, opportunities for HELE related work, etc. Please mail these directly to <a href="mailto:shelejournal@gmail.com">shelejournal@gmail.com</a> for the consideration of the editorial team.
                        </p>
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_journal_policies_page)
        @include('partials.figma-header')

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
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=special-issues" 
                               class="figma-policy-nav-item {{ $active_policy === 'special-issues' ? 'active' : '' }}">
                                <span class="figma-policy-number">5.</span>
                                <span class="figma-policy-text">Special Issues And Guest Editors</span>
                            </a>
                            <a href="{{ route('showPage', ['slug' => 'journal-policies']) }}?section=subscription-policy" 
                               class="figma-policy-nav-item {{ $active_policy === 'subscription-policy' ? 'active' : '' }}">
                                <span class="figma-policy-number">6.</span>
                                <span class="figma-policy-text">Subscription Policy</span>
                            </a>
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
                        @elseif($active_policy === 'special-issues')
                            <h2 class="figma-policy-title">SPECIAL ISSUES AND GUEST EDITORS</h2>
                            <div class="figma-policy-text-content">
                                <p>SHELE welcomes proposals for special issues on topics of particular interest to the HELE community. Special issues are typically guest-edited by experts in the field.</p>
                                
                                <p><strong>Proposal Process:</strong> Proposals for special issues should include:</p>
                                <ul>
                                    <li>A detailed description of the topic and its significance</li>
                                    <li>Proposed guest editor(s) and their qualifications</li>
                                    <li>A list of potential contributors or a call for papers</li>
                                    <li>A proposed timeline for submission and publication</li>
                                </ul>
                                
                                <p><strong>Guest Editor Responsibilities:</strong> Guest editors are responsible for soliciting submissions, coordinating the review process, and ensuring the quality and coherence of the special issue. All submissions undergo the same rigorous peer review process as regular submissions.</p>
                            </div>
                        @elseif($active_policy === 'subscription-policy')
                            <h2 class="figma-policy-title">SUBSCRIPTION POLICY</h2>
                            <div class="figma-policy-text-content">
                                <p>SHELE is an open-access journal, meaning all content is freely available to readers without subscription fees. There are no charges for accessing, downloading, or reading articles published in SHELE.</p>
                                
                                <p><strong>Open Access:</strong> All articles published in SHELE are made immediately available online upon publication. Readers can access, download, and share articles without restrictions, subject to proper attribution.</p>
                                
                                <p><strong>Article Processing Charges:</strong> SHELE does not charge authors for publication. The journal is supported by institutional funding and does not require article processing charges (APCs) or publication fees.</p>
                                
                                <p><strong>Archiving:</strong> All published content is archived and preserved for long-term access through digital repositories and library systems.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_editor_in_chief_page)
        @include('partials.figma-header')

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
        @include('partials.figma-header')

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
        @include('partials.figma-header')

        {{-- Advisory Board Page Content --}}
        <section class="figma-about-section">
            <div class="figma-about-container">
                {{-- Page Title Banner --}}
                <div class="figma-page-banner">
                    <h1>Advisory Board</h1>
                </div>

                {{-- Main Content --}}
                <div class="figma-about-content">
                    <div class="figma-editorial-board-content">
                        <div class="figma-editorial-board-list">
                            <div class="figma-board-member">
                                <span class="figma-member-number">1.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. Sunita Mishra</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">2.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. Parimala Rao</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">3.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. M. Sridhar</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">4.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. Richard Smith</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">5.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. Santosh Mahapatra</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">6.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. Shreesh Chaudhary</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">7.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. Asma Rashid EFLU</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">8.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. Prachi Deshpande</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">9.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Dr. Leya Matthew</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">10.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Dr. Fredericke Kippel</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">11.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. Marcelo Karuso</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">12.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Prof. Nicolla McLelland</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">13.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Dr. Xi Li</h3>
                                </div>
                            </div>

                            <div class="figma-board-member">
                                <span class="figma-member-number">14.</span>
                                <div class="figma-member-details">
                                    <h3 class="figma-member-name">Mr. Simon Dunton</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @elseif($is_publication_info_page)
        @include('partials.figma-header')

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
