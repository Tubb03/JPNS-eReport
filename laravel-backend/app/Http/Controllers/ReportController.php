<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit' => 'required|string|max:100',
            'program_name' => 'required|string|max:100',
            'program_date' => 'required|date',
            'description' => 'nullable|string|max:150',
            'objective' => 'nullable|string|max:300',
            'full_report' => 'required|string|max:1500',
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

    public function edit(Report $report)
    {
        if ($report->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $report->load('images');
        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        if ($report->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'unit' => 'required|string|max:100',
            'program_name' => 'required|string|max:100',
            'program_date' => 'required|date',
            'description' => 'nullable|string|max:150',
            'objective' => 'nullable|string|max:300',
            'full_report' => 'required|string|max:1500',
            'images_toremove' => 'nullable|array',
            'images_toremove.*' => 'integer|exists:report_images,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120'
        ]);

        $report->update([
            'unit' => $validated['unit'],
            'program_name' => $validated['program_name'],
            'program_date' => $validated['program_date'],
            'description' => $validated['description'],
            'objective' => $validated['objective'],
            'full_report' => $validated['full_report'],
        ]);

        if ($request->has('images_toremove')) {
            $imagesToDelete = ReportImage::whereIn('id', $validated['images_toremove'])->where('report_id', $report->id)->get();
            foreach ($imagesToDelete as $img) {
                $relativePath = str_replace('/storage/', '', $img->image_path);
                Storage::disk('public')->delete($relativePath);
                $img->delete();
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');
                ReportImage::create([
                    'report_id' => $report->id,
                    'image_path' => '/storage/' . $path
                ]);
            }
        }

        return redirect()->route('reports.show', $report)->with('success', 'Report updated successfully!');
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

    public function downloadPdf(Report $report)
    {
        $report->load('images', 'user');
        
        $pdf = Pdf::loadView('reports.pdf', compact('report'));
        
        // Optimize for A4 portrait
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Report_' . str_replace(' ', '_', $report->unit) . '_' . date('Y-m-d', strtotime($report->program_date)) . '.pdf';
        
        return $pdf->download($filename);
    }
}
