<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Categories;
use App\Topic as Topic;
use Illuminate\Support\Facades\DB as DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     *
     *
    */
    public function welcome()
    {
        $topics = Topic::where('flg',1)
                    ->orderBy('name', 'desc')
                    ->take(10)
                    ->get();
        return view('welcome',compact('topics'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categories::all();
        return view('home',compact('categories'));
    }


    public function getFeedCate(Request $request)
    {
        $feed   =   $request->slug;
        $topics =   DB::table('categories')
                        ->where('categories.slug',$request->slug)
                        ->join('topics', 'topics.categories', '=', 'categories.id')
                        ->get();
        return view('html.feed-list',compact('topics'));
    }
}
