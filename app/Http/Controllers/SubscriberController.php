<?php

/**
 * @package Scientific-Journal
 * @version 1.0
 * @author Amentotech <theamentotech@gmail.com>
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SubscriberController extends Controller
{
    /**
     * @access protected 
     * @var Subscriber $subscribers
     */
    protected $subscribers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Subscriber $subscribers)
    {
        $this->subscribers = $subscribers;
    }

    /**
     * @access public
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!empty($request)) {
            $this->subscribers->saveSubscriber($request);
            Session::flash('message', trans('prs.subscriber_added'));
            return redirect()->back();
        }
    }
}

