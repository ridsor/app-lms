<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request, $curriculum_id)
    {
        if (!$request->user()->can('subject.*')) {
            return abort(403);
        }

        return view('user.subject.index', compact('curriculum_id'));
    }
}
