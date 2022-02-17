<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Topic;
use App\Models\SocialPost;
use Auth, DB;

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function editProfile(Request $request)
    {
        $user_id = Auth::user()->id;
        if(isset($request->formSubmitted)){
            $topics = json_encode($request->topics);
            $user = User::find($user_id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->topics = $topics;
            $user->location = $request->location;
            $user->save();
        }
        $user_info = User::find($user_id);
        return view('front.profile_edit', compact('user_info'));
    }

    public function dashboard_v3(Request $request)
    {
        $user_id = Auth::user()->id;
        $user_info = User::find($user_id);
        //echo '<pre>';print_r($user_info);exit;
        $data_from = ($request->data_from)? $request->data_from : 'twitter';
        $topics = ($request->topics)? $request->topics: json_decode($user_info->topics);
        $location = ($request->location)? $request->location: $user_info->location;
        $location = '';


        $social_post =array();

        $social_post = DB::table('postby_tags');
        $social_post->join('social_posts', 'postby_tags.post_id', '=', 'social_posts.id');
        $social_post->join('topics', 'postby_tags.topic_id', '=', 'topics.id');
        $social_post->where('social_posts.post_details', '!=', '');
        $social_post->where('social_posts.post_details', '!=', null);
        $social_post->select('postby_tags.post_id as pt_pid', 'postby_tags.topic_id as pt_tid', 'topics.topic_name', 'social_posts.*');
                
        if(isset($data_from) && $data_from != ''){
            $social_post->where('social_posts.data_from', '=', strtolower($data_from));
        }
        // if(isset($location) && $location != ''){
        //     $social_post->where('social_posts.author_location', '=', $location);
        // }
        if(!empty($topics)){
            $social_post->whereIn('postby_tags.topic_id', $topics);
        }
        $social_post = $social_post->get();
        //dd($social_post);
        //echo '<pre>';print_r($social_post);exit;

        return view('front.dashboard3', compact('social_post', 'data_from', 'topics', 'location'));
    }
}
