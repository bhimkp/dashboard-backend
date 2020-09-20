<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
// use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;


class DemoController extends Controller
{
    public function index(){

        $artists = DB::table('artists')->select('artist_id', 'artist_name')->get();
        return $artists;
        // return view('user_view', ['artists' => $artists]);
    }   
}
