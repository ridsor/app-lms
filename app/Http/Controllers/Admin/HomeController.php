<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class HomeController extends Controller
{
    public function index()
    {
        $activities = Activity::with('causer')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.home', compact('activities'));
    }
}
