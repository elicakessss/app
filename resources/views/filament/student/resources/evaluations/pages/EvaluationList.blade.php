<x-filament-panels::page layout="top">
	@if($tasks->isEmpty())
		<x-filament::card>
			<div class="text-center py-12">
				<div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
					<x-heroicon-o-clipboard-document-check class="h-6 w-6 text-gray-400" />
				</div>
				<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No evaluations</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You don't have any evaluation tasks assigned yet.</p>
			</div>
		</x-filament::card>
	@else
		<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			@foreach($tasks as $task)
				<x-filament::card>
					<!-- Task Type Badge -->
					<div class="mb-4">
						<x-filament::badge 
							:color="$task['task_type'] === 'Self-Evaluation' ? 'info' : 'warning'"
							:icon="$task['task_type'] === 'Self-Evaluation' ? 'heroicon-s-user' : 'heroicon-s-users'">
							{{ $task['task_type'] }}
						</x-filament::badge>
					</div>

					<!-- Organization Info -->
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
						{{ $task['organization_name'] }}
					</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
						{{ $task['department_name'] }}
					</p>

					<!-- Target Student -->
					<div class="mb-4">
						<p class="text-sm text-gray-700 dark:text-gray-300">
							<span class="font-medium">Target:</span> {{ $task['target_name'] }}
						</p>
					</div>

					<!-- Status Badge -->
					<div class="mb-4">
						<x-filament::badge 
							:color="$task['status'] === 'Completed' ? 'success' : 'warning'"
							:icon="$task['status'] === 'Completed' ? 'heroicon-s-check-circle' : 'heroicon-s-clock'">
							{{ $task['status'] }}
						</x-filament::badge>
					</div>

					<!-- Action Button -->
					<div class="mt-4">
						@if($task['task_type'] === 'Self-Evaluation')
							<x-filament::button 
								tag="a"
								:href="route('filament.student.resources.evaluations.self-evaluate', ['evaluation' => $task['evaluation_id']])"
								size="sm"
								:icon="$task['status'] === 'Completed' ? 'heroicon-s-eye' : 'heroicon-s-pencil-square'">
								@if($task['status'] === 'Completed')
									View Evaluation
								@else
									Start Evaluation
								@endif
							</x-filament::button>
						@else
							<x-filament::button 
								tag="a"
								:href="route('filament.student.resources.evaluations.peer-evaluate', ['evaluation' => $task['evaluation_id'], 'student' => $task['target_id']])"
								size="sm"
								:icon="$task['status'] === 'Completed' ? 'heroicon-s-eye' : 'heroicon-s-pencil-square'">
								@if($task['status'] === 'Completed')
									View Evaluation
								@else
									Start Evaluation
								@endif
							</x-filament::button>
						@endif
					</div>
				</x-filament::card>
			@endforeach
		</div>
	@endif
</x-filament-panels::page>