<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Portfolio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $student->first_name }} {{ $student->last_name }}'s Portfolio
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">Showcase your academic achievements and projects</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Student Information -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                                <h4 class="font-semibold text-lg mb-4">Student Information</h4>
                                
                                @if($student->profile_picture)
                                    <img src="{{ Storage::url($student->profile_picture) }}" 
                                         alt="Profile Picture" 
                                         class="w-24 h-24 rounded-full mx-auto mb-4">
                                @else
                                    <div class="w-24 h-24 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                                        <span class="text-gray-600 dark:text-gray-400 text-2xl">
                                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif

                                <div class="text-center mb-4">
                                    <h5 class="font-semibold">{{ $student->first_name }} {{ $student->last_name }}</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $student->email }}</p>
                                    @if($student->school_number)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">ID: {{ $student->school_number }}</p>
                                    @endif
                                </div>

                                @if($student->bio)
                                    <div>
                                        <h6 class="font-medium mb-2">About</h6>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $student->bio }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Portfolio Content -->
                        <div class="lg:col-span-2">
                            <div class="space-y-6">
                                <!-- Projects Section -->
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 rounded-lg">
                                    <h4 class="font-semibold text-lg mb-4">Projects</h4>
                                    <div class="text-center py-8">
                                        <div class="text-gray-400 mb-4">
                                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400">No projects added yet.</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Projects will be managed by your advisers.</p>
                                    </div>
                                </div>

                                <!-- Achievements Section -->
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 rounded-lg">
                                    <h4 class="font-semibold text-lg mb-4">Achievements</h4>
                                    <div class="text-center py-8">
                                        <div class="text-gray-400 mb-4">
                                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400">No achievements recorded yet.</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Keep working hard and achievements will appear here!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="mt-8 text-center">
                        <a href="{{ route('student.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700">
                            ← Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>