<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Top Row: Student Information and Final Results Side by Side --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            {{-- Student Information Section --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Student Information
                    </h3>
                </div>
                <div class="fi-section-content p-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Student Name</label>
                            <p class="text-sm text-gray-900 dark:text-white font-semibold">{{ $record->student->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Organization</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $record->organization->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Academic Year</label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                @if($record->organization->year)
                                    {{ $record->organization->year }}-{{ $record->organization->year + 1 }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                @php
                                    $pivot = $record->student->organizations->where('id', $record->organization_id)->first()?->pivot;
                                @endphp
                                {{ $pivot?->position ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Final Results Section --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Final Results
                    </h3>
                </div>
                <div class="fi-section-content p-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Final Score</label>
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                {{ $record->final_score ? number_format($record->final_score, 3) : 'Pending' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Rank</label>
                            <p class="text-lg font-bold">
                                @switch($record->rank)
                                    @case('gold')
                                        <span class="text-yellow-600">🥇 Gold</span>
                                        @break
                                    @case('silver')
                                        <span class="text-gray-600">🥈 Silver</span>
                                        @break
                                    @case('bronze')
                                        <span class="text-orange-600">🥉 Bronze</span>
                                        @break
                                    @case('none')
                                        <span class="text-red-600">❌ None</span>
                                        @break
                                    @default
                                        <span class="text-gray-600">⏳ Pending</span>
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <p class="text-sm">
                                @switch($record->status)
                                    @case('finalized')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Finalized
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Unknown
                                        </span>
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Evaluation Forms --}}
        @foreach(['adviser', 'peer', 'self'] as $evaluatorType)
            @php
                $evaluation = $evaluations->get($evaluatorType);
                $weight = match($evaluatorType) {
                    'adviser' => '65%',
                    'peer' => '25%',
                    'self' => '10%'
                };
                $icon = match($evaluatorType) {
                    'adviser' => '👨‍🏫',
                    'peer' => '👥',
                    'self' => '🧑‍🎓'
                };
                $title = ucfirst($evaluatorType) . ' Evaluation (' . $weight . ' Weight)';
            @endphp
            
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $icon }} {{ $title }}
                    </h3>
                    @if($evaluation)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Score: {{ number_format($evaluation->evaluator_score, 3) }}/3.0
                        </p>
                    @endif
                </div>
                <div class="fi-section-content p-6">
                    @if($evaluation && $evaluation->answers)
                        @php
                            $questionsForEvaluator = \App\Models\Evaluation::getQuestionsForEvaluator($evaluatorType);
                        @endphp
                        
                        <div class="space-y-4">
                            @foreach($questionsForEvaluator as $questionKey => $questionText)
                                @php
                                    $score = $evaluation->answers[$questionKey] ?? null;
                                    $scoreText = is_numeric($score) ? $score : "Not answered";
                                @endphp
                                
                                <div class="border-l-4 border-blue-400 pl-4 py-2 bg-gray-50 dark:bg-gray-800">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        {{ $questionText }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>Score:</strong> 
                                        <span class="@if($score >= 2.5) text-green-600 @elseif($score >= 1.5) text-yellow-600 @else text-red-600 @endif">
                                            {{ $scoreText }}
                                        </span>
                                    </p>
                                </div>
                            @endforeach
                            
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <strong>Completed:</strong> {{ $evaluation->created_at->format('M j, Y g:i A') }}
                                    @if($evaluation->updated_at != $evaluation->created_at)
                                        | <strong>Last updated:</strong> {{ $evaluation->updated_at->format('M j, Y g:i A') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-4xl mb-4">⚠️</div>
                            <p class="text-gray-600 dark:text-gray-400">
                                <strong>Not yet evaluated by {{ $evaluatorType }}</strong>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                                This evaluation form has not been completed yet.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Score Summary --}}
        @if($record->breakdown)
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        📊 Score Calculation Summary
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Detailed breakdown of weighted scores
                    </p>
                </div>
                <div class="fi-section-content p-6">
                    <div class="space-y-4">
                        @foreach($record->breakdown as $evaluatorType => $data)
                            @php
                                $percentage = round($data['weight'] * 100) . '%';
                                $icon = match($evaluatorType) {
                                    'adviser' => '👨‍🏫',
                                    'peer' => '👥',
                                    'self' => '🧑‍🎓',
                                    default => '📋'
                                };
                            @endphp
                            
                            <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                        {{ $icon }} {{ ucfirst($evaluatorType) }} Evaluation
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Raw Score: {{ $data['score'] }}/3.0 | Weight: {{ $percentage }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-lg text-blue-600 dark:text-blue-400">
                                        {{ number_format($data['weighted_score'], 3) }}
                                    </p>
                                    <p class="text-sm text-gray-500">Weighted Score</p>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white">🎯 Final Score</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total weighted average</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-2xl text-blue-600 dark:text-blue-400">
                                        {{ number_format(array_sum(array_column($record->breakdown, 'weighted_score')), 3) }}
                                    </p>
                                    <p class="text-sm text-gray-500">out of 3.0</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3">Ranking Criteria:</h5>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div class="text-center p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded">
                                    <div class="text-yellow-600 font-bold">🥇 Gold</div>
                                    <div class="text-gray-600 dark:text-gray-400">2.41+</div>
                                </div>
                                <div class="text-center p-2 bg-gray-100 dark:bg-gray-700 rounded">
                                    <div class="text-gray-600 font-bold">🥈 Silver</div>
                                    <div class="text-gray-600 dark:text-gray-400">1.81-2.40</div>
                                </div>
                                <div class="text-center p-2 bg-orange-100 dark:bg-orange-900/30 rounded">
                                    <div class="text-orange-600 font-bold">🥉 Bronze</div>
                                    <div class="text-gray-600 dark:text-gray-400">1.21-1.80</div>
                                </div>
                                <div class="text-center p-2 bg-red-100 dark:bg-red-900/30 rounded">
                                    <div class="text-red-600 font-bold">❌ None</div>
                                    <div class="text-gray-600 dark:text-gray-400">&lt;1.21</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-content p-6">
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-4">⚠️</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            Evaluation Incomplete
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            This student requires evaluation from all three sources:
                        </p>
                        <div class="space-y-2 text-sm text-gray-500 dark:text-gray-500">
                            <p>👨‍🏫 <strong>Adviser Evaluation</strong> (65% weight)</p>
                            <p>👥 <strong>Peer Evaluation</strong> (25% weight)</p>
                            <p>🧑‍🎓 <strong>Self Evaluation</strong> (10% weight)</p>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-4">
                            Once all evaluations are completed, the final ranking will be calculated automatically.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>