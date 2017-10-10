<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function store(Request $request)
    {
    	auth()->user()->settings()->merge(request('settings'));

    	return back();
    }
}
