<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Welcome, {{ $student->first_name }} {{ $student->last_name }}!
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">Student ID: {{ $student->school_number ?? 'Not assigned' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Student Info Card -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                            <h4 class="font-semibold text-lg mb-3 text-blue-900 dark:text-blue-100">Your Profile</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <strong>Email:</strong> {{ $student->email }}
                            </p>
                            @if($student->bio)
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>Bio:</strong> {{ Str::limit($student->bio, 100) }}
                                </p>
                            @endif
                        </div>

                        <!-- Portfolio Card -->
                        <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg">
                            <h4 class="font-semibold text-lg mb-3 text-green-900 dark:text-green-100">Portfolio</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                View and manage your academic portfolio
                            </p>
                            <a href="{{ route('student.portfolio') }}" 
                               class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                View Portfolio
                            </a>
                        </div>

                        <!-- Quick Actions Card -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-lg">
                            <h4 class="font-semibold text-lg mb-3 text-purple-900 dark:text-purple-100">Quick Actions</h4>
                            <div class="space-y-2">
                                <form method="POST" action="{{ route('student.logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full text-left px-3 py-2 text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="mt-8">
                        <h4 class="font-semibold text-lg mb-4">Recent Activity</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-gray-600 dark:text-gray-400 text-center">
                                No recent activity to display.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>