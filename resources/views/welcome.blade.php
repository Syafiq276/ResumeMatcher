<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resume Matcher | Smart Career Aligner</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="absolute top-6 right-6 z-50">
        @if (Route::has('login'))
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-slate-300 hover:text-white font-medium transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-slate-300 hover:text-white font-medium transition-colors">Log
                        in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition-colors font-medium shadow-lg shadow-blue-500/20">Register</a>
                    @endif
                @endauth
            </div>
        @endif
    </div>

    <div class="relative w-full max-w-4xl">
        <!-- Decorational blobs -->
        <div
            class="absolute -top-20 -left-20 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob">
        </div>
        <div
            class="absolute -bottom-20 -right-20 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000">
        </div>

        <div
            class="relative bg-white/10 backdrop-blur-lg border border-white/20 p-8 md:p-12 rounded-2xl shadow-2xl w-full">

            <div class="text-center mb-10 relative">
                <div class="absolute top-0 right-0">
                    <a href="{{ route('history') }}"
                        class="text-slate-400 hover:text-white text-sm font-medium transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Scan History
                    </a>
                </div>
                <span
                    class="inline-block py-1 px-3 rounded-full bg-blue-500/20 text-blue-300 text-sm font-semibold mb-4 border border-blue-500/30">Powered
                    by Gemini 2.0 Flash</span>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 tracking-tight leading-tight">
                    Stop Guessing. <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">Start
                        Interviewing.</span>
                </h1>
                <p class="text-slate-300 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
                    Optimize your resume for <strong>Applicant Tracking Systems (ATS)</strong> in seconds. Our AI
                    analyzes your resume against job descriptions to give you the competitive edge you deserve.
                </p>
            </div>

            @auth
                <form action="{{ route('analyze') }}" method="POST" enctype="multipart/form-data" class="space-y-8"
                    id="analysisForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Upload Section -->
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-slate-300 ml-1">Resume Source</label>

                            <!-- Tabs / Toggle (Simple implementation) -->
                            <div x-data="{ mode: 'upload' }" class="space-y-4">
                                <div class="flex gap-4 mb-2">
                                    <button type="button" @click="mode = 'upload'"
                                        :class="{'bg-blue-600 text-white': mode === 'upload', 'bg-slate-700 text-slate-300': mode !== 'upload'}"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">Upload
                                        New</button>
                                    <button type="button" @click="mode = 'saved'"
                                        :class="{'bg-blue-600 text-white': mode === 'saved', 'bg-slate-700 text-slate-300': mode !== 'saved'}"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">Select
                                        Saved</button>
                                </div>

                                <!-- Upload Input -->
                                <div x-show="mode === 'upload'" class="relative group">
                                    <input type="file" name="resume" id="resume"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        :required="mode === 'upload'" />
                                    <div
                                        class="border-2 border-dashed border-slate-600 rounded-xl p-8 flex flex-col items-center justify-center text-center transition-all group-hover:border-blue-500 group-hover:bg-slate-800/50 bg-slate-800/30 h-48">
                                        <div
                                            class="w-12 h-12 bg-slate-700/50 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                </path>
                                            </svg>
                                        </div>
                                        <p class="text-slate-300 text-sm font-medium" id="file-name">Drag & drop or chosse
                                            file</p>
                                    </div>
                                </div>

                                <!-- Saved Resumes Dropdown -->
                                <div x-show="mode === 'saved'" class="space-y-2">
                                    <select name="resume_id"
                                        class="w-full bg-slate-800/30 border-2 border-slate-600 rounded-xl p-3 text-slate-200 focus:border-blue-500 focus:ring-0">
                                        <option value="" disabled selected>Select a saved resume...</option>
                                        @foreach(auth()->user()->resumes ?? [] as $resume)
                                            <option value="{{ $resume->id }}">{{ $resume->alias }}
                                                ({{ $resume->created_at->format('M d') }})</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-slate-500">Manage your resumes in the <a
                                            href="{{ route('resumes.index') }}"
                                            class="text-blue-400 hover:underline">Dashboard</a>.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Job Description Section -->
                        <div class="space-y-2">
                            <label for="job_description" class="block text-sm font-medium text-slate-300 ml-1">Job
                                Description</label>
                            <textarea name="job_description" id="job_description"
                                class="w-full h-64 bg-slate-800/30 border-2 border-slate-600 rounded-xl p-4 text-slate-200 placeholder-slate-500 focus:border-blue-500 focus:ring-0 transition-colors resize-none"
                                placeholder="Paste the full job advertisement here..." required></textarea>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" id="submitBtn"
                            class="w-full group relative flex justify-center py-4 px-4 border border-transparent rounded-xl text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-lg hover:shadow-blue-500/25">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-blue-300 group-hover:text-white transition-colors"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span id="btnText">Analyze Compatibility</span>
                            <div id="loadingSpinner" class="hidden absolute right-6 flex items-center">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </form>
            @else
                <div
                    class="bg-white/5 backdrop-blur-lg border border-white/10 p-8 md:p-12 rounded-2xl shadow-2xl text-center py-16">
                    <div class="w-20 h-20 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4">Ready to Land Your Dream Job?</h2>
                    <p class="text-slate-300 text-lg mb-8 max-w-2xl mx-auto">
                        Join thousands of job seekers who are optimizing their applications with AI. Get <strong>detailed
                            match reports</strong>, <strong>keyword gap analysis</strong>, and <strong>personalized
                            improvement tips</strong>—all for free.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('register') }}"
                            class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl shadow-lg hover:shadow-blue-500/25 hover:scale-105 transition-transform flex items-center justify-center gap-2">
                            <span>Get Started for Free</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                            class="px-8 py-4 bg-slate-700/50 border border-slate-600 text-white font-bold rounded-xl hover:bg-slate-700 transition-colors flex items-center justify-center">
                            Log In
                        </a>
                    </div>
                </div>
            @endauth

            @if(session('error'))
                <div
                    class="mt-6 p-4 bg-red-500/10 border border-red-500/50 text-red-200 rounded-xl flex items-center animate-pulse">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="text-center mt-6 text-slate-500 text-sm">
            &copy; {{ date('Y') }} Resume Matcher. Built with Laravel 12 & Python.
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('resume');
        const fileNameDisplay = document.getElementById('file-name');
        const form = document.getElementById('analysisForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const loadingSpinner = document.getElementById('loadingSpinner');

        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
                fileNameDisplay.classList.add('text-blue-400');
            }
        });

        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            btnText.textContent = 'Processing...';
            loadingSpinner.classList.remove('hidden');
        });
    </script>
</body>

</html>