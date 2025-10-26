<x-filament-panels::page layout="top">
	<style>
			.evaluation-table {
				width: 100%;
				border-collapse: collapse;
				background: #fff;
				border-radius: 1rem;
				overflow: hidden;
				box-shadow: 0 4px 16px rgba(0,0,0,0.08);
				border: 2px solid #878787ff;
			}
		.evaluation-table th, .evaluation-table td {
			padding: 1rem;
			text-align: left;
			border-bottom: 1px solid #e5e7eb;
		}
		.evaluation-table th {
			background: #f3f4f6;
			font-weight: 600;
		}
		.evaluation-avatar {
			width: 64px;
			height: 64px;
			border-radius: 50%;
			background: #f3f3f3;
			object-fit: cover;
			display: block;
		}
		.evaluation-table tr:last-child td {
			border-bottom: none;
		}
	</style>
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
			<div style="overflow-x:auto;">
				<table class="evaluation-table">
					<thead>
						<tr>
							<th>Photo</th>
							<th>Evaluatee</th>
							<th>Organization</th>
							<th>Type</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($tasks as $task)
						<tr>
							<td>
								<img src="{{ $task['avatar_url'] ?? 'https://via.placeholder.com/100?text=Photo' }}" alt="Student Photo" class="evaluation-avatar">
							</td>
							<td>
								<span class="font-semibold text-gray-900 dark:text-white">{{ $task['target_name'] }}</span>
							</td>
							<td>
								<span class="text-gray-500 dark:text-gray-400">{{ $task['organization_name'] }}</span>
							</td>
							<td>
								<x-filament::badge 
									:color="$task['task_type'] === 'Self-Evaluation' ? 'info' : 'warning'"
									:icon="$task['task_type'] === 'Self-Evaluation' ? 'heroicon-s-user' : 'heroicon-s-users'">
									{{ $task['task_type'] }}
								</x-filament::badge>
							</td>
							<td>
								<x-filament::badge 
									:color="$task['status'] === 'Completed' ? 'success' : 'warning'"
									:icon="$task['status'] === 'Completed' ? 'heroicon-s-check-circle' : 'heroicon-s-clock'">
									{{ $task['status'] }}
								</x-filament::badge>
							</td>
							<td>
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
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</x-filament-panels::page>