<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use App\Models\SocialPost;
use App\Models\Topic;
use App\Models\postbyTag;
use DB;

class ApiController extends Controller
{
    public function twitterData(){
        $insert_data = array();
        $postbytag_ids = array();
        $post_id = '';
        $topic_id = '';

        //$json = Storage::disk('local')->get('jsonFiles/twitter.json');

        //REST API
        $curl = curl_init();
        $twitter_url = 'http://localhost:8080/api/Twitter/search-tweet?trends=Afghanistan,COVID,FIFA22,Dune,AMC,SquidGame,T20worldcup,Ethereum,TigerWoods,Batllefield2042&limit=50';
        //$reddit_url = 'http://localhost:8080/api/Reddit/search-reddit?trends=afghanistan,COVID19,FIFA22,dune,dogecoin,squidgame,sports,ethereum,tigerwoods,battlefield2042&limit=100';
        curl_setopt_array($curl, array(
            CURLOPT_URL => $twitter_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        //END REST API

        $datas = json_decode($response, true);
        //echo '<pre>';print_r($datas['data']);exit;
        //dd($datas);
        if($datas){
            foreach($datas['data'] as $data){
                //echo '<pre>';print_r($data);exit;
                $topic = $data['subject'];
                $post = new SocialPost();
                
                $post->data_from = 'twitter';
                $post->post_id = $data['id'];
                $post->serach_key = $data['subject'];
                $post->post_details = $this->remove_emoji(trim(str_replace("\n", '', $data['text'])));
                $post->like_count = $data['likes'];
                $post->comment_count = $data['retweet_count'];
                $post->author_name = $this->remove_emoji($data['user_name']);
                $post->author_bio = '';
                $post->author_location = $this->remove_emoji($data['location']);
                $post->post_date = $data['date'];
                $post->language = $data['language'];
                $post->post_url = '';

                if(str_contains($topic, '#')){
                    $exploded_topic = explode("#", $topic);
                    $topic_name = $exploded_topic[1];
                    $topic_title = $topic;
                }else{
                    $topic_name = $topic;
                    $topic_title = '#'.$topic;
                } 

                $find_post = SocialPost::select('post_id')->where('post_id', $data['id'])->first();
                
                $find_topic = Topic::where(function($query) use ($data) {
                                $query->where('topic_name', $data['subject'])
                                      ->orWhere('topic_title', $data['subject']);
                                })->first();
                
                //$find_topic = Topic::where('topic_name', $topic_name)->first();
                
                if(empty($find_post)){
                    $post->save();
                    $post_id = $post->id;
                    
                    if(empty($find_topic)){
                        $topic = new Topic();
                        $topic->topic_name = $topic_name;
                        $topic->topic_title = $topic_title;
                        $topic->save();
                        $topic_id = $topic->id;
                    }
                    
                    $post_id = ($post_id)? $post_id: $find_post->id;
                    $topic_id = ($topic_id)? $topic_id: $find_topic->id;
                    
                    if($post_id && $topic_id){
                        $postbyTag = new postbyTag();
                        $postbyTag->post_id = $post_id;
                        $postbyTag->topic_id = $topic_id;
                        $postbyTag->notes = 'twitter';
                        $postbytag_ids[] = $postbyTag->save();
                    }
                }
            }//endforeach
        }

        $message = count($postbytag_ids).' data has been inserted successfuly for '.$post->data_from;
        return view('front.message', compact('message'));
        
    }

    public function redditData(){
        $insert_data = array();
        $postbytag_ids = array();
        $post_id = '';
        $topic_id = '';
        //$json = Storage::disk('local')->get('jsonFiles/reddit.json');

        //REST API
        $curl = curl_init();
        //$twitter_url = 'http://localhost:8080/api/Twitter/search-tweet?trends=Afghanistan,COVID,FIFA22,Dune,AMC,SquidGame,T20worldcup,Ethereum,TigerWoods,Batllefield2042&limit=50';
        $reddit_url = 'http://localhost:8080/api/Reddit/search-reddit?trends=afghanistan,COVID19,FIFA22,dune,dogecoin,squidgame,sports,ethereum,tigerwoods,battlefield2042&limit=50';
        curl_setopt_array($curl, array(
            CURLOPT_URL => $reddit_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        //END REST API


        $datas = json_decode($response, true);

        
        //echo '<pre>';print_r($datas);exit;
        if(!empty($datas)){
            foreach($datas['data'] as $data){
                $topic = $data['subject'];
                $post = new SocialPost();
                
                $post->data_from = 'reddit';
                $post->post_id = $data['id'];
                $post->serach_key = $topic;
                $post->post_details = $this->remove_emoji(trim(str_replace("\n", '', $data['text'])));
                $post->like_count = $data['score'];
                $post->comment_count = $data['num_comments'];
                $post->author_name = $this->remove_emoji($data['name_author']);
                $post->author_bio = '';
                $post->author_location = '';
                $post->post_date = @$data['date'];
                $post->language = '';
                $post->post_url = @$data['url'];

                if(str_contains($topic, '#')){
                    $exploded_topic = explode("#", $topic);
                    $topic_name = $exploded_topic[1];
                    $topic_title = $topic;
                }else{
                    $topic_name = $topic;
                    $topic_title = '#'.$topic;
                } 

                $find_post = SocialPost::select('post_id')->where('post_id', $data['id'])->first();
                
                $find_topic = Topic::where(function($query) use ($data) {
                                $query->where('topic_name', $data['subject'])
                                      ->orWhere('topic_title', $data['subject']);
                                })->first();
                
                if(empty($find_post)){
                    $post->save();
                    $post_id = $post->id;
                    
                    if(empty($find_topic)){
                        $topic = new Topic();
                        $topic->topic_name = $topic_name;
                        $topic->topic_title = $topic_title;
                        $topic->save();
                        $topic_id = $topic->id;
                    }
                    
                    $post_id = ($post_id)? $post_id: $find_post->id;
                    $topic_id = ($topic_id)? $topic_id: $find_topic->id;
                    
                    if($post_id && $topic_id){
                        $postbyTag = new postbyTag();
                        $postbyTag->post_id = $post_id;
                        $postbyTag->topic_id = $topic_id;
                        $postbyTag->notes = 'reddit';
                        $postbytag_ids[] = $postbyTag->save();
                    }
                }
            }

            $message = count($postbytag_ids).' data has been inserted successfuly for '.$post->data_from;
            return view('front.message', compact('message'));
        }
    }

    public function linkedInData(){
        $insert_data = array();
        $postbytag_ids = array();
        $post_id = '';
        $topic_id = '';
        $json = Storage::disk('local')->get('jsonFiles/linkedin.json');
        $datas = json_decode($json, true);

        //echo '<pre>'; print_r($datas);exit;

        if($datas){
            foreach($datas as $data){
                if(!empty($data['hashtags'])){
                    $topic = $data['hashtags'];
                    $serach_key = $this->remove_emoji(trim(str_replace("\n", '', json_encode($topic))));
                    $post = new SocialPost();
                    
                    $post->data_from = 'linkedin';  
                    $post->post_id = rand(111, 11111);
                    $post->serach_key = $serach_key;
                    $post->post_details = $this->remove_emoji(trim(str_replace("\n", '', $data['content'])));
                    $post->like_count = @$data['like_count'];
                    $post->comment_count = @$data['comment_count'];
                    $post->author_name = $this->remove_emoji($data['author']);
                    $post->author_bio = $data['author_info'];
                    $post->author_location = @$data['location'];
                    $post->post_date = @$data['approx_date'];
                    $post->language = @$data['language'];
                    $post->post_url = @$data['linkedin_url'];
                    $post->save();
                    $post_id = $post->id;

                    if($post_id){
                        if($topic){
                            foreach($topic as $tp){
                                $t_name =  $this->remove_emoji(trim(str_replace("\n", '', $tp)));
                                if(str_contains($t_name, '#')){
                                    $exploded_topic = explode("#", $t_name);
                                    $topic_name = $exploded_topic[1];
                                    $topic_title = $t_name;
                                }else{
                                    $topic_name = $t_name;
                                    $topic_title = '#'.$t_name;
                                } 
                                
                                $find_topic = Topic::where('topic_name', $topic_name)->first();
                                //dd($find_topic);
    
                                if(empty($find_topic)){
                                    $topic = new Topic();
                                    $topic->topic_name = $topic_name;
                                    $topic->topic_title = $topic_title;
                                    $topic->save();
                                    $topic_id = $topic->id;
                                }
    
                                $topic_id = ($topic_id)? $topic_id: $find_topic->id;
                                
                                if($post_id && $topic_id){
                                    $postbyTag = new postbyTag();
                                    $postbyTag->post_id = $post_id;
                                    $postbyTag->topic_id = $topic_id;
                                    $postbyTag->notes = 'linkedin';
                                    $postbytag_ids[] = $postbyTag->save();
                                }
                            }//foreach
                        }//endif
                    }//endif
                }//endif
            }//endforeach
            echo count($postbytag_ids).' entry';
        }
    }

    public function pre_linkedInData(){
        $insert_data = array();
        $insert_count = array();
        $json = Storage::disk('local')->get('jsonFiles/linkedin.json');
        $datas = json_decode($json, true);

        echo '<pre>'; print_r($datas);exit;

        if($datas){
            foreach($datas as $data){

                $post = new SocialPost();
                
                $post->data_from = 'linkedin';
                $post->post_id = rand(111, 11111);
                $post->serach_key = @$data['subject'];
                $post->post_details = $this->remove_emoji(trim(str_replace("\n", '', $data['content'])));
                $post->like_count = $data['like_count'];
                $post->comment_count = $data['comment_count'];
                $post->author_name = $this->remove_emoji($data['author']);
                $post->author_bio = $data['author_info'];
                $post->author_location = @$data['location'];
                $post->post_date = @$data['date'];
                $post->language = @$data['language'];
                $post->post_url = @$data['url'];

                $find_post = SocialPost::select('post_id')->where('post_id', rand(111, 11111))->first();
                
                if(empty($find_post)){
                    $insert_count[] = $post->save();
                }
            }
            echo count($insert_count).' rows has been inserted';
        }
    }

    function remove_emoji($string)
    {
        // Match Enclosed Alphanumeric Supplement
        $regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
        $clear_string = preg_replace($regex_alphanumeric, '', $string);

        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $clear_string);

        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);
        
        // Match Supplemental Symbols and Pictographs
        $regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
        $clear_string = preg_replace($regex_supplemental, '', $clear_string);

        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        return $clear_string;
    }

    function deleteData(){
        exit;
        $data=SocialPost::where('data_from', 'reddit');
        $deleted = $data->delete();
        if($deleted){
            echo "data has been deleted";
        }
    }

    function insertTopics(){
        exit;
        $topics = search_topic();
        $i=1;
        if(!empty($topics)){
            foreach($topics as $topic){
                $topics = new Topic();
                $topics->topic_name = $topic;
                $topics->topic_title = $topic;
                $topics->order = $i;
                $topics->save();
                $i++;
            }
        }
    }

    function TestApi(){
        $curl = curl_init();
        //$url = "http://localhost:8080/api/Reddit/search-reddit?trends=Covid,%20Vaccine";
        $twitter_url = 'http://localhost:8080/api/Twitter/search-tweet?trends=Afghanistan,COVID,FIFA22,Dune,AMC,SquidGame,T20worldcup,Ethereum,TigerWoods,Batllefield2042&limit=100';
        $reddit_url = 'http://localhost:8080/api/Reddit/search-reddit?trends=afghanistan,COVID19,FIFA22,dune,dogecoin,squidgame,sports,ethereum,tigerwoods,battlefield2042&limit=100';
        curl_setopt_array($curl, array(
            CURLOPT_URL => $reddit_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $results = json_decode($response);
        //echo count($results);
        dd($results);
        echo '<pre>';print_r($results);

        curl_close($curl);
        echo $response;
    }

}
