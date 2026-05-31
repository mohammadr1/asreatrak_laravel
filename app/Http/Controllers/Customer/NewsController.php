<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // public function index(News $news)
    // {

    //     return view('Customer.news.index');
    // }

    public function show(){

        return view('Customer.news.show');

    }
    public function archive(){

        return view('Customer.news.archive');

    }
}
