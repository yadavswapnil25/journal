<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SiteManagementController;
use App\Http\Controllers\EditionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SubscriberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Password reset routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Email verification routes
Route::get('email/verify', [VerificationController::class, 'show'])->middleware('auth')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// Import demo route
Route::get('create-database', function () {
    $exitCode = Artisan::call('migrate:fresh');
    $exitCode = Artisan::call('db:seed');
    return redirect()->back();
});

// Cache clear route
Route::get('cache-clear', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('config:clear');
    return redirect()->back();
});

Route::get(
    '/',
    function () {
        if (Schema::hasTable('users')) {
            return view('home');
        } else {
            if (!empty(env('DB_DATABASE'))) {
                return redirect()->to('/install');
            } else {
                return "please configure database in .env file";
            }
        }
    }
)->name('home');

Route::get('{role}/dashboard/{id}/{status}', [ArticleController::class, 'index'])->name('editorArticles');
Route::get('{role}/dashboard/{id}/{status}/article-search', [ArticleController::class, 'index']);
Route::get('{role}/dashboard/{id}/{status}/{slug}', [ArticleController::class, 'show'])->name('editorArticleDetail');
Route::post('notify-article-review', [ArticleController::class, 'notifyArticleReview']);
Route::post('submit-editor-feedback/{id}', [ArticleController::class, 'submitEditorFeedback']);
Route::post('{role}/dashboard/assign-reviewer', [ArticleController::class, 'assignReviewer']);
Route::post('{role}/dashboard/update-accepted-article', [ArticleController::class, 'updateAcceptedArticle']);

// Author controller routes
Route::get('author/user/{id}/{status}', [AuthorController::class, 'index'])->name('authorArticles');
Route::get('author/user/{id}/{status}/article-search', [AuthorController::class, 'index']);
Route::get('author/create-article', [AuthorController::class, 'create'])->name('checkAuthor');
Route::post('author/store-article', [AuthorController::class, 'store']);
Route::post('author/resubmit-article', [AuthorController::class, 'resubmitArticle']);
Route::post('author/user/article/author-notified', [AuthorController::class, 'authorNotified']);
Route::post('author/user/article/new-article-custom-errors', [ArticleController::class, 'articleCustomErrors']);

// User controller routes
Route::get('superadmin/users/manage-users', [UserController::class, 'index'])->name('manageUsers');
Route::get('superadmin/users/create-users', [UserController::class, 'addUser'])->name('createUser');
Route::post('superadmin/users/store-users', [UserController::class, 'create']);
Route::get('superadmin/users/edit-user/{id}', [UserController::class, 'edit'])->name('editUser');
Route::post('superadmin/users/update-users/{id}', [UserController::class, 'update']);
Route::post('superadmin/users/delete-user', [UserController::class, 'destroy']);
Route::post('superadmin/users/assign-category', [UserController::class, 'assignCategory']);
Route::get('superadmin/products/invoice/{id}', [UserController::class, 'OrderInvoice']);
Route::get('superadmin/downloads', [UserController::class, 'downloadOrders'])->name('orders');
Route::get('superadmin/users/role-filters', [UserController::class, 'index']);
Route::get('user/products/downloads', [UserController::class, 'downloadArticles'])->name('downloads');
Route::get('user/products/checkout/{id}', [UserController::class, 'checkout']);
Route::get('user/products/thankyou', [UserController::class, 'paymentRedirect']);
Route::get('user/products/invoice/{id}', [UserController::class, 'productInvoice']);

// general Settings Category Controller Route
Route::get('dashboard/category/settings', [CategoryController::class, 'index'])->name('categorySetting');
Route::post('dashboard/general/settings/create-category', [CategoryController::class, 'store']);
Route::post('dashboard/general/settings/category-delete', [CategoryController::class, 'destroy']);
Route::post('dashboard/general/settings/edit-category/{id}', [CategoryController::class, 'update']);

