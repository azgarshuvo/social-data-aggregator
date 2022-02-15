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
        //dd($social_post);
                
        return view('front.dashboard2', compact('social_post', 'topics', 'data_from', 'location'));
    }
    
    public function chart()
    {
        return view('front.chart');

        
    }
    
    public function dashboard_v4(Request $request)
    {    
         
        $search_topic = $request->search_topic;

        //pie chart -- topic wise post
        $topic_name = array();
        $post_count = array();
        $pie_chart_data = DB::table('postby_tags');
        $pie_chart_data->join('topics', 'postby_tags.topic_id', '=', 'topics.id');
        $pie_chart_data->select('topics.topic_name', DB::raw('count(postby_tags.id) as post_count'));
        $pie_chart_data->groupBy('postby_tags.topic_id');
        $pie_chart_data->orderBy('post_count', 'DESC');
        $pie_chart_data = $pie_chart_data->paginate(7);

        if(!empty($pie_chart_data)){
            foreach($pie_chart_data as $val){
                $topic_name[] = $val->topic_name;
                $post_count[] = $val->post_count;
            }
        }
        $s_topic_name = "['" . implode("','",$topic_name) . "']";
        $s_post_count = "[" . implode(",",$post_count) . "]";



        //line chart -- month wise post
        $month = array();
        $month_wise_post = array();
        $line_chart_data = DB::table('postby_tags');
        $line_chart_data->join('social_posts', 'postby_tags.post_id', '=', 'social_posts.id');
        if(isset($search_topic)){
            $line_chart_data->where('postby_tags.topic_id', '=', $search_topic);
        }
        $line_chart_data->select(DB::raw('count(postby_tags.id) as `month_wise_post`'), DB::raw('MONTH(social_posts.post_date) as `month`'));
        $line_chart_data->groupBy('month');
        $line_chart_data->orderBy('month', 'ASC');
        $line_chart_data = $line_chart_data->paginate(6);

        if(!empty($line_chart_data)){
            foreach($line_chart_data as $l_data){
                $month[] = calender($l_data->month);
                $month_wise_post[] = $l_data->month_wise_post;
            }
        }

        $line_month = "['" . implode("','",$month) . "']";
        $line_post = "[" . implode(",",$month_wise_post) . "]";
        //dd($line_chart_data);


        //StackedBar graph -- like vs comment
        $sb_likes = array();
        $sb_comments = array();
        $sb_months = array();

        $stack_bar_data = DB::table('postby_tags');
        $stack_bar_data->join('social_posts', 'postby_tags.post_id', '=', 'social_posts.id');
        if(isset($search_topic)){
            $stack_bar_data->where('postby_tags.topic_id', '=', $search_topic);
        }
        $stack_bar_data->select(DB::raw('sum(social_posts.like_count) as `total_likes`'), DB::raw('sum(social_posts.comment_count) as `total_comments`'), DB::raw('MONTH(social_posts.post_date) as `month`'));
        $stack_bar_data->groupBy('month');
        $stack_bar_data->orderBy('month', 'ASC');
        $stack_bar_data = $stack_bar_data->paginate(6);

        if(!empty($stack_bar_data)){
            foreach($stack_bar_data as $sb_data){
                $sb_months[] = calender($sb_data->month);
                $sb_likes[] = $sb_data->total_likes;
                $sb_comments[] = $sb_data->total_comments;
            }
        }

        $sbg_months = "['" . implode("','",$sb_months) . "']";
        $sbg_likes = "[" . implode(",",$sb_likes) . "]";
        $sbg_comments = "[" . implode(",",$sb_comments) . "]";
        //dd($stack_bar_data);


        //Dognut Chart -- topic wise likes
        $d_topic_name = array();
        $d_total_like = array();
        $dg_chart_data = DB::table('postby_tags');
        $dg_chart_data->join('social_posts', 'postby_tags.post_id', '=', 'social_posts.id');
        $dg_chart_data->join('topics', 'postby_tags.topic_id', '=', 'topics.id');
        $dg_chart_data->select('topics.topic_name', DB::raw('sum(social_posts.like_count) as total_like'));
        $dg_chart_data->groupBy('postby_tags.topic_id');
        $dg_chart_data->orderBy('total_like', 'DESC');
        $dg_chart_data = $dg_chart_data->paginate(7);
        //dd($dg_chart_data);

        if(!empty($dg_chart_data)){
            foreach($dg_chart_data as $val){
                $d_topic_name[] = $val->topic_name;
                $d_total_like[] = $val->total_like;
            }
        }
        $dg_topic_name = "['" . implode("','",$d_topic_name) . "']";
        $dg_total_like = "[" . implode(",",$d_total_like) . "]";

        return view('front.dashboard4', compact('search_topic', 's_topic_name', 's_post_count', 'line_month', 'line_post', 'sbg_months', 'sbg_likes', 'sbg_comments', 'dg_topic_name', 'dg_total_like'));
        

        //SELECT COUNT(`id`), MONTH(post_date) as year_wise_post FROM `social_posts` GROUP BY MONTH(post_date);
        //https://intellipaat.com/community/3892/mysql-query-group-by-day-month-year


        
    }
}
