<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Page extends Model
{
    /**
     * @access protected
     * @var array $fillable
     */
    protected $fillable = ['title', 'slug', 'sub_title', 'body'];

    /**
     * @access public
     * @param string $value
     * @desc Set slug before saving in DB
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $temp = Str::slug($value, '-');
        if (static::where('slug', $temp)->exists()) {
            $i = 1;
            $new_slug = $temp . '-' . $i;
            while (static::where('slug', $new_slug)->exists()) {
                $i++;
                $new_slug = $temp . '-' . $i;
            }
            $temp = $new_slug;
        }
        $this->attributes['slug'] = $temp;
    }

    /**
     * @access public
     * @desc Get pages from database
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getPages()
    {
        $pages = DB::table('pages')->paginate(5);
        return $pages;
    }

    /**
     * @access public
     * @param \Illuminate\Http\Request  $request
     * @desc Store page data in database
     * @return int
     */
    public function savePage($request)
    {
        if (!empty($request)) {
            $this->title = htmlspecialchars($request->title, ENT_QUOTES, 'UTF-8');
            $this->slug = htmlspecialchars($request->title, ENT_QUOTES, 'UTF-8');
            $this->sub_title = htmlspecialchars($request->sub_title, ENT_QUOTES, 'UTF-8');
            $this->body = $request->content;
            if ($request->parent_id) {
                $this->relation_type = 1;
            } else {
                $this->relation_type = 0;
            }
            $this->save();
            $page_id =  $this->id;
            if (!empty($request['seo_desc'])) {
                DB::table('sitemanagements')->insert(
                    [
                        'meta_key' => 'seo-desc-'.$page_id, 
                        'meta_value' => $request['seo_desc'],
                        "created_at" => Carbon::now(), 
                        "updated_at" => Carbon::now()
                    ]
                );
            }
            if (!empty($request['show_page'])) {
                DB::table('sitemanagements')->insert(
                    [
                        'meta_key' => 'show-page-'.$page_id, 
                        'meta_value' => $request['show_page'],
                        "created_at" => Carbon::now(), 
                        "updated_at" => Carbon::now()
                    ]
                );
            }
            return $page_id;
        }
    }

    /**
     * @access public
     * @param int $id
     * @param \Illuminate\Http\Request  $request
     * @desc Update page data in database
     * @return void
     */
    public function updatePage($id, $request)
    {
        if (!empty($id) && !empty($request)) {
            $pages = static::find($id);
            $pages->title = htmlspecialchars($request->title, ENT_QUOTES, 'UTF-8');
            if ($pages->title != $request->title) {
                $pages->slug = htmlspecialchars($request->title, ENT_QUOTES, 'UTF-8');
            }
            $pages->sub_title = htmlspecialchars($request->sub_title, ENT_QUOTES, 'UTF-8');
            $pages->body = $request->content;
            if ($request->parent_id == null) {
                $pages->relation_type = 0;
            } elseif ($request->parent_id) {
                $pages->relation_type = 1;
            }
            $pages->save();
            if (!empty($request['seo_desc'])) {
                DB::table('sitemanagements')->where('meta_key', '=', 'seo-desc-'.$id)->delete();
                DB::table('sitemanagements')->insert(
                    [
                        'meta_key' => 'seo-desc-'.$id, 
                        'meta_value' => $request['seo_desc'],
                        "created_at" => Carbon::now(), 
                        "updated_at" => Carbon::now()
                    ]
                );
            }
            if (!empty($request['show_page'])) {
                DB::table('sitemanagements')->where('meta_key', '=', 'show-page-'.$id)->delete();
                DB::table('sitemanagements')->insert(
                    [
                        'meta_key' => 'show-page-'.$id, 
                        'meta_value' => $request['show_page'],
                        "created_at" => Carbon::now(), 
                        "updated_at" => Carbon::now()
                    ]
                );
            }
        }
    }

    /**
     * @access public
     * @desc Get page data
     * @param string $slug
     * @return object|null
     */
    public static function getPageData($slug)
    {
        if (!empty($slug) && is_string($slug)) {
            return DB::table('pages')->select('*')->where('slug', $slug)->first();
        }
    }

    /**
     * @access public
     * @desc Get page slug
     * @param int $id
     * @return object|null
     */
    public static function getPageslug($id)
    {
        if (!empty($id) && is_numeric($id)) {
            return DB::table('pages')->select('slug')->where('id', $id)->first();
        }
    }

    /**
     * @access public
     * @desc Get parent page
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function getParentPages($id = '')
    {
        if (!empty($id)) {
            return DB::table('pages')->where('relation_type', '=', 0)->where('id', '!=', $id)->pluck('title', 'id')->prepend('Select parent', '');
        } else {
            return DB::table('pages')->where('relation_type', '=', 0)->pluck('title', 'id')->prepend('Select parent', '');
        }
    }

    /**
     * @access public
     * @desc Get page list
     * @return \Illuminate\Support\Collection
     */
    public static function getPageList()
    {
        return DB::table('pages')->select('title', 'slug')->pluck('title', 'slug');
    }

    /**
     * @access public
     * @desc Get child page
     * @param int $child_id
     * @return object|null
     */
    public static function getChildPages($child_id)
    {
        return DB::table('pages')->select('title', 'slug', 'id')->where('id', $child_id)->first();
    }

    /**
     * @access public
     * @desc Get Parent Pages
     * @param int $page_id
     * @return array
     */
    public static function pageHasChild($page_id)
    {
        if (!empty($page_id) && is_numeric($page_id)) {
            return DB::table('pages')
                ->join('parent_child_pages', 'pages.id', '=', 'parent_child_pages.parent_id')
                ->select('pages.id', 'pages.title', 'parent_child_pages.child_id')
                ->where('parent_child_pages.parent_id', '=', $page_id)
                ->get()->all();
        }
    }
}

