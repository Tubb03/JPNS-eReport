<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::with('images', 'user')->latest()->get();
        return view('dashboard', compact('reports'));
    }
}
