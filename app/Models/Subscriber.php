<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    /**
     * @access protected 
     * @var array $fillable
     */
    protected $fillable = ['name', 'email'];

    /**
     * @access public
     * @param \Illuminate\Http\Request  $request
     * @desc Store subscriber data in database
     * @return void
     */
    public function saveSubscriber($request)
    {
        $this->name = $request->user_name;
        $this->email = $request->email;
        $this->save();
    }
}

