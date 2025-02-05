<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function categories()
    {
        return view('settings.categories');
    }

    public function schools()
    {
        return view('settings.schools');
    }

    public function school()
    {
        return view('settings.school');
    }

    public function sector()
    {
        return view('settings.sector');
    }
}