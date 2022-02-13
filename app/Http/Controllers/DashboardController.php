<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialPost;
use App\Models\postbyTag;
use DB;
use Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('front.dashboard1');
    }

    public function dashboard_v2(Request $request)
    {
        $data_from = ($request->data_from)? $request->data_from : 'twitter';
        $topics = $request->topics;
        $location = $request->location;
        $social_post =array();

        $social_post = DB::table('postby_tags');
        $social_post->join('social_posts', 'postby_tags.post_id', '=', 'social_posts.id');
        $social_post->join('topics', 'postby_tags.topic_id', '=', 'topics.id');
        $social_post->select('postby_tags.post_id as pt_pid', 'postby_tags.topic_id as pt_tid', 'topics.topic_name', 'social_posts.*');
                
        if(isset($data_from) && $data_from != ''){
            $social_post->where('social_posts.data_from', '=', strtolower($data_from));
        }
        if(isset($location) && $location != ''){
            $social_post->where('social_posts.author_location', '=', $location);
        }
        if(!empty($topics)){
            $social_post->whereIn('postby_tags.topic_id', $topics);
        }
        $social_post = $social_post->paginate(100);
                
        return view('front.dashboard2', compact('social_post', 'topics', 'data_from', 'location'));
    }
    
    public function chart()
    {
        return view('front.chart');
    }
    
    public function dashboard_v4(Request $request)
    {
        /*
        $social_post = SocialPost::query();
        $social_post->where('serach_key', '!=', '');
        $social_post->where('serach_key', '!=', null);
        //$social_post->select(DB::raw('count(_id) as total_c, serach_key'));
        $social_post->selectRaw('count(_id) as number_of_orders, serach_key');
        $social_post->groupBy('serach_key');
        $social_post = $social_post->get();
        

        // if(!empty($social_post)){
        //     foreach($social_post as $post){
        //         echo '<pre>'; print_r($post);
        //     }
        // }

        dd($social_post);
        //echo '<pre>'; print_r($social_post);exit;
        //return view('front.dashboard4');
        */
        
        $topic_name = array();
        $post_count = array();

        $results = SocialPost::raw(function($collection){
            return $collection->aggregate([
                [
                    '$match' => [
                        'serach_key' => ['$ne' => null]
                    ]       
                ], 
                [       
                    '$group' => [
                        '_id' => '$serach_key',
                        'total_post' => ['$sum' => 1],
                        'total_like' => ['$sum' => '$like_count'],
                        'total_comment' => ['$sum' => '$comment_count'],
                    ]   
                ],
                [   
                    '$sort' => ['total_post' => -1]   
                ], 
            ]); 
        });

        //dd($results);

        if(!empty($results)){
            foreach($results as $val){
                $topic_name[] = $val->_id;
                $post_count[] = $val->total_post;
            }
        }
        $s_topic_name = "['" . implode("','",$topic_name) . "']";
        $s_post_count = "[" . implode(",",$post_count) . "]";
        return view('front.dashboard4', compact('s_topic_name', 's_post_count'));
    }
}
