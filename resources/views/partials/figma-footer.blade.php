@php 
    $contact_info = App\Models\SiteManagement::getMetaValue('contact_info');
    $contact_email = !empty($contact_info) && !empty($contact_info[0]['email']) ? $contact_info[0]['email'] : 'editor@ijlls.org';
    $contact_phone = !empty($contact_info) && !empty($contact_info[0]['phone_no']) ? $contact_info[0]['phone_no'] : '+91 98765 43210';
    $contact_address = !empty($contact_info) && !empty($contact_info[0]['address']) ? $contact_info[0]['address'] : 'International Journal of Language and Literary Studies, Rome, Italy.';
    $stored_site_title = App\Models\SiteManagement::getMetaValue('site_title');
    $site_title = !empty($stored_site_title) ? $stored_site_title[0]['site_title'] : 'Journal Name';
@endphp

{{-- Footer --}}
<footer class="footer figma-footer" style="display: block !important; visibility: visible !important; width: 100% !important;">
    <div class="container">
        <div class="row">
            <!-- Brand and Description Column -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-brand">
                    <img src="{{asset('images/logo.png')}}" alt="shele" style="width: 100px; height: auto;">    
                    <!-- <p class="figma-footer-tagline">Studies in History of English<br>Language Education</p> -->
                </div>
                <p class="footer-description figma-footer-description">
                    Studies in History of English Language Education (SHELE) is an online, open access, 
                    double blind peer reviewed journal devoted to the history and historiography of the 
                    teaching and learning of English. It offers a forum for intellectual exchange and sharing 
                    of ideas, resources, insights and expertise in HELE in India and globally.
                </p>
                
                <!-- Social Media Icons -->
                <div class="social-icons figma-social-icons">
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Telegram"><i class="fab fa-telegram-plane"></i></a>
                    <!-- <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a> -->
                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <!-- Useful Links Column -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Useful Links</h5>
                <ul>
                    <li><a href="{{ url('/page/about/') }}">About</a></li>
                    <li><a href="{{ url('/page/submission-guidelines/') }}">Submissions</a></li>
                    <li><a href="{{ url('/page/editorial-team/') }}">Editorial Team</a></li>
                    <!-- <li><a href="#">Current Issue</a></li> -->
                    <li><a href="{{ route('archives') }}">Archives</a></li>
                    <!-- <li><a href="#">Announcements</a></li> -->
                </ul>
            </div>
            
            <!-- Legal Column -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Legal</h5>
                <ul>
                    <li><a href="{{ route('showPage', ['slug' => 'journal-policies']) }}">Journal Policy</a></li>
                    <li><a href="{{ url('/page/submission-guidelines/') }}">Submission Guidelines</a></li>
                    <li><a href="{{ route('showPage', ['slug' => 'call-for-submissions']) }}">Call for Submission</a></li>
                    <!-- <li><a href="#">Copyright</a></li> -->
                </ul>
            </div>
            
            <!-- Contact Column -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Contact</h5>
                <div class="contact-info figma-contact-list">
                    <p><strong>Email:</strong><br><a href="mailto:{{$contact_email}}">{{$contact_email}}</a></p>
                    <p><strong>Phone:</strong><br><a href="tel:{{$contact_phone}}">{{$contact_phone}}</a></p>
                    <p><strong>Address:</strong><br>{{$contact_address}}</p>
                </div>
            </div>
        </div>
        
        <!-- Copyright Bar -->
        <div class="footer-bottom figma-footer-bottom">
            <p class="figma-footer-copyright">&copy; {{date("Y")}} {{$site_title}} | All Rights Reserved</p>
        </div>
    </div>
</footer>

