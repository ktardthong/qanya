<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Cache;
use Illuminate\Http\Response;

class Topic extends Model
{

    protected $table = 'topics';

    //Get topic replies
    public function getReplies($topic_uuid)
    {

        return $topic = DB::table('topics_reply')
            ->select('topics_reply.topic_uuid as topic_uuid',
                     'topics_reply.body',
                     'topics_reply.created_at as replycreated_at',
                     'users.*'
            )
            ->orderBy('topics_reply.created_at', 'desc')
            ->join('users', 'topics_reply.uid', '=', 'users.uuid')
            ->where('topics_reply.topic_uuid', $topic_uuid)
            ->limit(10)
            ->get();
    }

    //Get topic information
    public function getTopic($slug)
    {
        $results = Cache::remember('topic_posts_cache_'.$slug,1,function() use ($slug){
            return $topic = DB::table('topics')
                ->select(
                        'topics.topic',
                        'topics.body',
                        'topics.slug as topic_slug',
                        'users.displayname',
                        'topics.uuid as topic_uuid',
                        'topics.created_at as topic_created_at'
                        )
                ->join('users', 'topics.uid', '=', 'users.uuid')
                ->where('topics.slug',$slug)
                ->first();
        });
        return $results;
    }
}
