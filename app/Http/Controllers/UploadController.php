<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\JobAd;
use App\Models\AnalysisResult;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'resume' => 'required|mimes:pdf,docx|max:2048',
            'job_description' => 'required|string',
        ]);

        // 1. Store the uploaded file
        $filePath = $request->file('resume')->store('resumes'); // relative path
        $absolutePath = Storage::path($filePath); // Get absolute path efficiently via driver

        if (!file_exists($absolutePath)) {
            return back()->with('error', "File upload failed. Path not found: $absolutePath");
        }

        // 2. Prepare Python command
        // We use the absolute path to the python executable in our venv
        $pythonPath = base_path('venv/Scripts/python.exe');
        $scriptPath = base_path('python_scripts/analyzer.py');

        // Pass arguments: resume path and job description
        // Note: Job description might be long, so we might want to pass it as a file or base64 if it causes issues.
        // For now, let's pass it as a command line argument, but escaping is important. 
        // Better approach: write job desc to a temp file and pass the path.

        $tempJobFile = storage_path('app/temp_job_' . uniqid() . '.txt');
        file_put_contents($tempJobFile, $request->job_description);

        $process = new Process([
            $pythonPath,
            $scriptPath,
            '--resume',
            $absolutePath,
            '--job_file',
            $tempJobFile
        ], base_path(), [
            'SystemRoot' => getenv('SystemRoot'),
            'PATH' => getenv('PATH'),
            'NLTK_DATA' => storage_path('app/nltk_data'),
            'APPDATA' => getenv('APPDATA'),
            'LOCALAPPDATA' => getenv('LOCALAPPDATA'),
            'USERPROFILE' => getenv('USERPROFILE'),
            'TEMP' => getenv('TEMP'),
        ]);

        $process->setTimeout(120); // 2 minutes max

        try {
            $process->mustRun();

            $output = $process->getOutput();
            $result = json_decode($output, true);

            // Clean up temp file
            @unlink($tempJobFile);

            if (!$result) {
                return back()->with('error', 'Failed to parse analysis result. Raw output: ' . $output);
            }

            // 3. Store results
            // Extract raw text for job ad
            $jobAd = JobAd::create([
                'title' => 'Job Analysis - ' . now()->format('M d, Y H:i'), // Default title
                'raw_text' => $request->job_description,
            ]);

            AnalysisResult::create([
                'user_id' => auth()->id(), // null if guest
                'job_ad_id' => $jobAd->id,
                'match_percentage' => $result['match_percentage'],
                'llm_feedback' => $result, // Store entire result json
                'detailed_scores' => [
                    'lexicon' => $result['lexicon_match'],
                    'vector' => $result['vector_match']
                ],
                'resume_path' => $filePath,
                'job_title' => $jobAd->title,
                'status' => 'completed'
            ]);

            return view('dashboard', ['result' => $result]);

        } catch (ProcessFailedException $exception) {
            @unlink($tempJobFile);
            return back()->with('error', 'Analysis failed: ' . $exception->getMessage());
        }
    }

    public function history()
    {
        $analyses = AnalysisResult::with('jobAd')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('history', ['analyses' => $analyses]);
    }

    public function show(AnalysisResult $result)
    {
        // Ensure user owns this result
        if ($result->user_id !== auth()->id()) {
            abort(403);
        }

        // The stored `llm_feedback` is already the JSON structure we need
        // but we need to ensure the structure matches what dashboard expects.
        // Dashboard expects `result` variable.

        $data = $result->llm_feedback;

        // ensure format consistency if needed (e.g. if some older records use different keys)
        // For now, assuming direct mapping since we just laid this out.

        return view('dashboard', ['result' => $data]);
    }
}
