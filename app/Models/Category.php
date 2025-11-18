<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\UploadMedia;

class Category extends Model
{
    /**
     * @access protected 
     * @var array $fillable
     */
    protected $fillable = ['title', 'image', 'description'];

    /**
     * @access public
     * @desc Get all categories
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getCategories()
    {
        $categories = DB::table('categories')->paginate(5);
        return $categories;
    }

    /**
     * @access public
     * @param \Illuminate\Http\Request  $request
     * @desc Store categories in database
     * @return void
     */
    public function saveCategory($request)
    {
        if (!empty($request)) {
            $cat_img = UploadMedia::mediaUpload('category_image', $request, 'uploads/categories/');
            $this->title = htmlspecialchars($request['title'], ENT_QUOTES, 'UTF-8');
            $this->image = $cat_img;
            $this->description = htmlspecialchars($request['description'], ENT_QUOTES, 'UTF-8');
            $this->save();
        }
    }

    /**
     * @access public
     * @param \Illuminate\Http\Request  $request
     * @param int $category_id
     * @desc Update category
     * @return void
     */
    public function updateCategory($request, $category_id)
    {
        if (!empty($request) && !empty($category_id) && is_numeric($category_id)) {
            $category = $this->find($category_id);
            $path = 'uploads/categories/';
            if (!empty($request['hidden_category_image'])) {
                $image_parts = explode('.', $request['hidden_category_image']);
                $image_last_parts = end($image_parts);
                $cat_img = $path . '/' . $image_parts[0] . '-' . time() . '.' . $image_last_parts;
                if (!empty($request['category_image'])) {
                    $request['category_image']->getClientOriginalName();
                    $request['category_image']->move($path, $cat_img);
                }
            } else {
                $cat_img = UploadMedia::mediaUpload('category_image', $request, 'uploads/categories/');
            }
            $category->title = htmlspecialchars($request->title, ENT_QUOTES, 'UTF-8');
            $category->description = htmlspecialchars($request->description, ENT_QUOTES, 'UTF-8');
            $category->image = htmlspecialchars($cat_img, ENT_QUOTES, 'UTF-8');
            $category->save();
        }
    }

    /**
     * @access public
     * @desc Get list of article categories.
     * @return \Illuminate\Support\Collection
     */
    public static function getCategoriesList()
    {
        return DB::table('categories')->pluck('title', 'id')->prepend('Select Article Category', '');
    }

    /**
     * @access public
     * @param int $id
     * @desc Get category by id
     * @return object|null
     */
    public static function getCategoryByID($id)
    {
        if (!empty($id) && is_numeric($id)) {
            return DB::table('categories')->select('title', 'id')->where('id', $id)->first();
        }
    }

    /**
     * @access public
     * @desc Get reviewer by category
     * @return \Illuminate\Support\Collection
     */
    public static function getReviewersCategory()
    {
        return DB::table('categories')
            ->join('reviewers_categories', 'categories.id', '=', 'reviewers_categories.category_id')
            ->select('categories.*')
            ->groupBy('categories.id')
            ->get();
    }

    /**
     * @access public
     * @param array $categories
     * @param int $user_id
     * @desc Store category id and reviewer id in database
     * @return bool
     */
    public static function saveReviewerCategory($categories = [], $user_id)
    {
        if (!empty($categories) && !empty($user_id)) {
            $reviewers = DB::table('reviewers_categories')->select('reviewer_id')
                ->where('reviewer_id', $user_id)->get()->pluck('reviewer_id')->toArray();
            if (!empty($reviewers)) {
                if ((in_array($user_id, $reviewers))) {
                    DB::table('reviewers_categories')->where('reviewer_id', $user_id)->delete();
                }
            }
            foreach ($categories as $category) {
                $result = DB::table('reviewers_categories')->insert(
                    [
                        'category_id' => $category, 
                        'reviewer_id' => $user_id,
                        'created_at' => \Carbon\Carbon::now(), 
                        'updated_at' => \Carbon\Carbon::now()
                    ]
                );
            }
            return $result;
        }
    }

    /**
     * @access public
     * @param int $reviewer_id
     * @desc Categories assigned to reviewers
     * @return array
     */
    public static function getCategoryByReviewerID($reviewer_id)
    {
        if (!empty($reviewer_id) && is_numeric($reviewer_id)) {
            return DB::table('reviewers_categories')->select('category_id')
                ->where('reviewer_id', $reviewer_id)->get()->pluck('category_id')->toArray();
        }
    }
}

