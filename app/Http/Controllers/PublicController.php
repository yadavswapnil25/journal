<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Edition;
use App\Models\SiteManagement;
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Helper;

class PublicController extends Controller
{
    /**
     * @access public
     * @desc Get published article file from storage.
     * @param string $publish_file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getPublishFile($publish_file)
    {
        if (!empty($publish_file)) {
            // Decode HTML entities in filename (e.g., &amp; to &) since file is stored with original name
            $decoded_publish_file = html_entity_decode($publish_file, ENT_QUOTES, 'UTF-8');
            $file_parts = explode('-', $decoded_publish_file);
            $article_id = $file_parts[0];
            $file_path = 'uploads/articles_pdf/' . $article_id . '/' . $decoded_publish_file;
            
            // Check if file exists
            if (Storage::exists($file_path)) {
                return Storage::download($file_path);
            } else {
                // If decoded filename doesn't exist, try with original filename (for backward compatibility)
                $file_parts_original = explode('-', $publish_file);
                $article_id_original = $file_parts_original[0];
                $file_path_original = 'uploads/articles_pdf/' . $article_id_original . '/' . $publish_file;
                if (Storage::exists($file_path_original)) {
                    return Storage::download($file_path_original);
                }
                abort(404, 'File not found');
            }
        }
    }

    /**
     * @access public
     * @desc Get published article from database.
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function showPublishArticle($slug)
    {
        $published_articles = Edition::getPublishedEditionArticles($slug);
        $first_article = collect($published_articles)->first();
        if (empty($first_article)) {
            abort(404);
        }
        $title = $first_article->edition_title;
        return view('editions.index', compact('published_articles', 'slug', 'title'));
    }

    /**
     * @access public
     * @desc Display the specified resource.
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function show($slug)
    {
        $article = DB::table('articles')->where('slug', $slug)->first();
        if (empty($article)) {
            abort(404);
        }
        $payment_detail = SiteManagement::getMetaValue('payment_settings');
        $currency_symbol = !empty($payment_detail) && !empty($payment_detail[0]['currency']) ? $payment_detail[0]['currency'] : '';
        $article_edition = Edition::getEditionByArticleID($article->id);
        if (empty($article_edition)) {
            abort(404);
        }
        $edition_slug = $article_edition->slug;
        $edition_title = $article_edition->title;
        $meta_desc = !empty($article) ? $article->excerpt : '';
        return view(
            'editions.show',
            compact(
                'article', 'payment_detail', 'currency_symbol',
                'edition_slug', 'edition_title', 'meta_desc'
            )
        );
    }

    /**
     * @access public
     * @desc Display the search result
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function filterEdition(Request $request)
    {
        $categories = Category::getCategories()->all();
        $editions = Edition::getPublishedEdition();
        $keyword = $request->get('s', '');
        $requested_category = $request->get('category', []);
        $requested_editions = $request->get('edition', []);
        
        if (!empty($request['s']) || !empty($request['category']) || !empty($request['edition']) || !empty($request['sort']) || !empty($request['show'])) {
            $sort_by = $request->get('sort', '');
            if (!empty($sort_by)) {
                if ($sort_by == 'date') {
                    $sort_by = "created_at";
                } else {
                    $sort_by = $request['sort'];
                }
            } else {
                $sort_by = "created_at";
            }
            $total_records = $request->get('show', 10); // Default to 10 if not provided
            $published_articles = Article::getFilterArticles($keyword, $requested_category, $requested_editions, $sort_by, $total_records);
            if (!empty($published_articles)) {
                return view('editions.all_published_articles', compact('published_articles', 'categories', 'editions', 'requested_category', 'requested_editions', 'keyword'))->withInput($request->all());
            } else {
                $published_articles = [];
                Session::flash('message', trans('prs.record_not_found'));
                return view('editions.all_published_articles', compact('published_articles', 'categories', 'editions', 'requested_category', 'requested_editions', 'keyword'))->withInput($request->all());
            }
        } else {
            $published_articles = Article::getPublishedArticle();
            return view('editions.all_published_articles', compact('published_articles', 'categories', 'editions', 'keyword'))->withInput($request->all());
        }
    }

    /**
     * @access public
     * @desc Display the specified resource.
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function showDetailPage($slug)
    {
        // Static Terms & Conditions page (does not require a database record)
        if (strtolower($slug) === 'terms-and-conditions') {
            return view('pages.terms-and-conditions');
        }

        // Static Privacy Policy page (does not require a database record)
        if (strtolower($slug) === 'privacy-policy') {
            return view('pages.privacy-policy');
        }

        $page = Page::getPageData($slug);
        if (empty($page)) {
            // If page doesn't exist, create a default page object for specific public pages
            $normalizedSlug = strtolower($slug);

            if ($normalizedSlug === 'about' || $normalizedSlug === 'about-the-journal') {
                $page = (object)[
                    'id'        => 0,
                    'title'     => 'About',
                    'slug'      => 'about',
                    'sub_title' => 'About the Journal',
                    'body'      => ''
                ];
            } elseif (in_array($normalizedSlug, ['aims-scope', 'aims-&-scope', 'aims-and-scope'], true)) {
                $page = (object)[
                    'id'        => 0,
                    'title'     => 'Aims & Scope',
                    'slug'      => 'aims-scope',
                    'sub_title' => 'Aims & Scope',
                    'body'      => ''
                ];
            } elseif (in_array($normalizedSlug, ['publication-information', 'publication-info'], true)) {
                $page = (object)[
                    'id'        => 0,
                    'title'     => 'Publication Information',
                    'slug'      => 'publication-information',
                    'sub_title' => 'Publication Information',
                    'body'      => ''
                ];
            } elseif (in_array($normalizedSlug, ['submission-guidelines', 'submissions-guidelines', 'submission-guideline'], true)) {
                $page = (object)[
                    'id'        => 0,
                    'title'     => 'Submission Guidelines',
                    'slug'      => 'submission-guidelines',
                    'sub_title' => 'Submission Guidelines',
                    'body'      => ''
                ];
            } elseif (in_array($normalizedSlug, ['call-for-submissions', 'call-for-submission', 'call-for-papers'], true)) {
                $page = (object)[
                    'id'        => 0,
                    'title'     => 'Call for Submissions',
                    'slug'      => 'call-for-submissions',
                    'sub_title' => 'Call for Submissions',
                    'body'      => ''
                ];
            } elseif (in_array($normalizedSlug, ['journal-policies', 'policies', 'policy'], true)) {
                $page = (object)[
                    'id'        => 0,
                    'title'     => 'Journal Policies',
                    'slug'      => 'journal-policies',
                    'sub_title' => 'Journal Policies',
                    'body'      => ''
                ];
            } elseif (in_array($normalizedSlug, ['editor-in-chief', 'editor-in-chief-page'], true)) {
                $page = (object)[
                    'id'        => 0,
                    'title'     => 'Editor-in-Chief',
                    'slug'      => 'editor-in-chief',
                    'sub_title' => 'Editor-in-Chief',
                    'body'      => ''
                ];
            } elseif (in_array($normalizedSlug, ['editorial-board', 'editorial-board-page'], true)) {
                $page = (object)[
                    'id'        => 0,
                    'title'     => 'Editorial Board',
                    'slug'      => 'editorial-board',
                    'sub_title' => 'Editorial Board',
                    'body'      => ''
                ];
            } elseif (in_array($normalizedSlug, ['advisory-board', 'advisory-board-page'], true)) {
                $page = (object)[
                    'id'        => 0,
                    'title'     => 'Advisory Board',
                    'slug'      => 'advisory-board',
                    'sub_title' => 'Advisory Board',
                    'body'      => ''
                ];
            } else {
                abort(404);
            }
        }

        $meta = DB::table('sitemanagements')
            ->where('meta_key', 'seo-desc-'.$page->id)
            ->select('meta_value')
            ->pluck('meta_value')
            ->first();

        $meta_desc = !empty($meta) ? $meta : '';
        return view('admin.pages.show', compact('page', 'slug', 'meta_desc'));
    }

    /**
     * @access public
     * @desc Display archives page with all editions grouped by year
     * @return \Illuminate\Contracts\View\View
     */
    public function archives()
    {
        // Get all editions (both published and unpublished) with article counts
        $editions = DB::table('editions')
            ->select('editions.*', DB::raw('COUNT(articles.id) as article_count'))
            ->leftJoin('articles', 'articles.edition_id', '=', 'editions.id')
            ->groupBy('editions.id', 'editions.title', 'editions.slug', 'editions.edition_date', 'editions.edition_price', 'editions.edition_cover', 'editions.edition_status', 'editions.created_at', 'editions.updated_at')
            ->orderBy('editions.edition_date', 'desc')
            ->get();

        // Group editions by year
        $archives = [];
        foreach ($editions as $edition) {
            $year = date('Y', strtotime($edition->edition_date));
            
            if (!isset($archives[$year])) {
                $archives[$year] = [];
            }

            $archives[$year][] = $edition;
        }

        // Sort years in descending order
        krsort($archives);

        return view('archives.index', compact('archives'));
    }

    /**
     * @access public
     * @desc Check server authentication
     * @return array|null
     */
    public function checkServerAuthentication()
    {
        $server = Helper::ajax_journal_is_demo_site();
        if (!empty($server)) {
            $response['message'] = $server->getData()->message;
            return $response;
        }
        return null;
    }
}

