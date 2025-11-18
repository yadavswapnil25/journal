<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App;

use Illuminate\Support\Facades\Storage;

class UploadMedia
{
    /**
     * @access public
     * @param string $type
     * @param \Illuminate\Http\Request  $request
     * @param string $path
     * @desc Upload media 
     * @return string
     */
    public static function mediaUpload($type, $request, $path)
    {
        if ($request->hasfile($type)) {
            $file = $request->file($type);
            // getting image Full Name
            $file_original_name = $file->getClientOriginalName();
            // getting image Name Without extension
            $file_name_without_extension = pathinfo($file_original_name, PATHINFO_FILENAME);
            // getting image extension
            $extension = $file->getClientOriginalExtension();
            $filename = $file_name_without_extension . '-' . time() . '.' . $extension;
            $file->move($path, $filename);
            return $path . $filename;
        } else {
            return '';
        }
    }

    /**
     * @access public
     * @param string $full_image_name
     * @desc Get image name
     * @return string
     */
    public static function getImageName($full_image_name)
    {
        if (!empty($full_image_name)) {
            $image_parts = explode('/', $full_image_name);
            $image_last_parts = end($image_parts);
            $extension = explode('.', $image_last_parts);
            $img_timestamp = explode('-', $extension[0]);
            $timestamp = "-" . end($img_timestamp);
            $image_name = str_replace($timestamp, '', $image_last_parts);
            return $image_name;
        } else {
            return '';
        }
    }

    /**
     * @access public
     * @param int $bytes
     * @desc Get image Size units
     * @return string
     */
    public static function formatSizeUnits($bytes)
    {
        if (!empty($bytes)) {
            if ($bytes >= 1073741824) {
                $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            } elseif ($bytes >= 1048576) {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            } elseif ($bytes > 1) {
                $bytes = $bytes . ' bytes';
            } elseif ($bytes == 1) {
                $bytes = $bytes . ' byte';
            } else {
                $bytes = '0 bytes';
            }

            return $bytes;
        } else {
            return '';
        }
    }

    /**
     * @access public
     * @param int $author_id
     * @param string $file_name
     * @desc Get article size
     * @return string
     */
    public static function getArticleSize($author_id, $file_name)
    {
        if (!empty($author_id) && !empty($file_name)) {
            $size = Storage::size('uploads/articles/users/' . $author_id . '/' . $file_name);
            $article_file_size = UploadMedia::formatSizeUnits($size);
            return $article_file_size;
        } else {
            return '';
        }
    }
}

