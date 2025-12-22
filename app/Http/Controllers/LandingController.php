<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Plan;

class LandingController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('landing', compact('plans'));
    }
}
