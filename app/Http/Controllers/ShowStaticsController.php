<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ShowStaticsController extends Controller
{
    public function countDownloads(){
        $label_auth_id = '034232af46545d47a4fabff7171a856f';
        //For Download Count
        $label_id = DB::table('label_master')
        ->select('label_id')
        ->where('label_auth_id', '=', $label_auth_id)
        ->first();
        $label_id = $label_id->label_id;

        $download = DB::table('download_history')
        ->where('download_label_id', '=', $label_id)
        ->count();

        //For Previews Count
        $previews = DB::table('preview_log')
        ->where('preview_label_id', '=', $label_id)
        ->count();
        
        //For Feedback count
        $feedback = DB::table('feedback_master')
        ->where('feedback_member_id', '=', $label_id)
        ->count();
        //For Average rating
        $Avgrating = DB::table('feedback_master')
        ->where('feedback_member_id', '=', $label_id)
        ->avg('feedback_rating');

        $Avgrating = round($Avgrating);

        $chance_of_playing = round(($previews  * 100) / ($download + $previews)); 
        
        $rating = '';
        if($Avgrating < 3){
            $rating = 'POOR';
        }
        elseif($Avgrating > 3 && $Avgrating < 5){
            $rating = 'AVERAGE';
        }
        elseif($Avgrating >= 5 && $Avgrating < 7){
            $rating = 'GOOD';
        }
        elseif($Avgrating >= 7 && $Avgrating < 9){
            $rating = 'VERY GOOD';
        }
        else{
            $rating = 'EXCELLENT';
        }

        //For Response Doughnut

        $count_rating = DB::table('feedback_master')
        ->where('feedback_member_id', '=', $label_id)
        ->count();

        $rating_array = [['feedback_member_id', '=', $label_id]];

        $ratings = DB::table('feedback_master')
        ->select('feedback_rating', DB::raw('count(*) as ratingcount'))
        ->where($rating_array)
        ->groupby('feedback_rating')
        ->get();


        $poor_rating = 0;
        $avg_rating = 0;
        $good_rating = 0;
        $very_good_rating = 0;
        $excellent_rating = 0;
        foreach($ratings as $rat){
            if($rat->feedback_rating < 3){
                $poor_rating = ceil((($poor_rating + $rat->ratingcount) / $count_rating) * 100);
            }
            elseif($rat->feedback_rating >= 3 && $rat->feedback_rating < 5){
                $avg_rating = ceil((($avg_rating + $rat->ratingcount) / $count_rating) * 100);
            }
            elseif($rat->feedback_rating >= 5 && $rat->feedback_rating < 7){
                $good_rating = ceil((($good_rating + $rat->ratingcount) / $count_rating) * 100);
            }
            elseif($rat->feedback_rating >= 7 && $rat->feedback_rating < 9){
                $very_good_rating = ceil((($very_good_rating + $rat->ratingcount) / $count_rating) * 100);
            }
            else{
                $excellent_rating = ceil((($excellent_rating + $rat->ratingcount) / $count_rating) * 100);
            }
        }
       
        $rating_arr = [$poor_rating, $avg_rating, $good_rating, $very_good_rating, $excellent_rating];

        //For Track details(Favourite Mix)
        $tracks = DB::table('download_history')
        ->select('track_mix', 'track_download_count')
        ->join('track_master', 'download_track_id', '=', 'track_id')
        ->limit(10)
        ->distinct()
        ->get();
        $sum = 0;
        $percentArray = array();
        foreach($tracks as $track){
            $sum += $track->track_download_count;
        }
        foreach($tracks as $track){
            $percent = (int)(($track->track_download_count / $sum) * 100);
            array_push($percentArray, $percent);
        }
        $trackArray = array();
        foreach($tracks as $track){
            array_push($trackArray,$track->track_mix);
        }

        $count_play = DB::table('feedback_master')
        ->where('feedback_member_id', '=', $label_id)
        ->count('feedback_willplay');
        // return $count_rating;

        $play_arr = DB::table('feedback_master')
        ->select('feedback_willplay', DB::raw('count(*) as play_count'))
        ->where('feedback_member_id', '=', $label_id)
        ->groupby('feedback_willplay')
        ->get();

        //return $play_arr;
        $play_0 = 0;
        $play_1 = 0;
        $play_2 = 0;
        foreach($play_arr as $play){
            if($play->feedback_willplay == 0){
                $play_0 = ceil(($play->play_count / $count_play) * 100);
            }
            elseif($play->feedback_willplay == 1){
                $play_1 = ceil(($play->play_count / $count_play) * 100);
            }
            else{
                $play_2 = ceil(($play->play_count / $count_play) * 100);
            }
        }
        // return [$play_0, $play_1, $play_2];
        $play_data = [$play_0, $play_1, $play_2];

        //For DJ Activity

        // $dj_activity = DB::table('member_master')
        // ->select('')

        //For Radio Activity
        $radio_activity = DB::table('member_master')
        ->select('member_group_list', DB::raw('count(download_id) as d_id'))
        ->join('radio_master', 'member_id', '=', 'radio_member_id')
        ->join('download_history', 'member_auth_id', '=', 'download_user_auth_id')
        ->groupby('member_group_list')
        ->limit(10)
        ->get();
        //return $radio_activity;

        return [
        'download' => $download,
        'previous' => $previews,
        'feedback' => $feedback,
        'Avgrating' => $Avgrating,
        'rating' => $rating,
        'chanceOfPlaying' => $chance_of_playing,
        'tracks' => $tracks,
        'percentage' => $percentArray,
        'trackname' => $trackArray,
        'rating_arr' => $rating_arr,
        'play_data' => $play_data];
    }
    public function getRegionalSummary(){
        $label_auth_id = '034232af46545d47a4fabff7171a856f';
        $label_id = DB::table('label_master')
        ->select('label_id')
        ->where('label_auth_id', '=', $label_auth_id)
        ->first();
        $label_id = $label_id->label_id;
        
        $member_id = DB::table('download_history')
        ->select('country_name', DB::raw('ROUND(AVG(feedback_master.feedback_rating)) as avgRating'),
         DB::raw('count(download_history.download_id) as totalDownload'))
        ->join('member_master', 'download_user_auth_id', '=', 'member_auth_id')
        ->join('feedback_master', 'member_id', '=', 'feedback_member_id')
        ->join('country_master', 'member_master.member_country', '=', 'country_master.country_iso_2')
        ->groupby('country_name')
        ->limit(10)
        ->get();

        $radios = DB::table('radio_master')
        ->select('radio_station_name','radio_show_name', 'member_djname', 'radio_station_city')
        ->join('member_master', 'radio_member_id', '=', 'member_id')
        ->skip(10)
        ->limit(10)
        ->get();
    
        return [
        'regions' => $member_id,
        'radios' => $radios
        ];
    }
}
