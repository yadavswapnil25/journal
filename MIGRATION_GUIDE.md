# Laravel 5.7 to Laravel 10 Migration Guide

## Overview
This document outlines the migration process from Laravel 5.7 (PHP 7.1.3) to Laravel 10 (PHP 8.1+).

## Completed Steps
✅ Created new Laravel 10 project in `journals-upgraded` folder
✅ Updated `composer.json` with Laravel 10 compatible packages
✅ Migrated `Author` model
✅ Migrated `Category` model

## Remaining Tasks

### 1. Models Migration (Priority: HIGH)
All models need to be migrated from `App\` to `App\Models\` namespace:

**Files to migrate:**
- [ ] `Article.php` - Complex model with many methods
- [ ] `Edition.php` - Uses `str_slug()` → needs `Str::slug()`
- [ ] `User.php` - Already exists, needs updating
- [ ] `Page.php` - Uses `str_slug()` → needs `Str::slug()`
- [ ] `Reviewer.php`
- [ ] `Subscriber.php`
- [ ] `Invoice.php`
- [ ] `Item.php`
- [ ] `IPNStatus.php`
- [ ] `EmailTemplate.php`
- [ ] `SiteManagement.php`
- [ ] `UploadMedia.php`

**Key changes needed:**
- Change namespace from `namespace App;` to `namespace App\Models;`
- Update relationship definitions: `'Article'` → `Article::class`
- Replace `str_slug()` with `use Illuminate\Support\Str;` and `Str::slug()`
- Replace `filter_var(..., FILTER_SANITIZE_STRING)` with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- Update `collect()->isEmpty()` logic where needed
- Add proper type hints and return types

### 2. Helper Class (Priority: HIGH)
- [ ] Move `Helper.php` to `app/Helpers/Helper.php` (or keep in `app/`)
- [ ] Remove `extends Model` - Helper should not extend Model
- [ ] Update all references to use proper namespaces
- [ ] Fix `str_slug()` → `Str::slug()`

### 3. Controllers Migration (Priority: HIGH)
All controllers need updates:

**Files to migrate:**
- [ ] `ArticleController.php`
- [ ] `AuthorController.php`
- [ ] `CategoryController.php`
- [ ] `EditionController.php`
- [ ] `EmailController.php`
- [ ] `FileController.php`
- [ ] `PageController.php`
- [ ] `PaymentController.php`
- [ ] `PublicController.php`
- [ ] `ReviewerController.php`
- [ ] `SettingController.php`
- [ ] `SiteManagementController.php`
- [ ] `SubscriberController.php`
- [ ] `UserController.php`
- [ ] Auth controllers (Login, Register, etc.)

**Key changes:**
- Update model references: `App\Article` → `App\Models\Article`
- Update `View::make()` → `view()`
- Update `Input::all()` → `request()->all()`
- Update `Session::flash()` → `session()->flash()`
- Update `Redirect::to()` → `redirect()->to()`
- Update `DB::` facade usage (add `use Illuminate\Support\Facades\DB;`)
- Update `Auth::` facade usage (add `use Illuminate\Support\Facades\Auth;`)

### 4. Routes (Priority: HIGH)
- [ ] Copy `routes/web.php` and update syntax
- [ ] Update route model binding if used
- [ ] Update middleware syntax if needed

### 5. Views (Priority: MEDIUM)
- [ ] Copy all Blade templates from `resources/views/`
- [ ] Update any deprecated Blade directives
- [ ] Check for `@csrf` instead of `{{ csrf_field() }}`
- [ ] Update form helpers if using Laravel Collective

### 6. Database Migrations (Priority: HIGH)
- [ ] Copy all migration files
- [ ] Update migration syntax if needed (Laravel 10 uses similar syntax)
- [ ] Check for deprecated methods

### 7. Seeders (Priority: MEDIUM)
- [ ] Copy all seeder files
- [ ] Update namespace: `Database\Seeds\` → `Database\Seeders\`
- [ ] Update `run()` method calls

### 8. Configuration Files (Priority: HIGH)
- [ ] Copy and update `config/` files
- [ ] Update `config/app.php` - remove deprecated configs
- [ ] Update `config/auth.php` if needed
- [ ] Update `config/database.php` if needed
- [ ] Copy custom configs: `auto-translate.php`, `breadcrumbs.php`, `image.php`, `installer.php`, `localization-js.php`, `paypal.php`, `permission.php`, `vue-i18n-generator.php`

### 9. Middleware (Priority: HIGH)
- [ ] Copy middleware files
- [ ] Update middleware registration in `app/Http/Kernel.php`
- [ ] Check for deprecated middleware methods

### 10. Public Assets (Priority: MEDIUM)
- [ ] Copy `public/css/`, `public/js/`, `public/images/`
- [ ] Copy `public/uploads/` structure
- [ ] Update asset references if needed

### 11. Language Files (Priority: MEDIUM)
- [ ] Copy `resources/lang/` files
- [ ] Verify translation syntax

### 12. Package Configuration
- [ ] Update `package.json` for frontend dependencies
- [ ] Update `webpack.mix.js` or migrate to Vite
- [ ] Install npm dependencies

### 13. Environment Setup
- [ ] Copy `.env.example` structure
- [ ] Update environment variables
- [ ] Run `php artisan key:generate`
- [ ] Run `composer install`
- [ ] Run `npm install`

### 14. Testing
- [ ] Test authentication
- [ ] Test article submission workflow
- [ ] Test payment processing
- [ ] Test email notifications
- [ ] Test file uploads
- [ ] Test all user roles

## Common Code Replacements

### Namespace Updates
```php
// Old
namespace App;
use App\Article;

// New
namespace App\Models;
use App\Models\Article;
```

### String Helpers
```php
// Old
str_slug($value, '-')

// New
use Illuminate\Support\Str;
Str::slug($value, '-')
```

### Filter Sanitize
```php
// Old
filter_var($value, FILTER_SANITIZE_STRING)

// New
htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
// Or remove if Laravel handles it
```

### View Helpers
```php
// Old
View::make('view.name', compact('data'))
return Redirect::to('/path')

// New
return view('view.name', compact('data'))
return redirect()->to('/path')
```

### Relationship Definitions
```php
// Old
return $this->belongsToMany('Article');

// New
return $this->belongsToMany(Article::class);
```

## Next Steps
1. Continue migrating models (start with Article, Edition, User)
2. Migrate controllers systematically
3. Update routes
4. Copy and update views
5. Test each component as you migrate

## Notes
- Laravel 10 uses similar structure to Laravel 5.7, so most code will work with minor updates
- Focus on namespace changes and deprecated function replacements
- Test thoroughly after each major component migration

