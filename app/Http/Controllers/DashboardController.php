<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class DashboardController extends Controller
{

    public function index()
    {
        addVendors(['amcharts', 'amcharts-maps', 'amcharts-stock']);


        return view('pages/dashboards.index');
    }
    public function directionss($lang){
//        dd($lang);

        session(['locale'=>$lang]);
//        App::setLocale($lang);
//        dd((app()->getLocale() == 'ar'));
//        if (session('locale') == 'ar') {
//            $direction = 'rtl';
//        } else
//            $direction = 'ltr';
//
//        Config::set('settings.KT_THEME_DIRECTION', $direction);
//        dd(config('settings.KT_THEME_DIRECTION'));
//    session(['locale' => $lang]);
//    dd((app()->getLocale() == 'ar'));
        return redirect()->back();
    }
}
