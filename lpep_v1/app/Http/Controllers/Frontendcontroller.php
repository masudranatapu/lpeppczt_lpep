<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Frontendcontroller extends Controller
{
    public function aboutUs(){
        return view('frontend.about-us');
    }

    public function contact(){
        return view('frontend.contact');
    }

    public function mission(){
        return view('frontend.mission');
    }

    public function vision(){
        return view('frontend.vision');
    }

    public function service(){
        return view('frontend.service');
    }
}
