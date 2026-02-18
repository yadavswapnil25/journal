@extends('master')

@section('title', 'Terms and Conditions')
@section('description', 'Terms and Conditions for using the SHELE Journal website')

@section('content')
    @include('partials.figma-header')

    {{-- Terms and Conditions Page Content --}}
    <section class="figma-about-section">
        <div class="figma-about-container">
            {{-- Page Title Banner --}}
            <div class="figma-page-banner">
                <h1>Terms and Conditions</h1>
            </div>

            {{-- Main Content --}}
            <div class="figma-about-content">
                <div class="figma-terms-content">
                    <p class="figma-about-description">
                        These Terms and Conditions govern your use of this website with the URL 
                        <a href="https://shele.org" target="_blank" rel="noopener">https://shele.org</a> 
                        and the services provided therein. All mentions of "the website", "this website" or "our website" 
                        below refer to this website with the URL 
                        <a href="https://shele.org" target="_blank" rel="noopener">https://shele.org</a>.
                    </p>

                    <p class="figma-about-description">
                        The access to and use of the website is bound by the following terms and conditions. 
                        By accessing or using the Website, you agree to these terms and conditions.
                    </p>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">1. Eligibility</h2>
                        <p class="figma-terms-text">
                            You must be at least 18 years of age or have the legal authority under applicable law 
                            to agree to these Terms on behalf of another person or entity.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">2. Use of the Website</h2>
                        <p class="figma-terms-text">
                            You agree to use the Website only for lawful purposes and in a way that does not 
                            infringe the rights of any third party or restrict or inhibit anyone else's use of the Website.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">3. Account Registration</h2>
                        <p class="figma-terms-text">
                            Some parts of our Website may require account registration. You agree to provide 
                            accurate and complete information and to keep it up to date. You are responsible 
                            for safeguarding your login credentials.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">4. Payment Terms</h2>
                        <p class="figma-terms-text">
                            A third-party payment gateway may be used to process all online payments. By making 
                            a payment through the Website, you agree to the following:
                        </p>
                        <ul class="figma-terms-list">
                            <li>All transactions will be subject to terms and privacy policy of the third-party payment gateway.</li>
                            <li>You authorize us and payment gateway to charge your selected payment method for the total amount of your order, including any applicable taxes and fees.</li>
                        </ul>
                        <p class="figma-terms-text">
                            We will not store your credit/debit card details. It will be ensured that all sensitive 
                            payment data is handled securely by the payment gateway.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">5. Intellectual Property</h2>
                        <p class="figma-terms-text">
                            All content, trademarks, logos, graphics, and software on the website are the property 
                            of the SHELE Journal or its licensors and protected by copyright and other laws.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">6. Limitation of Liability</h2>
                        <p class="figma-terms-text">
                            We are not liable for any damages resulting from:
                        </p>
                        <ul class="figma-terms-list">
                            <li>Your use or inability to use the Website.</li>
                            <li>Unauthorized access to your data.</li>
                            <li>Third-party content or conduct on the Website.</li>
                        </ul>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">7. Termination</h2>
                        <p class="figma-terms-text">
                            We may suspend or terminate your access to the website at any time without notice 
                            for violating these Terms.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">8. Changes to the Terms</h2>
                        <p class="figma-terms-text">
                            We may update these Terms at any time. Updates will be posted on this page. Your 
                            continued use of the website will confirm your acceptance of those changes.
                        </p>
                    </div>

                    <div class="figma-terms-section">
                        <h2 class="figma-terms-heading">9. Governing Law</h2>
                        <p class="figma-terms-text">
                            These Terms are governed by the laws of India. Legal disputes shall be resolved 
                            in the courts of Maharashtra, India.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.figma-footer')
@endsection


