<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'sur_name', 'email', 'password', 'user_image',
    ];

    /**
     * @access protected 
     * @var string $guard_name
     */
    protected $guard_name = 'web';

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @access public
     * @desc Get users from database
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|string
     */
    public static function getUsers()
    {
        $users = DB::table('users')->paginate(10);
        if (!empty($users)) {
            return $users;
        } else {
            return '';
        }
    }

    /**
     * @access public
     * @param int $user_id
     * @desc Get users by id
     * @return object|null
     */
    public static function getUserDataByID($user_id)
    {
        if (!empty($user_id) && is_numeric($user_id)) {
            return DB::table('users')->select('name', 'sur_name', 'email', 'user_image')->where('id', $user_id)->first();
        }
    }

    /**
     * @access public
     * @param string $role_type
     * @desc Get users by role id
     * @return array
     */
    public static function getUserByRoleType($role_type)
    {
        if (!empty($role_type)) {
            return DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('users.id', 'users.name', 'users.sur_name', 'users.email', 'roles.id as role_id')
                ->where('roles.role_type', '=', $role_type)
                ->get()->all();
        }
    }

    /**
     * @access public
     * @param int $user_id
     * @desc Get user Image from database
     * @return object|null
     */
    public static function getUploadedImage($user_id)
    {
        if (!empty($user_id) && is_numeric($user_id)) {
            $result = DB::table('users')->select('user_image')->where('id', $user_id)->first();
            return $result ? [$result] : null;
        }
    }

    /**
     * @access public
     * @param int $category_id
     * @desc Get Reviewers By Category
     * @return array
     */
    public static function getReviewersByCategory($category_id)
    {
        if (!empty($category_id) && is_numeric($category_id)) {
            return DB::table('users')
                ->join('reviewers_categories', 'users.id', '=', 'reviewers_categories.reviewer_id')
                ->join('categories', 'categories.id', '=', 'reviewers_categories.category_id')
                ->select('users.name', 'users.id')
                ->where('categories.id', '=', $category_id)
                ->orderBy('categories.id', 'desc')
                ->get()->all();
        }
    }

    /**
     * @access public
     * @param int $user_id
     * @desc Get user role type
     * @return object|string
     */
    public static function getUserRoleType($user_id)
    {
        if (!empty($user_id) && is_numeric($user_id)) {
            $role_id = DB::table('model_has_roles')->select('role_id')->where('model_id', $user_id)
                ->get()->pluck('role_id')->toArray();
            if (!empty($role_id)) {
                return DB::table('roles')->select('id', 'role_type', 'name')->where('id', $role_id[0])->first();
            } else {
                return '';
            }
        }
    }

    /**
     * @access public
     * @param int $user_id
     * @desc Get username from database
     * @return string
     */
    public static function getUserNameByID($user_id)
    {
        if (!empty($user_id) && is_numeric($user_id)) {
            $user = DB::table('users')->select('name')->where('id', $user_id)->first();
            if (!empty($user)) {
                return $user->name;
            } else {
                return '';
            }
        }
    }

    /**
     * @access public
     * @param int $user_id
     * @desc Get user purchased articles
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getUserPurchasedArticles($user_id)
    {
        if (!empty($user_id) && is_numeric($user_id)) {
            return DB::table('articles')
                ->join('downloads', 'downloads.product_id', '=', 'articles.id')
                ->join('items', 'items.product_id', '=', 'articles.id')
                ->join('invoices', 'invoices.id', '=', 'items.invoice_id')
                ->select(
                    'articles.id as article_id',
                    'articles.title as article_title',
                    'articles.publish_document as article_publish_document',
                    'invoices.*'
                )
                ->where('downloads.user_id', '=', $user_id)
                ->groupBy('invoices.id')
                ->orderBy('invoices.id', 'DESC')
                ->paginate(10);
        }
    }

    /**
     * @access public
     * @desc Get downloaded articles by reader
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getDownloadedArticles()
    {
        return DB::table('articles')
            ->join('downloads', 'downloads.product_id', '=', 'articles.id')
            ->join('items', 'items.product_id', '=', 'articles.id')
            ->join('invoices', 'invoices.id', '=', 'items.invoice_id')
            ->select(
                'articles.id as article_id',
                'articles.title as article_title',
                'articles.publish_document as article_publish_document',
                'invoices.*'
            )
            ->groupBy('invoices.id')
            ->orderBy('invoices.id', 'DESC')
            ->paginate(10);
    }

    /**
     * @access public
     * @param string $email_type
     * @desc Get roles list related to email type
     * @return array
     */
    public static function getRoleList($email_type)
    {
        if (!empty($email_type)) {
            return DB::table('roles')
                ->join('role_email_types', 'role_email_types.role_id', '=', 'roles.id')
                ->select('roles.id as role_id', 'roles.role_type as role_type', 'roles.name as role_name')
                ->where('role_email_types.email_type', '=', $email_type)
                ->get()->all();
        }
    }

    /**
     * @access public
     * @param string $email_type
     * @param array $role_id
     * @desc Get roles list by role id
     * @return array
     */
    public static function getEmailTypeRoles($email_type, $role_id = [])
    {
        if (!empty($email_type) && !empty($role_id)) {
            return DB::table('roles')
                ->join('role_email_types', 'role_email_types.role_id', '=', 'roles.id')
                ->select('roles.id as role_id', 'roles.role_type as role_type', 'roles.name as role_name')
                ->where('role_email_types.email_type', '=', $email_type)
                ->whereNotIn('role_email_types.role_id', $role_id)
                ->get()->all();
        }
    }

    /**
     * @access public
     * @param int $user_id
     * @desc Get role id related to user
     * @return int|string
     */
    public static function getRoleIDByUserID($user_id)
    {
        if (!empty($user_id) && is_numeric($user_id)) {
            $role = DB::table('model_has_roles')->select('role_id')->where('model_id', $user_id)->first();
            if (!empty($role)) {
                return $role->role_id;
            } else {
                return '';
            }
        }
    }

    /**
     * @access public
     * @param int $role_id
     * @desc Get user role by id
     * @return object|null
     */
    public static function getRoleByRoleID($role_id)
    {
        if (!empty($role_id) && is_numeric($role_id)) {
            return DB::table('roles')->select('role_type', 'name')->where('id', $role_id)->first();
        }
    }

    /**
     * @access public
     * @param string $role_type
     * @desc Get users by filter role type
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getUserByRoleFilter($role_type)
    {
        if (!empty($role_type)) {
            return DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('users.*', 'users.email', 'roles.id as role_id')
                ->where('roles.role_type', '=', $role_type)
                ->paginate(10);
        }
    }

    /**
     * @access public
     * @param int $article_id
     * @desc Get article author image
     * @return string
     */
    public static function getArticleAuthorImage($article_id)
    {
        if (!empty($article_id)) {
            $article =  DB::table('articles')
                ->join('users', 'users.id', '=', 'articles.corresponding_author_id')
                ->select('users.user_image')
                ->where('articles.id', '=', $article_id)
                ->first();
            if (!empty($article)) {
                return $article->user_image;
            } else {
                return '';
            }
        }
    }
}
