<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Resumes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Upload New Resume -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Upload New Resume') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Add a new version of your resume (PDF or DOCX).') }}
                    </p>
                </header>

                <form method="post" action="{{ route('resumes.store') }}" enctype="multipart/form-data"
                    class="mt-6 space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="alias" :value="__('Resume Alias (Optional)')" />
                        <x-text-input id="alias" name="alias" type="text" class="mt-1 block w-full"
                            placeholder="e.g. Frontend Developer Resume" />
                    </div>

                    <div>
                        <x-input-label for="resume" :value="__('Resume File')" />
                        <input id="resume" name="resume" type="file"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            required>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Upload') }}</x-primary-button>
                        @if (session('success'))
                            <p x-data="{ show: true }" x-show="show" x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600 dark:text-gray-400">{{ session('success') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Resume List -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Stored Resumes') }}
                    </h2>
                </header>

                <div class="mt-6 overflow-x-auto">
                    @if($resumes->count() > 0)
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Alias</th>
                                    <th scope="col" class="px-6 py-3">Uploaded</th>
                                    <th scope="col" class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resumes as $resume)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ $resume->alias }}
                                            @if($resume->is_default)
                                                <span
                                                    class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Default</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $resume->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <form method="POST" action="{{ route('resumes.destroy', $resume) }}"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="font-medium text-red-600 dark:text-red-500 hover:underline"
                                                    onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No resumes uploaded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>