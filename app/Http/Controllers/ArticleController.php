<?php

/**
 * Class ArticleController
 *
 * @category Scientific-Journal
 *
 * @package Scientific-Journal
 * @author  Amentotech <theamentotech@gmail.com>
 * @license http://www.amentotech.com Amentotech
 * @link    http://www.amentotech.com
 */

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Edition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\View;
use App\Mail\ArticleNotificationMailable;
use Illuminate\Support\Facades\Auth;
use App\Helper;
use App\Models\SiteManagement;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Class ArticleController
 *
 */
class ArticleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * @access public
     * @param string $role
     * @param int $id
     * @param string $status
     * @desc Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $role = "", $id = "", $status = "")
    {
        $article_status = Helper::getMenuStatus($status);
        $user_id = Auth::user()->id;
        $user_role_type = User::getUserRoleType($user_id);
        $user_role_type = !empty($user_role_type) && is_object($user_role_type) ? $user_role_type : null;
        $user_role = !empty($user_role_type) ? $user_role_type->role_type : '';
        $assigned_role_types = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $user_id)
            ->pluck('roles.role_type')
            ->toArray();
        $role_allowed = false;
        if ($role === 'editor') {
            $role_allowed = in_array('editor', $assigned_role_types, true) || in_array('superadmin', $assigned_role_types, true);
        } elseif ($role === 'superadmin') {
            $role_allowed = in_array('superadmin', $assigned_role_types, true);
        } else {
            $role_allowed = in_array($role, $assigned_role_types, true);
        }
        $page_title = Helper::DashboardArticlePageTitle($status);
        $payment_mode = SiteManagement::getMetaValue('payment_mode');
        if (empty($article_status)) {
            $redirect_role = $role_allowed && !empty($role) ? $role : $user_role;
            return redirect()->to('/' . $redirect_role . '/dashboard/' . $user_id . '/articles-under-review');
        } else {
            if (!$role_allowed || $user_id != $id || !(is_numeric($id)) || !(in_array($article_status, Helper::statusStaticList()))) {
                return view('errors.401');
            }
            // Keep links/status actions aligned with selected dashboard role.
            $user_role = $role;
            $editions = Edition::getEditionsListByStatus();
            if (!empty($request->get('keyword'))) {
                $keyword = $request->get('keyword');
                $articles = Article::where('status', $article_status)->where('title', 'like', '%' . $keyword . '%')
                    ->orderBy('updated_at', 'desc')->paginate(10);
            } else {
                $articles = Article::where('status', $article_status)->orderBy('updated_at', 'desc')->paginate(9);
            }
            return view(
                'admin.article.index',
                compact(
                    'page_title', 'editions', 'user_id',
                    'articles', 'article_status', 'user_role', 'payment_mode'
                )
            );
        }
    }

    /**
     * @access public
     * @desc Display the specified resource.
     * @param string $role
     * @param int $id
     * @param string $status
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function show($role = "", $id = "", $status, $slug)
    {
        $article_status = Helper::getMenuStatus($status);
        $user_id = Auth::user()->id;
        $user_role_type = User::getUserRoleType($user_id);
        $user_role_type = !empty($user_role_type) && is_object($user_role_type) ? $user_role_type : null;
        $user_role = !empty($user_role_type) ? $user_role_type->role_type : '';
        $assigned_role_types = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $user_id)
            ->pluck('roles.role_type')
            ->toArray();
        $role_allowed = false;
        if ($role === 'editor') {
            $role_allowed = in_array('editor', $assigned_role_types, true) || in_array('superadmin', $assigned_role_types, true);
        } elseif ($role === 'superadmin') {
            $role_allowed = in_array('superadmin', $assigned_role_types, true);
        } else {
            $role_allowed = in_array($role, $assigned_role_types, true);
        }
        if (!$role_allowed || $user_id != $id || !(is_numeric($id)) || !(in_array($article_status, Helper::statusStaticList()))) {
            return view('errors.401');
        }
        $user_role = $role;
        $article = DB::table('articles')->where('slug', $slug)->where('status', $article_status)->first();
        if (!empty($article)) {
            $existed_reviewers = User::getUserByRoleType('reviewer');
            $existed_categories = Category::all();
            $reviewers_categories = Category::getReviewersCategory();
            $article_reviewers = Article::getReviewerIdByArticle($article->id);
            return view(
                'admin.article.show',
                compact(
                    'slug', 'article_reviewers', 'user_role',
                    'user_id', 'article', 'existed_reviewers',
                    'existed_categories', 'reviewers_categories'
                )
            )->with('status', $article_status);
        }
    }

    /**
     * @access public
     * @desc Assign article to the reviewer and
     * sent email to the reviewer.
     * @param  \Illuminate\Http\Request  $request
     * @param string $role
     * @return \Illuminate\Http\Response
     */
    public function assignReviewer(Request $request, $role = "")
    {
        $server = Helper::ajax_journal_is_demo_site();
        if (!empty($server)) {
            $response['message'] = $server->getData()->message;
            return $response;
        }

        $this->validate($request, [
            'reviewer_email' => 'required|email',
            'reviewer_article' => 'required|exists:articles,id',
        ]);

        $reviewer_email = trim($request->input('reviewer_email'));
        $article_id = (int) $request->input('reviewer_article');
        $editor_file_path = null;

        // Handle file upload if provided
        if ($request->hasFile('editor_file')) {
            $request->validate(['editor_file' => 'mimes:pdf,doc,docx|max:10000']);
            $uploaded_file = $request->file('editor_file');
            $file_original_name = $uploaded_file->getClientOriginalName();
            $file_name_without_extension = pathinfo($file_original_name, PATHINFO_FILENAME);
            $file_path = 'uploads/articles_editor/' . $article_id . '/';
            $extension = $uploaded_file->getClientOriginalExtension();
            $file_name = $article_id . '-editor-' . $file_name_without_extension . '-' . time() . '.' . $extension;
            Storage::disk('local')->putFileAs($file_path, $uploaded_file, $file_name);
            $editor_file_path = htmlspecialchars($file_name, ENT_QUOTES, 'UTF-8');
        }

        $submitted_article = Article::getArticleNotificationData($article_id);
        $article_title = !empty($submitted_article) ? $submitted_article->title : '';

        $mail_username = config('mail.mailers.smtp.username') ?: config('mail.username');
        $mail_password = config('mail.mailers.smtp.password') ?: config('mail.password');
        $mail_configured = !empty($mail_username) && !empty($mail_password);
        $email_settings_available = !empty(SiteManagement::getMetaValue('email_settings'));
        $mail_driver = config('mail.default');
        $can_send_email = $mail_configured || $email_settings_available || in_array($mail_driver, ['log', 'array']);

        // Match email case-insensitively so assignment is found and appears in reviewer dashboard
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($reviewer_email)])->first();
        $user_role = $user ? User::getUserRoleType($user->id) : null;
        $is_reviewer = $user && (
            ($user_role && isset($user_role->role_type) && $user_role->role_type === 'reviewer')
            || (method_exists($user, 'hasRole') && $user->hasRole('reviewer'))
        );

        if ($can_send_email) {
            $email_params = [
                'reviewer_assign_article_title' => $article_title,
                'assign_article_reviewer_name'  => $user ? trim($user->name . ' ' . $user->sur_name) : $reviewer_email,
                'reviewer_email'                => $reviewer_email,
                'assign_article_id'             => $article_id,
                'article_link'                 => $is_reviewer ? url('/login?user_id=' . $user->id . '&email_type=assign_reviewer') : url('/login?email_type=assign_reviewer'),
            ];

            $role_id = $is_reviewer ? User::getRoleIDByUserID($user->id) : null;
            if ($role_id) {
                $template_data = EmailTemplate::getEmailTemplatesByID($role_id, 'assign_reviewer');
            } else {
                $reviewer_role = DB::table('roles')->where('role_type', 'reviewer')->first();
                $template_data = $reviewer_role ? EmailTemplate::getEmailTemplatesByID($reviewer_role->id, 'assign_reviewer') : null;
            }
            if (!empty($template_data)) {
                try {
                    Mail::to($reviewer_email)->send(new ArticleNotificationMailable($email_params, $template_data, 'reviewer'));
                } catch (\Exception $e) {
                    \Log::warning('Assign reviewer email failed: ' . $e->getMessage(), ['article_id' => $article_id, 'reviewer_email' => $reviewer_email, 'exception' => $e]);
                    return response()->json(['message' => trans('prs.article_assigned') . ' ' . __('Email could not be sent.')]);
                }
            }
        }

        // Always link article to the user when email matches, so it appears under Articles Under Review
        if ($user) {
            try {
                DB::table('reviewers')->where('article_id', $article_id)->delete();
                Article::SaveArticleReviewers('articles_under_review', $article_id, [$user->id], $editor_file_path);
            } catch (\Exception $e) {
                return response()->json(['message' => trans('prs.article_assigned') . ' ' . __('Assignment could not be saved. Please try again.')], 500);
            }
        }

        $message = trans('prs.article_assigned');
        return response()->json(['message' => $message]);
    }

    /**
     * @access public
     * @desc Notify reviewer comments
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function notifyArticleReview(Request $request)
    {
        $request_id = $request['ID'];
        if (!empty($request_id)) {
            $parts = explode("-", $request_id);
            $article_id = $parts[1];
            return DB::table('articles')
                ->where('id', $article_id)
                ->update(['notify' => 0]);
        }
    }

    /**
     * @access public
     * @desc Notify Article Review.
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function submitEditorFeedback(Request $request, $id)
    {
        $server_verification = Helper::journal_is_demo_site();
        if (!empty($server_verification)) {
            Session::flash('error', $server_verification);
            return redirect()->back();
        }
        if (!empty($request)) {
            $this->validate(
                $request,
                [
                    'comments' => 'required',
                ]
            );
            $email_params = array();
            $status = $request['status'];
            $status_title = Helper::setArticleMenuParameter($status);
            $user_id = Auth::user()->id;
            $user_role_type = User::getUserRoleType($user_id);
            $user_role_type = !empty($user_role_type) && is_object($user_role_type) ? $user_role_type : null;
            $userRole = !empty($user_role_type) ? $user_role_type->role_type : '';
            $editor = User::getUserDataByID($user_id);
            Article::submitComments($request, $user_id, $id);
            $comment_id = DB::getPdo()->lastInsertId();
            $comments = Article::getCommentsByID($comment_id);
            Article::where('id', '=', $id)->update(['status' => $status, 'author_notify' => 1]);
            // prepare email and send to users
            $corresponding_author = Article::getArticleCorrespondingAuthor($id);
            if (!empty($corresponding_author)) {
                $corresponding_author_email = $corresponding_author[0]->email;
                $corresponding_author_name = $corresponding_author[0]->name . " " . $corresponding_author[0]->sur_name;
                $corresponding_author_data = User::getUserRoleType($corresponding_author[0]->id);
                $corresponding_author_data = !empty($corresponding_author_data) && is_object($corresponding_author_data) ? $corresponding_author_data : null;
                $author_template_data = !empty($corresponding_author_data) ? EmailTemplate::getEmailTemplatesByID($corresponding_author_data->id, $status . '_editor_feedback') : null;
                $author_article_link = url(
                    '/login?user_id=' . $corresponding_author[0]->id . '&email_type=' . $status . '_editor_feedback&status=' . $status
                );
                // For revision loop statuses, fallback to resubmit template if specific template is missing.
                if (in_array($status, ['minor_revisions', 'major_revisions'], true) && empty($author_template_data) && !empty($corresponding_author_data)) {
                    $author_template_data = EmailTemplate::getEmailTemplatesByID($corresponding_author_data->id, 'resubmit_article');
                    $author_article_link = url('/login?user_id=' . $corresponding_author[0]->id . '&email_type=resubmit_article&status=articles-under-review');
                }
                $email_params['editor_review_corresponding_author_name'] = $corresponding_author_name;
                $email_params['author_editor_review_article_link'] = $author_article_link;
            }
            $superadmins = User::getUserByRoleType('superadmin');
            $email_params['editor_review_super_admin_name'] = !empty($superadmins) ? $superadmins[0]->name : '';
            $articles = Article::select('title')->where('id', $id)->first();
            if (!empty($articles)) {
                $email_params['editor_review_author_article_title'] = $articles->title;
            }
            $email_params['editor_review_author_article_id'] = $id;
            $email_params['editor_review_comments'] = $comments->comment;
            $email_params['editor_name'] = $editor->name . " " . $editor->sur_name;
            // Check email configuration - support both old and new Laravel config structure
            $mail_username = config('mail.mailers.smtp.username') ?: config('mail.username');
            $mail_password = config('mail.mailers.smtp.password') ?: config('mail.password');
            $mail_configured = !empty($mail_username) && !empty($mail_password);
            $email_settings_available = !empty(SiteManagement::getMetaValue('email_settings'));
            $mail_driver = config('mail.default');
            $can_send_email = $mail_configured || $email_settings_available || in_array($mail_driver, ['log', 'array']);
            
            if ($can_send_email) {
                $role_type = array("superadmin", "corresponding_author", "author");
                $user_email = "";
                foreach ($role_type as $key => $role) {
                    if ($role == "superadmin") {
                        if (!empty($superadmins)) {
                            foreach ($superadmins as $superadmin) {
                                $article_link = url('/login?user_id=' . $superadmin->id . '&email_type=' . $status . '_editor_feedback&status=' . $status);
                                $email_params['editor_review_article_link'] = $article_link;
                                $template_data = EmailTemplate::getEmailTemplatesByID($superadmin->role_id, $status . '_editor_feedback');
                                if (!empty($template_data)) {
                                    try {
                                        Mail::to($superadmin->email)->send(new ArticleNotificationMailable($email_params, $template_data, $role));
                                    } catch (\Exception $e) {
                                        // Log error but continue with other emails
                                    }
                                }
                            }
                        }
                    } elseif ($role == "author") {
                        $authors = Article::getArticleAuthors($id);
                        foreach ($authors as $author) {
                            if (!empty($author_template_data)) {
                                $email_params['editor_review_author_name'] = $author->name;
                                try {
                                    Mail::to($author->email)->send(new ArticleNotificationMailable($email_params, $author_template_data, $role));
                                } catch (\Exception $e) {
                                    // Log error but continue with other emails
                                }
                            }
                        }
                    } elseif ($role == "corresponding_author") {
                        if (!empty($author_template_data)) {
                            try {
                                Mail::to($corresponding_author_email)->send(new ArticleNotificationMailable($email_params, $author_template_data, $role));
                            } catch (\Exception $e) {
                                // Log error but continue
                            }
                        }
                    }
                }
            }
            Session::flash('message', trans('prs.feedback_submitted'));
            return redirect()->to('/' . $userRole . '/dashboard/' . $user_id . '/' . $status_title);
        }
    }

    /**
     * @access public
     * @desc Custom errors for articles.
     * @param \Illuminate\Http\Request  $request
     * @return string
     */
    public function articleCustomErrors(Request $request)
    {
        $errors = array();
        $errors['author_name_error'] = trans('prs.ph_article_author_name_error');
        $errors['author_email_error'] = trans('prs.ph_article_author_email_error');
        $errors['article_title_error'] = trans('prs.ph_article_title_error');
        $errors['article_desc_error'] = trans('prs.ph_article_desc_error');
        $errors['article_doc_error'] = trans('prs.ph_article_doc_error');
        return $errors;
    }

    /**
     * @access public
     * @desc Update accepted articles.
     * @param \Illuminate\Http\Request  $request
     * @param string $role
     * @return \Illuminate\Http\Response
     */
    public function updateAcceptedArticle(Request $request, $role = "")
    {
        $server_verification = Helper::journal_is_demo_site();
        if (!empty($server_verification)) {
            Session::flash('error', $server_verification);
            return redirect()->back();
        }
        if (!empty($request)) {
            $hidden_pdf = $request['hidden_pdf_field'];
            $pdf = $request->file('article_pdf');
            $price = $request['price'];
            $article_id = $request['article'];
            if (empty($hidden_pdf)) {
                $this->validate(
                    $request,
                    [
                        'article_pdf' => 'required|mimes:pdf|max:2000',
                    ]
                );
            }
            if (!empty($pdf)) {
                $this->validate(
                    $request,
                    [
                        'article_pdf' => 'required|mimes:pdf|max:2000',
                    ]
                );
                $uploaded_file = $request->file('article_pdf');
                $file_original_name = $uploaded_file->getClientOriginalName();
                $file_name_without_extension = pathinfo($file_original_name, PATHINFO_FILENAME);
                $file_path = 'uploads/articles_pdf/' . $article_id . '/';
                $extension = $uploaded_file->getClientOriginalExtension();
                $file_name = $article_id . '-' . $file_name_without_extension . '-' . time() . '.' . $extension;
                Storage::disk('local')->putFileAs(
                    $file_path,
                    $uploaded_file,
                    $file_name
                );
                $article = Article::find($article_id);
                if (!empty($price)) {
                    $article->price = filter_var($price, FILTER_SANITIZE_NUMBER_INT);
                } else {
                    $article->price = null;
                }
                $article->publish_document = htmlspecialchars($file_name, ENT_QUOTES, 'UTF-8');
                $article->save();
                Session::flash('message', trans('prs.article_updated'));
                return redirect()->back();
            } elseif (!empty($hidden_pdf)) {
                $article = Article::find($article_id);
                if (!empty($price)) {
                    $article->price = filter_var($price, FILTER_SANITIZE_NUMBER_INT);
                } else {
                    $article->price = null;
                }
                $article->save();
                Session::flash('message', trans('prs.article_updated'));
                return redirect()->back();
            } else {
                Session::flash('message', trans('prs.article_updated'));
                return redirect()->back();
            }
        }
    }

    /**
     * @access public
     * @desc Download reviewer feedback as PDF
     * @param string $role
     * @param int $article_id
     * @param int $comment_id
     * @return \Illuminate\Http\Response
     */
    public function downloadReviewerFeedbackPDF($role, $article_id, $comment_id)
    {
        $article = Article::find($article_id);
        if (empty($article)) {
            abort(404, 'Article not found');
        }

        // Get the specific comment
        $comment = DB::table('comments')
            ->join('users', 'users.id', '=', 'comments.comment_author')
            ->select('comments.*', 'users.name', 'users.sur_name', 'users.email')
            ->where('comments.id', '=', $comment_id)
            ->where('comments.article_id', '=', $article_id)
            ->first();

        if (empty($comment)) {
            abort(404, 'Reviewer feedback not found');
        }

        // Parse the comment into structured format
        $questions = [];
        $lines = explode("\n", $comment->comment);
        $currentQuestion = '';
        $currentAnswer = '';

        foreach ($lines as $line) {
            $line = trim($line);
            
            if (preg_match('/^(\d+)\.\s(.+)$/', $line, $matches)) {
                if (!empty($currentQuestion)) {
                    $questions[] = [
                        'question' => $currentQuestion,
                        'answer' => $currentAnswer
                    ];
                }
                
                if (preg_match('/^(\d+)\.\s(.+?)[:\?]\s*(.+)$/', $line, $sameLineMatches)) {
                    $currentQuestion = trim($sameLineMatches[2]);
                    $currentAnswer = trim($sameLineMatches[3]);
                } else {
                    $currentQuestion = trim($matches[2]);
                    $currentAnswer = '';
                }
            } else if (!empty($line)) {
                if (!empty($currentAnswer)) {
                    $currentAnswer .= "\n" . $line;
                } else {
                    $currentAnswer = $line;
                }
            }
        }

        if (!empty($currentQuestion)) {
            $questions[] = [
                'question' => $currentQuestion,
                'answer' => $currentAnswer
            ];
        }

        $data = [
            'article' => $article,
            'comment' => $comment,
            'questions' => $questions,
        ];

        $pdf = Pdf::loadView('admin.article.reviewer-feedback-pdf', $data);
        $filename = 'Reviewer_Feedback_' . $comment->name . '_' . $article->unique_code . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}

