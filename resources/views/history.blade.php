<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Scan History') }}
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 overflow-hidden shadow-sm sm:rounded-lg">
                @if($analyses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-500 dark:text-slate-400">
                            <thead
                                class="bg-gray-50 dark:bg-slate-900/50 text-xs uppercase font-bold text-slate-700 dark:text-slate-300">
                                <tr>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Job Title</th>
                                    <th class="px-6 py-4 text-center">Match Score</th>
                                    <th class="px-6 py-4 text-center">Vector/Lexicon</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                @foreach($analyses as $analysis)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                                                        <td class="px-6 py-4">
                                                            {{ $analysis->created_at->format('M d, Y H:i') }}
                                                            <div class="text-xs text-slate-400 dark:text-slate-500">
                                                                {{ $analysis->created_at->diffForHumans() }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 font-medium text-white">
                                                            <a href="{{ route('history.show', $analysis->id) }}"
                                                                class="hover:text-blue-400 transition-colors">
                                                                {{ $analysis->job_title ?? 'Job Analysis' }}
                                                            </a>
                                                            <div class="text-xs text-slate-500 truncate max-w-xs">
                                                                {{ Str::limit($analysis->jobAd->raw_text ?? '', 50) }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            <div
                                                                class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full font-bold
                                                                                                {{ $analysis->match_percentage >= 70 ? 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-500/20' :
                                    ($analysis->match_percentage >= 40 ? 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-500/20' :
                                        'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-500/20') }}">
                                                                {{ number_format($analysis->match_percentage, 0) }}%
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            <span class="text-purple-600 dark:text-purple-400 font-semibold"
                                                                title="Semantic">{{ $analysis->detailed_scores['vector'] ?? 0 }}%</span>
                                                            <span class="text-slate-400 mx-1">/</span>
                                                            <span class="text-blue-600 dark:text-blue-400 font-semibold"
                                                                title="Keywords">{{ $analysis->detailed_scores['lexicon'] ?? 0 }}%</span>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <span
                                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                                {{ ucfirst($analysis->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center text-slate-500">
                        <p class="mb-4">No scan history found.</p>
                        <a href="{{ url('/') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Start your first
                            analysis</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>