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
            $file_parts = explode('-', $publish_file);
            $article_id = $file_parts[0];
            return Storage::download('uploads/articles_pdf/' . $article_id . '/' . $publish_file);
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
        $page = Page::getPageData($slug);
        if (empty($page)) {
            abort(404);
        }
        $meta = DB::table('sitemanagements')->where('meta_key', 'seo-desc-'.$page->id)->select('meta_value')->pluck('meta_value')->first();
        $meta_desc = !empty($meta) ? $meta : '';
        return view('admin.pages.show', compact('page', 'slug', 'meta_desc'));
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

