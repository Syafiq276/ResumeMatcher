<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Analysis Report') }}
            </h2>
            <a href="{{ url('/') }}"
                class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm font-medium shadow-lg shadow-blue-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Analysis
            </a>
        </div>
    </x-slot>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(isset($result))

                <!-- Top Row: Score & Status -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Score Card -->
                    <div
                        class="md:col-span-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 relative overflow-hidden shadow-xl">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16 blur-2xl">
                        </div>
                        <h2 class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-widest mb-4">
                            Compatibility Score</h2>

                        <div class="relative h-48 w-48 mx-auto">
                            <canvas id="gaugeChart"></canvas>
                            <div class="absolute inset-0 flex items-center justify-center flex-col">
                                <span
                                    class="text-5xl font-bold text-slate-800 dark:text-white tracking-tighter">{{ number_format($result['match_percentage'], 0) }}%</span>
                                <span
                                    class="text-xs uppercase font-bold px-2 py-1 rounded bg-slate-200 dark:bg-slate-700 mt-2 {{ $result['match_percentage'] >= 70 ? 'text-green-600 dark:text-green-400' : ($result['match_percentage'] >= 40 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                    {{ $result['eligibility_status'] ?? 'Analyzed' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Insight & Context -->
                    <div
                        class="md:col-span-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-xl flex flex-col justify-center relative overflow-hidden">
                        <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-purple-500"></div>
                        <h2
                            class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Strategic Insight
                        </h2>
                        <p class="text-lg text-slate-700 dark:text-slate-200 leading-relaxed italic">
                            "{{ $result['tnb_specific_insight'] ?? 'No specific context available.' }}"
                        </p>
                        <div class="mt-6 flex gap-4">
                            <div
                                class="text-center px-4 py-2 bg-slate-100 dark:bg-slate-700/50 rounded-lg border border-slate-200 dark:border-slate-600">
                                <div class="text-xs text-slate-500 dark:text-slate-400">Lexicon Match</div>
                                <div class="text-xl font-bold text-blue-500 dark:text-blue-400">
                                    {{ $result['lexicon_match'] ?? 0 }}%</div>
                            </div>
                            <div
                                class="text-center px-4 py-2 bg-slate-100 dark:bg-slate-700/50 rounded-lg border border-slate-200 dark:border-slate-600">
                                <div class="text-xs text-slate-500 dark:text-slate-400">Semantic Match</div>
                                <div class="text-xl font-bold text-purple-500 dark:text-purple-400">
                                    {{ $result['vector_match'] ?? 0 }}%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Skills Analysis Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Key Matches -->
                    <div
                        class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-xl">
                        <h2 class="text-green-600 dark:text-green-400 font-bold flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Strong Matches
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            @if(isset($result['key_matches']) && count($result['key_matches']) > 0)
                                @foreach($result['key_matches'] as $match)
                                    <span
                                        class="px-3 py-1 bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-500/20 rounded-full text-sm">
                                        {{ $match }}
                                    </span>
                                @endforeach
                            @else
                                <p class="text-slate-500 text-sm">No specific strong matches detected.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Missing Skills -->
                    <div
                        class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-xl">
                        <h2 class="text-red-600 dark:text-red-400 font-bold flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Missing / To Improve
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            @if(isset($result['missing_skills']) && count($result['missing_skills']) > 0)
                                @foreach($result['missing_skills'] as $skill)
                                    <span
                                        class="px-3 py-1 bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-500/20 rounded-full text-sm">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            @else
                                <p class="text-slate-500 text-sm">No critical missing skills detected.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Optimization Tips -->
                <div
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 shadow-xl">
                    <h2 class="text-slate-800 dark:text-white font-bold text-lg mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Resume Optimization Plan
                    </h2>
                    <div class="space-y-4">
                        @if(isset($result['resume_optimization_tips']))
                            @foreach($result['resume_optimization_tips'] as $index => $tip)
                                <div
                                    class="flex gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-200 dark:border-slate-600 hover:border-blue-500/50 transition-colors">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="text-slate-700 dark:text-slate-300">{{ $tip }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Integration: Smart Job Links -->
                <div
                    class="bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 shadow-xl relative overflow-hidden">
                    <div
                        class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-900/10 dark:from-blue-900/20 to-transparent">
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-slate-800 dark:text-white font-bold text-lg mb-2">Ready to Apply?</h2>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 max-w-2xl">Based on your potential match, check
                            out real-time listings on major platforms. These links are optimized for your identified
                            keywords.</p>

                        <div class="flex flex-wrap gap-4">
                            <!-- Maukerja -->
                            <a href="https://www.maukerja.my/search?q={{ urlencode(implode(' ', array_slice($result['key_matches'] ?? ['job'], 0, 2))) }}"
                                target="_blank"
                                class="flex items-center gap-3 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-transform hover:-translate-y-1 shadow-lg shadow-red-600/20">
                                Search on Maukerja
                            </a>

                            <!-- JobStreet -->
                            <a href="https://www.jobstreet.com.my/en/job-search/{{ urlencode(implode('-', array_slice($result['key_matches'] ?? ['job'], 0, 2))) }}-jobs/"
                                target="_blank"
                                class="flex items-center gap-3 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-transform hover:-translate-y-1 shadow-lg shadow-purple-600/20">
                                Search on JobStreet
                            </a>

                            <!-- LinkedIn -->
                            <a href="https://www.linkedin.com/jobs/search/?keywords={{ urlencode(implode(' ', array_slice($result['key_matches'] ?? ['job'], 0, 2))) }}"
                                target="_blank"
                                class="flex items-center gap-3 px-6 py-3 bg-blue-700 hover:bg-blue-800 text-white rounded-xl font-semibold transition-transform hover:-translate-y-1 shadow-lg shadow-blue-700/20">
                                LinkedIn Jobs
                            </a>
                        </div>
                    </div>
                </div>

            @else
                <div
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-12 rounded-2xl text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">No Analysis Found</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6">Please upload a resume and job description to get
                        started.</p>
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Start New Analysis
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Chart Script -->
    <script>
        @if(isset($result))
            const ctx = document.getElementById('gaugeChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 150, 0);
            gradient.addColorStop(0, '#3b82f6');
            gradient.addColorStop(1, '#a855f7');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Match', 'Gap'],
                    datasets: [{
                        data: [{{ $result['match_percentage'] }}, 100 - {{ $result['match_percentage'] }}],
                        backgroundColor: [gradient, '#1e293b'],
                        borderWidth: 0,
                        circumference: 240,
                        rotation: 240,
                        cutout: '85%',
                        borderRadius: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    animation: { animateScale: true, animateRotate: true }
                }
            });
        @endif
    </script>
</x-app-layout>