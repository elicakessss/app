<x-filament-panels::page>
    <div class="space-y-6">
        @if(empty($this->getViewData()['tasks']))
            <div class="text-center py-12">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                    <x-heroicon-o-clipboard-document-list class="h-6 w-6 text-gray-400" />
                </div>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No evaluations</h3>
                <p class="mt-1 text-sm text-gray-500">You have no pending evaluation tasks at this time.</p>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($this->getViewData()['tasks'] as $task)
                    <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($task['type'] === 'self')
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                                            <x-heroicon-o-user class="h-6 w-6 text-blue-600" />
                                        </div>
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                                            <x-heroicon-o-user-group class="h-6 w-6 text-green-600" />
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            {{ $task['organization'] }}
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ $task['title'] }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-gray-500">
                                    <x-heroicon-o-building-library class="flex-shrink-0 mr-1.5 h-4 w-4" />
                                    {{ $task['department'] }}
                                </div>
                                <div class="flex items-center text-sm text-gray-500 mt-1">
                                    <x-heroicon-o-user class="flex-shrink-0 mr-1.5 h-4 w-4" />
                                    Evaluating: {{ $task['target'] }}
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                @if($task['completed'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                        Pending
                                    </span>
                                @endif

                                <a href="{{ $task['url'] }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ $task['completed'] ? 'Review' : 'Evaluate' }}
                                    <x-heroicon-o-arrow-right class="ml-1 w-3 h-3" />
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>