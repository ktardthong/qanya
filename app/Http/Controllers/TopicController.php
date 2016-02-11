<?php

namespace App\Http\Controllers;

use Auth;
use App\Topic as Topic;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon as Carbon;

use Illuminate\Contracts\Filesystem\Filesystem;


//SEO
use SEOMeta;
use OpenGraph;
use Twitter;
use SEO;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {


    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $topicUUID = rand(0, 10) . str_random(12) . rand(0, 10);
        $topicSlug = str_slug($request->postTitle, "-") . '-' . $topicUUID;

        if (Auth::user()->uuid) {
            if ($request->data) {
                $json = $request->data;

                $topic              = new Topic;
                $topic->uuid        = $topicUUID;
                $topic->uid         = Auth::user()->uuid;
                $topic->topic       = $json['title'];
                $topic->body        = $json['body'];
                $topic->categories  = $json['categories'];
                $topic->slug        = $topicSlug;
                $topic->save();

                return $topicSlug;
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  char  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($displayname,$slug)
    {
        DB::connection()->enableQueryLog();

        $topic = new Topic();
        $topic = $topic->getTopic($slug);

        if(empty($topic)){
            return "not found".$topic;
        }else{


            $log = DB::getQueryLog();
            print_r($log);
            $dt = Carbon::parse($topic->created_at);

            $title      = $topic->topic;
            $body       = $topic->body;
            $username   = $topic->name;
            $created_at = $dt->diffForHumans();

            SEOMeta::setTitle($title);
            SEOMeta::setDescription(str_limit($body,152));


            OpenGraph::setTitle($title);
            OpenGraph::setDescription($body);
            /*OpenGraph::setUrl('http://current.url.com');
            OpenGraph::addProperty('type', 'articles');*/


            return view('pages.topic.topic',
                compact('title','body','username','created_at'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
