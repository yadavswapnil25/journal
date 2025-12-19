<?php

namespace App\Http\Controllers\Auth;

use App\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function authenticated(Request $request, $user)
    {
        if (Schema::hasTable('users')) {
            if (!empty($request->get('user_id')) && !empty($request->get('email_type'))) {
                $email_user_id = !empty($request->get('user_id')) ? $request->get('user_id') : "";
                $role = User::getUserRoleType($email_user_id);
                $role = !empty($role) && is_object($role) ? $role : null;
                $email_redirect_type = !empty($request->get('email_type')) ? $request->get('email_type') : "";
                $status_title = !empty($request->get('status')) ? Helper::displayArticleBreadcrumbsTitle($request->get('status')) : "";
                if ($email_redirect_type == "new_article") {
                    return Helper::getArticleEmailRedirectLink($email_user_id);
                } elseif ($email_redirect_type == "assign_reviewer") {
                    return redirect()->to('/reviewer/user/' . $email_user_id . '/articles-under-review');
                } elseif ($email_redirect_type == "reviewer_feedback" && !empty($request->get('status'))) {
                    if (!empty($role) && ($role->role_type == 'superadmin' || $role->role_type == 'editor')) {
                        $status = DB::table('articles')->select('status')->where('id', $request->get('id'))->first();
                        $menu_status = Helper::setArticleMenuParameter($status->status);
                        return Helper::getArticleEmailRedirectLink($email_user_id, $menu_status);
                    } else {
                        return Helper::getArticleEmailRedirectLink($email_user_id, $request->get('status'));
                    }
                } elseif (($email_redirect_type == "accepted_articles_editor_feedback"
                    || $email_redirect_type == "minor_revisions_editor_feedback"
                    || $email_redirect_type == "major_revisions_editor_feedback"
                    || $email_redirect_type == "rejected_editor_feedback")
                    && !empty($request->get('status'))) {
                        $menu_status = Helper::setArticleMenuParameter($request->get('status'));
                        return Helper::getArticleEmailRedirectLink($email_user_id, $menu_status);
                } elseif ($email_redirect_type == "new_user") {
                    return redirect()->to(url('/superadmin/users/edit-user/' . $email_user_id));
                } elseif ($email_redirect_type == "success_order" && !empty($request->get('invoice_id'))) {
                    if (!empty($role) && $role->role_type == 'superadmin') {
                        return redirect()->to(url('/superadmin/products/invoice/' . $request->get('invoice_id')));
                    } else {
                        return redirect()->to(url('/user/products/invoice/' . $request->get('invoice_id')));
                    }
                }
            }
            $user_id = Auth::user()->id;
            $user_role_type = User::getUserRoleType($user_id);
            $userRole = !empty($user_role_type) && is_object($user_role_type) ? $user_role_type->role_type : '';
            if ($user->hasRole('editor') || $user->hasRole('super admin')) {
                return redirect()->to('/' . $userRole . '/dashboard/' . $user_id . '/articles-under-review');
            } elseif ($user->hasRole('reviewer')) {
                return redirect()->to('/reviewer/user/' . $user_id . '/articles-under-review');
            } elseif ($user->hasRole('author')) {
                return redirect()->to('author/create-article');
            } else {
                return redirect()->to('/');
            }
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Schema::hasTable('users')) {
            $this->middleware('guest')->except('logout');
        }
    }
}

