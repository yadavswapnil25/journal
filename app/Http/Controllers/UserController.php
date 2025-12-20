<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Validator;
use App\UploadMedia;
use Illuminate\Support\Facades\Hash;
use App\Models\SiteManagement;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArticleNotificationMailable;
use App\Helper;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use HasRoles;

    protected $redirectTo = '/';

    /**
     * @access private
     * @var array $email_settings
     */
    private $email_settings;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
        $this->email_settings = '';
        if (isset($_SERVER["SERVER_NAME"]) && $_SERVER["SERVER_NAME"] != '127.0.0.1') {
            $this->email_settings = SiteManagement::getMetaValue('email_settings');
            if (!empty($this->email_settings)) {
                config(['mail.username' => $this->email_settings[0]['email']]);
            }
        }
    }

    /**
     * @access public
     * @desc Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Get users by role type filter
        if (!empty($request['role'])) {
            $role_id = $request['role'];
            $user_role = User::getRoleByRoleID($role_id);
            $user_role = !empty($user_role) && is_object($user_role) ? $user_role : null;
            $users = !empty($user_role) ? User::getUserByRoleFilter($user_role->role_type) : User::getUsers();
        } else {
            $users = User::getUsers();
        }
        $role_list = Role::select('name', 'id')->get()->pluck('name', 'id');
        $categories = Category::getCategories()->all();
        //Get roles with role type Editor & Reviewer
        $roles = Role::whereIn('role_type', ['editor', 'reviewer'])->get();
        $categories_id = null; // Initialize if needed
        return view(
            'admin.users.index',
            compact('roles', 'categories', 'categories_id', 'role_list', 'users')
        );
    }

    /**
     * @access public
     * @desc Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     * @version 1.0
     */
    public function addUser()
    {
        $roles = Role::whereIn('role_type', ['editor', 'reviewer'])->get();
        return view('admin.users.create')->with('roles', $roles);
    }

    /**
     * @access public
     * @desc Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $server_verification = Helper::journal_is_demo_site();
        if (!empty($server_verification)) {
            Session::flash('error', $server_verification);
            return redirect()->back();
        }
        if (!empty($request)) {
            $roles_id = array();
            //Validate name, email and password fields
            $this->validate($request, [
                'name' => 'required|max:20',
                'sur_name' => 'required|max:20',
                'email' => 'required|email|unique:users',
                'roles' => 'required',
            ]);
            // Generate secure temporary password with uppercase, lowercase, numbers
            // Mix of random string and numbers for better security
            $temp_password = Str::upper(Str::random(4)) . Str::lower(Str::random(4)) . rand(1000, 9999);
            
            $user = new User();
            $user->name = htmlspecialchars($request['name'], ENT_QUOTES, 'UTF-8');
            $user->sur_name = htmlspecialchars($request['sur_name'], ENT_QUOTES, 'UTF-8');
            $user->email = filter_var($request['email'], FILTER_SANITIZE_EMAIL);
            $user->password = Hash::make($temp_password);
            $user->save();
            $roles = $request['roles'];
            
            // Check if reviewer role is being assigned
            $is_reviewer = false;
            $reviewer_role_id = null;
            
            //Checking if a role was selected
            if (isset($roles)) {
                foreach ($roles as $role) {
                    $role_r = Role::where('id', '=', $role)->firstOrFail();
                    $user->assignRole($role_r); //Assigning role to user
                    
                    // Check if this is a reviewer role
                    if ($role_r->role_type == 'reviewer') {
                        $is_reviewer = true;
                        $reviewer_role_id = $role_r->id;
                    }
                }
            }
            
            // Send email to reviewer with temporary password
            // Check email configuration - support both old and new Laravel config structure
            $mail_username = config('mail.mailers.smtp.username') ?: config('mail.username');
            $mail_password = config('mail.mailers.smtp.password') ?: config('mail.password');
            $mail_configured = !empty($mail_username) && !empty($mail_password);
            $email_settings_available = !empty($this->email_settings);
            
            // Try to send email if either mail config is set OR email settings from database are available
            // Also try to send if MAIL_MAILER is set to 'log' or 'array' for testing
            $mail_driver = config('mail.default');
            $can_send_email = $mail_configured || $email_settings_available || in_array($mail_driver, ['log', 'array']);
            
            if ($can_send_email) {
                $site = SiteManagement::getMetaValue('site_title');
                $superadmin = User::getUserByRoleType('superadmin');
                $role_type = User::getRoleByRoleID($request['roles'][0]);
                $role_type = !empty($role_type) && is_object($role_type) ? $role_type : null;
                $email_params = array();
                $email_params['new_user_supper_admin_name'] = !empty($superadmin) && !empty($superadmin[0]) ? $superadmin[0]->name : '';
                $email_params['site_title'] = !empty($site) && !empty($site[0]) ? $site[0]['site_title'] : '';
                $email_params['user_edit_page_link'] = url('/login?user_id=' . $user->id . '&email_type=new_user');
                $email_params['new_user_name'] = $request['name'] . " " . $request['sur_name'];
                $email_params['new_user_role'] = !empty($role_type) ? $role_type->name : '';
                $email_params['login_email'] = $request['email'];
                $email_params['new_user_password'] = $temp_password; // Use generated temp password
                $email_params['login_url'] = url('/login'); // Add login URL
                
                // If reviewer, send email with temp password
                if ($is_reviewer) {
                    $user_template_data = DB::table('email_templates')->where('email_type', 'new_user')->where('role_id', null)->first();
                    if (!empty($user_template_data)) {
                        try {
                            Mail::to($request['email'])->send(new ArticleNotificationMailable($email_params, $user_template_data, 'reviewer'));
                            Session::flash('message', trans('prs.user_created') . ' ' . trans('Email with temporary password sent to reviewer.'));
                        } catch (\Exception $e) {
                            Session::flash('warning', trans('prs.user_created') . ' ' . trans('Warning: Email could not be sent. Please check mail configuration.'));
                        }
                    } else {
                        Session::flash('warning', trans('prs.user_created') . ' ' . trans('Warning: Email template not found. Email not sent.'));
                    }
                } else {
                    // For other roles, use existing logic
                    $user_template_data = DB::table('email_templates')->where('email_type', 'new_user')->where('role_id', null)->first();
                    if (!empty($user_template_data) && !empty($role_type)) {
                        try {
                            Mail::to($request['email'])->send(new ArticleNotificationMailable($email_params, $user_template_data, $role_type->role_type));
                        } catch (\Exception $e) {
                            // Silent fail for non-reviewer roles
                        }
                    }
                }
            } else {
                if ($is_reviewer) {
                    Session::flash('warning', trans('prs.user_created') . ' ' . trans('Warning: Email configuration missing. Email with temporary password was not sent.'));
                } else {
                    Session::flash('message', trans('prs.user_created'));
                }
            }
            
            // Only set success message if not already set above
            if (!Session::has('message') && !Session::has('warning')) {
                Session::flash('message', trans('prs.user_created'));
            }
            return redirect()->to('superadmin/users/manage-users');
        }
    }

    /**
     * @access public
     * @return \Illuminate\Http\Response
     * @param int $id
     * @desc Show the form for editing the specified User.
     */
    public function edit($id)
    {
        if (!empty($id)) {
            $users = User::find($id);
            if (!empty($users)) {
                $role = User::getUserRoleType($id);
                $role = !empty($role) && is_object($role) ? $role : null;
                $categories = Category::getCategories()->all();
                $categories_id = Category::getCategoryByReviewerID($users->id);
                return view('admin.users.edit', compact('role', 'categories', 'categories_id', 'id'))
                    ->with('users', $users);
                Session::flash('message', trans('prs.user_delete'));
                return redirect()->to('superadmin/users/manage-users');
            }
        }
    }

    /**
     * @access public
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @desc Update the specified User.
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $server_verification = Helper::journal_is_demo_site();
        if (!empty($server_verification)) {
            Session::flash('error', $server_verification);
            return redirect()->back();
        }
        if (!empty($request)) {
            if (!empty($request['old_password']) || !empty($request['password']) || !empty($request['confirm_password'])) {
                Validator::extend('old_password', function ($attribute, $value, $parameters) {
                    return Hash::check($value, Auth::user()->password);
                });
                $this->validate($request, [
                    'password' => 'min:6|required', 'confirm_password' => 'same:password',
                    'old_password' => 'required|old_password'
                ]);
            }
            $this->validate(
                $request,
                [
                    'name' => 'required|max:20',
                    'sur_name' => 'required|max:20',
                    'email' => 'required',
                ]
            );
            $file = $request['user_image'];
            $users = User::find($id);
            $users->name = htmlspecialchars($request['name'], ENT_QUOTES, 'UTF-8');
            $users->sur_name = htmlspecialchars($request['sur_name'], ENT_QUOTES, 'UTF-8');
            $users->email = filter_var($request['email'], FILTER_SANITIZE_EMAIL);
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                if ($extension === "jpg" || $extension === "png") {
                    $file_original_name = $file->getClientOriginalName();
                    $file_name_without_extension = pathinfo($file_original_name, PATHINFO_FILENAME);
                    $filename = $file_name_without_extension . '-' . time() . '.' . $extension;

                    $path = public_path() . '/uploads/users/';
                    if (!file_exists($path . $id)) {
                        $directory = File::makeDirectory($path . $id, 0755, true);
                    }
                    // generate mini image
                    $small_img = Image::make($file);
                    $small_img->fit(
                        40,
                        40,
                        function ($constraint) {
                            $constraint->upsize();
                        }
                    );
                    $small_img->save($path . $id . '/mini-' . $filename);
                    // generate small image
                    $small_img = Image::make($file);
                    $small_img->fit(
                        50,
                        50,
                        function ($constraint) {
                            $constraint->upsize();
                        }
                    );
                    $small_img->save($path . $id . '/small-' . $filename);
                    // generate medium image
                    $medium_img = Image::make($file);
                    $medium_img->fit(
                        70,
                        70,
                        function ($constraint) {
                            $constraint->upsize();
                        }
                    );
                    $medium_img->save($path . $id . '/medium-' . $filename);
                    // generate large image
                    $img = Image::make($file);
                    $img->fit(
                        110,
                        110,
                        function ($constraint) {
                            $constraint->upsize();
                        }
                    );
                    $img->save($path . $id . '/' . $filename);

                    $users->user_image = htmlspecialchars($filename, ENT_QUOTES, 'UTF-8');
                } else {
                    Session::flash('error', trans('image must jpg or png type'));
                    return redirect()->back();
                }
            }
            if (Hash::check($request->old_password, $users->password)) {
                $users->password = Hash::make($request->password);
            }
            $users->save();
            $categories = $request['category'];
            if (!empty($categories)) {
                Category::saveReviewerCategory($categories, $users->id);
            }
            Session::flash('message', trans('prs.user_updated'));
            return redirect()->to('superadmin/users/manage-users');
        }
    }

    /**
     * @access public
     * Remove the specified resource from storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $json = array();
        $server = Helper::ajax_journal_is_demo_site();
        if (!empty($server)) {
            $json['type'] = 'error';
            $json['message'] = $server->getData()->message;
            return $json;
        }
        $id = $request['id'];
        if (!empty($id)) {
            $user = User::find($id);
            $user_role_type = User::getUserRoleType($id);
            $user_role_type = !empty($user_role_type) && is_object($user_role_type) ? $user_role_type : null;
            if (!empty($user_role_type) && $user_role_type->role_type == 'reviewer') {
                DB::table('reviewers')->where('reviewer_id', $id)->delete();
                DB::table('comments')->where('comment_author', $id)->delete();
                DB::table('reviewers_categories')->where('reviewer_id', $id)->delete();
            }
            if (!empty($user_role_type) && $user_role_type->role_type == 'author') {
                $articles = DB::table('articles')
                    ->select('id')
                    ->where('corresponding_author_id', $id)
                    ->get();
                if (!empty($articles)) {
                    foreach ($articles as $article) {
                        DB::table('author_article')->where('article_id', $article->id)->delete();
                    }
                }
                DB::table('articles')->where('corresponding_author_id', $id)->delete();
            }
            $user->roles()->detach();
            $user->delete();
            $json['type'] = 'success';
            $message = trans('prs.user_deleted');
            return $message;
        }
    }

    /**
     * @access public
     * @desc Redirect user to the thank you page.
     * @return \Illuminate\Http\Response
     */
    public function paymentRedirect()
    {
        $user_id = Auth::user()->id;
        return view('customers.thank-you', compact('user_id'));
    }

    /**
     * @access public
     * @desc Display a listing of the purchased products.
     * @return \Illuminate\Http\Response
     */
    public function downloadOrders()
    {
        $user_id = Auth::user()->id;
        $user_role_type = User::getUserRoleType($user_id);
        $user_role_type = !empty($user_role_type) && is_object($user_role_type) ? $user_role_type : null;
        $user_role = !empty($user_role_type) ? $user_role_type->role_type : '';
        if ($user_role != 'superadmin') {
            abort(404);
        }
        $downloads = User::getDownloadedArticles();
        $purchases = !empty($downloads) ? $downloads : '';
        return view('admin.orders.downloads', compact('purchases'));
    }

    /**
     * @access public
     * @desc Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function OrderInvoice($id)
    {
        $invoice_info = DB::table('invoices')
            ->join('items', 'items.invoice_id', '=', 'invoices.id')
            ->select('items.*', 'invoices.*')
            ->where('invoices.id', '=', $id)
            ->first();
        return view('admin.orders.invoice', compact('invoice_info'));
    }

    /**
     * @access public
     * @return \Illuminate\Http\Response
     * @param int $id
     * @desc Download Purchased Articles.
     */
    public function downloadArticles()
    {
        $user_id = Auth::user()->id;
        $user_role_type = User::getUserRoleType($user_id);
        $user_role_type = !empty($user_role_type) && is_object($user_role_type) ? $user_role_type : null;
        $user_role = !empty($user_role_type) ? $user_role_type->role_type : '';
        if ($user_role != 'reader') {
            abort(404);
        }
        $purchases = User::getUserPurchasedArticles($user_id);
        return view('customers.downloads', compact('purchases'));
    }

    /**
     * @access public
     * @desc Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function productInvoice($id)
    {
        if (!empty($id)) {
            $invoice_info = DB::table('invoices')
                ->join('items', 'items.invoice_id', '=', 'invoices.id')
                ->select('items.*', 'invoices.*')
                ->where('invoices.id', '=', $id)
                ->first();
            return view('customers.invoice', compact('invoice_info'));
        }
    }

    /**
     * @access public
     * @param int $id
     * @desc Displaying checkout page.
     * @return \Illuminate\Http\Response
     */
    public function checkout($id)
    {
        $serialize_array = DB::table('sitemanagements')->select('meta_value')->where('meta_key', 'payment_settings')->first();
        if (!empty($serialize_array)) {
            $payment_setting = !empty($serialize_array) ? unserialize($serialize_array->meta_value) : '';
            $currency = $payment_setting[0]['currency'];
            session()->put(['product_id' => $id]);
            return view('customers.checkout', compact('currency'));
        } else {
            return redirect()->back();
        }
    }

    /**
     * @access public
     * @param \Illuminate\Http\Request  $request
     * @desc Assign category to reviewer.
     * @return \Illuminate\Http\Response
     */
    public function assignCategory(Request $request)
    {
        $server = Helper::ajax_journal_is_demo_site();
        if (!empty($server)) {
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        $reviewer_id = $request['reviewer_id'];
        $cat_id = $request['category'];
        if (!empty($reviewer_id) && !empty($cat_id)) {
            Category::saveReviewerCategory($cat_id, $reviewer_id);
            $json['message'] = 'Category Assigned Successfully';
        } else {
            $json['message'] = 'please select category';
        }
        return $json;
    }
}

