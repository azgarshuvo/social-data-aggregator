<?php
use App\Models\SocialPost;
use App\Models\Topic;

function search_topic(){
    $search_topics = array("Afghanistan","COVID","FIFA22","Dune","AMC","dogecoin","SquidGame","Sports","T20worldcup","Ethereum","TigerWoods","Batllefield2042");
    return $search_topics;
}

function searchTopicOption($topics){
    $selected_array = $topics;
    $searchArray = Topic::get();
    //echo '<pre>';print_r($searchArray);exit;
    if(!empty($selected_array)){
        foreach($searchArray as $value){
            $sel=(in_array($value->id, $selected_array))?' selected="selected"':'';
                echo '<option value="'.$value->id.'"'.$sel.'>'.$value->topic_name.'</option>';
            }
    }else{
        foreach($searchArray as $value){
            echo '<option value="'.$value->id.'">'.$value->topic_name.'</option>';
        }
    }
}

function searchTopicOption2($topics){
    $selected_array = json_decode($topics);
    $searchArray = Topic::get();
    if($selected_array != null && $selected_array != ''){
        if(!empty($selected_array)){
            foreach($searchArray as $value){
            $sel=(in_array($value->id, $selected_array))?' selected="selected"':'';
                echo '<option value="'.$value->id.'"'.$sel.'>'.$value->topic_name.'</option>';
            }
        }
    }else{
        foreach($searchArray as $value){
            echo '<option value="'.$value->id.'">'.$value->topic_name.'</option>';
        }
    }
}

function platform(){
    $platform = array("Twitter", "Reddit", "LinkedIn");
    return $platform;
}

function platformOption($selected = false){
    $platform_option = '';
    $platform = platform();
    if($platform){
        foreach($platform as $value){
            $sel = (strtolower($value) == strtolower($selected))? ' selected="selected"':'';
            $platform_option .= '<option value="'.$value.'"'.$sel.'>'.$value.'</option>';
        }
    }
    return $platform_option;
}

function locationOption($selected = false){
    $location_option = '<option value="">--select location--</option>';
    $locations = SocialPost::where('author_location', '!=', null)->where('author_location', '!=', '')->groupBy('author_location')->get();
    if($locations){
        foreach($locations as $value){
            $sel = (strtolower($value->author_location) == strtolower($selected))? ' selected="selected"':'';
            $location_option .= '<option value="'.$value->author_location.'"'.$sel.'>'.$value->author_location.'</option>';
        }
    }
    return $location_option;
}


?>