<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class FileController extends Controller
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
     * @desc Get file path.
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getFile($filename)
    {
        // Decode HTML entities in filename (e.g., &amp; to &) since file is stored with original name
        $decoded_filename = html_entity_decode($filename, ENT_QUOTES, 'UTF-8');
        $file_parts = explode('-', $decoded_filename);
        $user_id = $file_parts[0];
        $file_path = 'uploads/articles/users/' . $user_id . '/' . $decoded_filename;
        
        // Check if file exists
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        } else {
            // If decoded filename doesn't exist, try with original filename (for backward compatibility)
            $file_parts_original = explode('-', $filename);
            $user_id_original = $file_parts_original[0];
            $file_path_original = 'uploads/articles/users/' . $user_id_original . '/' . $filename;
            if (Storage::exists($file_path_original)) {
                return Storage::download($file_path_original);
            }
            abort(404, 'File not found');
        }
    }

    /**
     * @access public   
     * @desc Get editor uploaded file for reviewer.
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getEditorFile($filename)
    {
        // Decode HTML entities in filename
        $decoded_filename = html_entity_decode($filename, ENT_QUOTES, 'UTF-8');
        $file_parts = explode('-', $decoded_filename);
        $article_id = $file_parts[0];
        $file_path = 'uploads/articles_editor/' . $article_id . '/' . $decoded_filename;
        
        // Check if file exists
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        } else {
            // Try with original filename
            $file_parts_original = explode('-', $filename);
            $article_id_original = $file_parts_original[0];
            $file_path_original = 'uploads/articles_editor/' . $article_id_original . '/' . $filename;
            if (Storage::exists($file_path_original)) {
                return Storage::download($file_path_original);
            }
            abort(404, 'File not found');
        }
    }
}