// Pages controller routes
Route::get('{role}/dashboard/pages', [PageController::class, 'index'])->name('managePages');
Route::get('{role}/dashboard/{userId}/pages/page/create-page', [PageController::class, 'create'])->name('createPage');
Route::post('{role}/dashboard/pages/store-page', [PageController::class, 'store']);
Route::get('page/{slug}/', [PublicController::class, 'showDetailPage'])->name('showPage');
Route::get('{role}/dashboard/pages/page/{id}/edit-page', [PageController::class, 'edit'])->name('editPage');
Route::post('{role}/dashboard/pages/page/{id}/update-page', [PageController::class, 'update']);
Route::post('{role}/dashboard/pages/page/delete-page', [PageController::class, 'destroy']);

// Site Management Controller Route
Route::get('dashboard/{userRole}/site-management/settings', [SiteManagementController::class, 'index'])->name('manageSite');
Route::post('dashboard/{userRole}/site-management/store-settings', [SiteManagementController::class, 'store']);
Route::post('dashboard/{userRole}/site-management/store/slider-settings', [SiteManagementController::class, 'storeSlidesData']);
Route::post('dashboard/{userRole}/site-management/store/welcome-slider-settings', [SiteManagementController::class, 'storeWelcomeSlidesData']);
Route::post('dashboard/{userRole}/site-management/store/welcome-settings', [SiteManagementController::class, 'storePages']);
Route::post('dashboard/{userRole}/site-management/store/register-settings', [SiteManagementController::class, 'storeRegSettings']);
Route::post('dashboard/{userRole}/site-management/store/success-factor-settings', [SiteManagementController::class, 'storeSuccessFactors']);
Route::post('dashboard/{userRole}/site-management/store/contact-info-settings', [SiteManagementController::class, 'storeContactInfo']);
Route::post('dashboard/{userRole}/site-management/store/about-us-settings', [SiteManagementController::class, 'storeAboutUsNote']);
Route::post('dashboard/{userRole}/site-management/store/notice-board-settings', [SiteManagementController::class, 'storeNotices']);
Route::post('dashboard/{userRole}/site-management/store/language-settings', [SiteManagementController::class, 'storeLanguageSetting']);
Route::post('dashboard/{userRole}/site-management/store-logo', [SiteManagementController::class, 'storeLogo']);
Route::post('dashboard/{userRole}/site-management/delete-logo', [SiteManagementController::class, 'destroySiteLogo']);
Route::get('dashboard/{userRole}/site-management/logo/get-logo', [SiteManagementController::class, 'getSiteLogo']);
Route::post('dashboard/{userRole}/site-management/store/store-resource-pages', [SiteManagementController::class, 'storeResourceMenuPages']);
Route::post('dashboard/{userRole}/site-management/store-advertise-image', [SiteManagementController::class, 'storeAdvertise']);
Route::post('dashboard/{userRole}/site-management/delete-advertise', [SiteManagementController::class, 'destroyAdvertise']);
Route::get('dashboard/{userRole}/site-management/advertise/get-advertise-image', [SiteManagementController::class, 'getAdvertise']);
Route::post('dashboard/{userRole}/site-management/store/site-title-settings', [SiteManagementController::class, 'storeSiteTitle']);
Route::get('dashboard/superadmin/site-management/payment/settings', [SiteManagementController::class, 'setPaymentSetting'])->name('paymentSettings');
Route::post('dashboard/superadmin/site-management/payment/store-payment-settings', [SiteManagementController::class, 'storePaymentSetting']);
Route::post('dashboard/superadmin/site-management/payment/store-product-type', [SiteManagementController::class, 'storeProductType']);
Route::get('dashboard/superadmin/site-management/settings/email', [SiteManagementController::class, 'createEmailSetting'])->name('emailSettings');
Route::post('dashboard/superadmin/site-management/email/store-email-settings', [SiteManagementController::class, 'storeEmailSetting']);
Route::get('dashboard/superadmin/site-management/cache/clear-allcache', [SiteManagementController::class, 'clearAllCache']);
Route::post('superadmin/dashboard/pages/page/edit-page', [SiteManagementController::class, 'getPageOption']);

