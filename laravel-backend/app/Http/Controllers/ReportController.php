<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit' => 'required|string',
            'program_name' => 'required|string',
            'program_date' => 'required|date',
            'description' => 'nullable|string',
            'objective' => 'nullable|string',
            'full_report' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|max:5120' // Max 5MB per image
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'unit' => $validated['unit'],
            'program_name' => $validated['program_name'],
            'program_date' => $validated['program_date'],
            'description' => $validated['description'],
            'objective' => $validated['objective'],
            'full_report' => $validated['full_report'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');
                ReportImage::create([
                    'report_id' => $report->id,
                    'image_path' => '/storage/' . $path
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Report submitted successfully!');
    }

    public function destroy(Report $report)
    {
        // Simple authorization check
        if ($report->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // The images table will automatically cascade delete, but we should also delete physical files
        foreach ($report->images as $img) {
            $relativePath = str_replace('/storage/', '', $img->image_path);
            Storage::disk('public')->delete($relativePath);
        }

        $report->delete();
        return response()->json(['message' => 'Report deleted successfully']);
    }

    public function staff()
    {
        $reports = Report::with('images', 'user')->latest()->get();
        return view('reports.staff', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load('images', 'user');
        return view('reports.show', compact('report'));
    }
}
