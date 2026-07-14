<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Models\SiteManagement;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArticleNotificationMailable;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;
use App\Helper;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';
    private $email_settings;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->email_settings = '';
        if (isset($_SERVER["SERVER_NAME"]) && $_SERVER["SERVER_NAME"] != '127.0.0.1') {
            $this->email_settings = SiteManagement::getMetaValue('email_settings');
            if (!empty($this->email_settings)) {
                config(['mail.username' => $this->email_settings[0]['email']]);
            }
        }
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        session(['registration_form_started_at' => time()]);

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $server_verification = Helper::journal_is_demo_site();
        if (!empty($server_verification)) {
            Session::flash('error', $server_verification);
            return redirect()->back();
        }

        // Honeypot: bots fill hidden fields; humans leave them empty.
        if (!empty($request->input('website'))) {
            Session::flash('error', trans('prs.register_spam_blocked'));
            return redirect()->back()->withInput($request->except(['password', 'password_confirmation', 'website']));
        }

        // Reject instant robotic submissions (form opened too briefly).
        $startedAt = (int) session('registration_form_started_at', 0);
        if ($startedAt === 0 || (time() - $startedAt) < 3) {
            Session::flash('error', trans('prs.register_too_fast'));
            session(['registration_form_started_at' => time()]);
            return redirect()->back()->withInput($request->except(['password', 'password_confirmation', 'website']));
        }

        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator, 'register');
        } else {
            event(new Registered($user = $this->create($request->all())));
            session()->forget('registration_form_started_at');
            return redirect($this->redirectPath())->with('message', trans('prs.register_success'));
        }
    }

    /**
     * Get a validator for an incoming registration request.
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'mobile_number' => 'required|string|max:20',
            'institutional_affiliation' => 'nullable|string|max:255',
            'terms_condition' => 'required',
        ]);

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $role = $data['role'];
        $id = !empty($role) && $role == 'author' ? '3' : '5';
        
        // First create the user
        $user = User::create([
            'name' => $data['name'],
            'sur_name' => $data['sur_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'mobile_number' => $data['mobile_number'],
            'institutional_affiliation' => $data['institutional_affiliation'] ?? null,
            'author_bio' => null,
            'token' => md5(uniqid(rand(), true)),
        ]);
        
        // Then assign the role to the created user
        $role_r = Role::where('id', '=', $id)->firstOrFail();
        $user->assignRole($role_r);
        
        // Check email configuration - support both old and new Laravel config structure
        $mail_username = config('mail.mailers.smtp.username') ?: config('mail.username');
        $mail_password = config('mail.mailers.smtp.password') ?: config('mail.password');
        $mail_configured = !empty($mail_username) && !empty($mail_password);
        $email_settings_available = !empty($this->email_settings);
        $mail_driver = config('mail.default');
        $can_send_email = $mail_configured || $email_settings_available || in_array($mail_driver, ['log', 'array']);
        
        if ($can_send_email) {
            $site = SiteManagement::getMetaValue('site_title');
            $superadmins = User::getUserByRoleType('superadmin');
            $email_params = [];
            $email_params['new_user_supper_admin_name'] = !empty($superadmins) ? $superadmins[0]->name : '';
            $email_params['site_title'] = $site[0]['site_title'];
            $email_params['user_edit_page_link'] = url('/login?user_id=' . $user->id . '&email_type=new_user');
            $email_params['new_user_name'] = $data['name'] . " " . $data['sur_name'];
            $email_params['new_user_role'] = $role;
            $email_params['login_email'] = $data['email'];
            $email_params['new_user_password'] = $data['password'];
            if (!empty($superadmins)) {
                foreach ($superadmins as $superadmin) {
                    $template_data = EmailTemplate::getEmailTemplatesByID($superadmin->role_id, 'new_user');
                    if (!empty($template_data)) {
                        try {
                            Mail::to($superadmin->email)->send(new ArticleNotificationMailable($email_params, $template_data, $role));
                        } catch (\Exception $e) {
                            // Log error but continue
                        }
                    }
                }
            }
            $user_template_data = DB::table('email_templates')->where('email_type', 'new_user')->where('role_id', null)->first();
            if (!empty($user_template_data)) {
                try {
                    Mail::to($data['email'])->send(new ArticleNotificationMailable($email_params, $user_template_data, $role));
                } catch (\Exception $e) {
                    // Log error but continue
                }
            }
        }

        return $user;
    }
}