// Edition Controller Route
Route::get('dashboard/edition/settings', [EditionController::class, 'index'])->name('editionSetting');
Route::get('dashboard/edition/settings/search-edition', [EditionController::class, 'index']);
Route::post('dashboard/general/settings/create-edition', [EditionController::class, 'store']);
Route::get('dashboard/general/settings/edit-edition/{id}', [EditionController::class, 'edit'])->name('editEdition');
Route::post('dashboard/general/settings/delete-edition', [EditionController::class, 'destroy']);
Route::post('dashboard/general/settings/update-edition/{id}', [EditionController::class, 'update']);
Route::post('dashboard/general/settings/publish-edition', [EditionController::class, 'publishEdition']);
Route::get('article/{slug}', [PublicController::class, 'show'])->name('articleDetail');
Route::get('edition/{slug}', [PublicController::class, 'showPublishArticle'])->name('editListing');
Route::post('publish-edition/article/edition-id/', [EditionController::class, 'getEditionID']);
Route::get('published/editions/articles', [PublicController::class, 'filterEdition']);
Route::get('published/editions/filters', [PublicController::class, 'filterEdition']);

// Account Settings Controller Route
Route::get('dashboard/general/settings/account-settings', [SettingController::class, 'index'])->name('accountSetting');
Route::post('/dashboard/general/settings/account-settings/request-new-password', [SettingController::class, 'requestPassword']);
Route::post('/dashboard/general/settings/account-settings/upload-image', [SettingController::class, 'uploadImage']);
Route::get('/dashboard/general/settings/account-settings/get-image', [SettingController::class, 'getImage']);
Route::post('/dashboard/general/settings/account-settings/delete-image', [SettingController::class, 'deleteImage']);

//  Reviewer controller routes
Route::get('reviewer/user/{userId}/{status}', [ReviewerController::class, 'index'])->name('reviewerArticles');
Route::get('reviewer/user/{reviewerId}/{status}/search-article', [ReviewerController::class, 'index']);
Route::get('reviewer-feedback/{reviewerId}/{status}/{id}', [ReviewerController::class, 'show'])->name('reviewerArticleDetail');
Route::post('reviewer/user/submit-feedback/{id}', [ReviewerController::class, 'storeReviewerFeedback']);

//  File controller routes
Route::get('get/{filename}', [FileController::class, 'getFile'])->name('getfile');
Route::get('get-publish-file/{PublishFile}', [PublicController::class, 'getPublishFile'])->name('getPublishFile');

//  Payment controller routes
Route::get('paypal/redirect-url', [PaymentController::class, 'getIndex']);
Route::get('paypal/ec-checkout', [PaymentController::class, 'getExpressCheckout']);
Route::get('paypal/ec-checkout-success', [PaymentController::class, 'getExpressCheckoutSuccess']);
Route::get('paypal/adaptive-pay', [PaymentController::class, 'getAdaptivePay']);
Route::post('paypal/notify', [PaymentController::class, 'notify']);

// Email Template Controller Route
Route::get('dashboard/superadmin/emails/get-email-type', [EmailController::class, 'getEmailType']);
Route::post('dashboard/superadmin/emails/get-email-user-type', [EmailController::class, 'getUserType']);
Route::post('/dashboard/superadmin/emails/get-email-variables', [EmailController::class, 'getEmailVariables']);
Route::get('/dashboard/superadmin/emails/templates', [EmailController::class, 'index'])->name('emailTemplates');
Route::get('/dashboard/superadmin/emails/filter-templates', [EmailController::class, 'index'])->name('emailTemplates');
Route::get('/dashboard/superadmin/emails/create-templates', [EmailController::class, 'create']);
Route::post('/dashboard/superadmin/emails/store-templates', [EmailController::class, 'store']);
Route::get('/dashboard/superadmin/emails/edit-template/{id}', [EmailController::class, 'edit'])->name('editTemplate');
Route::post('/dashboard/superadmin/emails/email/{id}/update-template', [EmailController::class, 'update']);
Route::post('/superadmin/emails/email/delete-template', [EmailController::class, 'destroy']);

// Subscriber Controller Route
Route::post('/prs/store-subscriber', [SubscriberController::class, 'store']);
