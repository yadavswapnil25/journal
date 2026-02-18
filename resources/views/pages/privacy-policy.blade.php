@extends('master')

@section('title', 'Privacy Policy')
@section('description', 'Privacy Policy for the SHELE Journal website')

@section('content')
    @include('partials.figma-header')

    {{-- Privacy Policy Page Content --}}
    <section class="figma-about-section">
        <div class="figma-about-container">
            {{-- Page Title Banner --}}
            <div class="figma-page-banner">
                <h1>Privacy Policy</h1>
            </div>

            {{-- Main Content --}}
            <div class="figma-about-content">
                <div class="figma-terms-content">
                    <p class="figma-about-description">
                        The SHELE Journal is committed to protecting your personal information and your right to privacy. 
                        This Privacy Policy describes how we collect, use, store, and disclose your information when you 
                        use our website at <a href="https://shele.org" target="_blank" rel="noopener">https://shele.org</a>.
                    </p>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">1. Information We Collect</h2>
                        <p class="figma-terms-text">
                            We may collect the following types of information:
                        </p>
                        <ul class="figma-terms-list">
                            <li><strong>Personal Information:</strong> Name, email address, phone number, billing/shipping address, etc.</li>
                            <li><strong>Payment Details:</strong> Processed securely via Razorpay; we do not store credit/debit card details.</li>
                            <li><strong>Technical Data:</strong> IP address, browser type, device info, access times, and visited pages.</li>
                        </ul>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">2. How We Use Your Information</h2>
                        <p class="figma-terms-text">
                            Your information may be used to:
                        </p>
                        <ul class="figma-terms-list">
                            <li>Process transactions and send order confirmations</li>
                            <li>Provide customer support</li>
                            <li>Send updates, newsletters, or promotions (if subscribed)</li>
                            <li>Improve our services and user experience</li>
                            <li>Prevent fraud or illegal activities</li>
                        </ul>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">3. Sharing Your Data</h2>
                        <p class="figma-terms-text">
                            We do not sell or rent your personal information. However, we may share data with:
                        </p>
                        <ul class="figma-terms-list">
                            <li>Trusted third-party service providers (like payment gateways, email service providers, logistics partners)</li>
                            <li>Law enforcement agencies, if required by law</li>
                        </ul>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">4. Data Security</h2>
                        <p class="figma-terms-text">
                            We take appropriate technical and organizational measures to protect your data from unauthorized 
                            access, loss, or misuse. Payments are processed securely using industry-standard encryption.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">5. Cookies</h2>
                        <p class="figma-terms-text">
                            Our website uses cookies to enhance your browsing experience. You can choose to disable cookies 
                            through your browser settings, although this may affect some functionalities of the site.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">6. Your Rights</h2>
                        <p class="figma-terms-text">
                            You have the right to access, update, or delete your personal information. To request changes, 
                            contact us using the information provided below.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">7. Third-Party Links</h2>
                        <p class="figma-terms-text">
                            Our site may contain links to external websites. We are not responsible for the content or 
                            privacy practices of those sites. Please read their privacy policies before providing any information.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">8. Changes to This Policy</h2>
                        <p class="figma-terms-text">
                            We may update this Privacy Policy from time to time. Changes will be posted on this page with 
                            the updated effective date. We encourage you to review this page regularly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.figma-footer')
@endsection

