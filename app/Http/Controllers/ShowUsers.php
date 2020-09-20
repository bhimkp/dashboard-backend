<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class ShowUsers extends Controller
{
    public function index(){
        return "hi";

        $artists = DB::table('artists')->select('artist_id', 'artist_name')->get();
        // return view('user_view', ['artists' => $artists]);
        return $artists;
    }   
}
