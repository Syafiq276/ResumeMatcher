<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\Request;
use App\Models\Resume;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{
    public function index()
    {
        $resumes = Resume::where('user_id', auth()->id())->latest()->get();
        return view('resumes.index', compact('resumes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'resume' => 'required|mimes:pdf,docx|max:2048',
            'alias' => 'nullable|string|max:255',
        ]);

        $path = $request->file('resume')->store('resumes');

        // Check if this is the first resume, if so make it default
        $isFirst = !Resume::where('user_id', auth()->id())->exists();

        Resume::create([
            'user_id' => auth()->id(),
            'file_path' => $path,
            'alias' => $request->alias ?? 'Resume ' . now()->format('M d, Y'),
            'is_default' => $isFirst,
        ]);

        return back()->with('success', 'Resume uploaded successfully.');
    }

    public function destroy(Resume $resume)
    {
        if ($resume->user_id !== auth()->id()) {
            abort(403);
        }

        Storage::delete($resume->file_path);
        $resume->delete();

        return back()->with('success', 'Resume deleted.');
    }
}
