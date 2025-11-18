<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmailTemplate extends Model
{
    /**
     * @access protected 
     * @var array $fillable
     */
    protected $fillable = ['subject', 'title', 'email_type', 'body'];

    /**
     * @access public
     * @param \Illuminate\Http\Request  $request
     * @desc Store template in database.
     * @return void
     */
    public function saveEmailTemplate($request)
    {
        if (!empty($request)) {
            $this->subject = htmlspecialchars($request->subject, ENT_QUOTES, 'UTF-8');
            $this->title = htmlspecialchars($request->title, ENT_QUOTES, 'UTF-8');
            $this->email_type = htmlspecialchars($request->template_types, ENT_QUOTES, 'UTF-8');
            $this->role_id = filter_var($request->user_types, FILTER_SANITIZE_NUMBER_INT);
            $this->body = $request->body;
            $this->save();
        }
    }

    /**
     * @access public
     * @param int $id
     * @param \Illuminate\Http\Request  $request
     * @desc Update template in database
     * @return void
     */
    public function updateTemplate($id, $request)
    {
        if (!empty($id) && !empty($request)) {
            $template = static::find($id);
            $template->subject = htmlspecialchars($request->subject, ENT_QUOTES, 'UTF-8');
            $template->title = htmlspecialchars($request->title, ENT_QUOTES, 'UTF-8');
            $template->body = $request->body;
            $template->save();
        }
    }

    /**
     * @access public
     * @desc Get email templates
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getEmailTemplates()
    {
        $email_templates = DB::table('email_templates')->paginate(10);
        return $email_templates;
    }

    /**
     * @access public
     * @param int $role_id
     * @param string $email_type
     * @desc Get specific email template
     * @return object|null
     */
    public static function getEmailTemplatesByID($role_id, $email_type)
    {
        if (!empty($role_id) && !empty($email_type)) {
            return DB::table('email_templates')->where('role_id', $role_id)->where('email_type', $email_type)->first();
        }
    }

    /**
     * @access public
     * @param int $role_id
     * @param string $email_type
     * @desc Get specific email veriables by role
     * @return object|null
     */
    public static function getEmailVariablesByRoleID($role_id, $email_type)
    {
        if (!empty($email_type)) {
            if ($role_id == null) {
                return DB::table('role_email_types')->select('variables')->where('role_id', null)
                    ->where('email_type', $email_type)->first();
            } else {
                return DB::table('role_email_types')->select('variables')
                    ->where('role_id', $role_id)->where('email_type', $email_type)
                    ->first();
            }
        }
    }

    /**
     * @access public
     * @param int $id
     * @desc Get specific email type by role
     * @return object|null
     */
    public static function getEmailTemplatesRoleByID($id)
    {
        if (!empty($id) && is_numeric($id)) {
            return DB::table('email_templates')->select('role_id')->where('id', $id)->first();
        }
    }

    /**
     * @access public
     * @param string $email_type
     * @desc Get specific email template role list
     * @return array
     */
    public static function getEmailTemplateRoleByType($email_type)
    {
        if (!empty($email_type)) {
            return DB::table('email_templates')->select('role_id')->where('email_type', $email_type)
                ->get()->pluck('role_id')->all();
        }
    }

    /**
     * @access public
     * @param string $email_type
     * @desc Get email template role count
     * @return int
     */
    public static function getEmailTypeRoleCount($email_type)
    {
        if ($email_type) {
            return DB::table('role_email_types')->select('role_id')->where('email_type', $email_type)->get()->count();
        }
    }

    /**
     * @access public
     * @param int $email_template_id
     * @desc Get specific email template role
     * @return object|null
     */
    public static function getEmailTemplateRole($email_template_id)
    {
        if(!empty($email_template_id) && is_numeric($email_template_id)){
            return DB::table('email_templates')->select('role_id')->where('id', $email_template_id)->first();
        }
    }

    /**
     * @access public
     * @desc Get change password template
     * @return \Illuminate\Support\Collection
     */
    public static function getChangePasswordEmailTemplate()
    {
        return DB::table('email_templates')->where('email_type', 'change_password')->get();
    }

    /**
     * @access public
     * @param int $role_id
     * @param string $type
     * @desc Get filter email template
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getFilterTemplate($role_id = "", $type = "")
    {
        $query = DB::table('email_templates')->select('*');
        if (!empty($role_id)) {
            $query->where('role_id', $role_id);
        }
        if (!empty($type)) {
            $query->where('email_type', $type);
        }
        return $query->paginate(10);
    }
}

