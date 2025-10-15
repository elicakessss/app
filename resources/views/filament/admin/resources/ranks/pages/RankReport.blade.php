<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                Rank Report - {{ $record->student->name ?? 'N/A' }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                <p><strong>Organization:</strong> {{ $record->organization->name ?? 'N/A' }}</p>
                <p><strong>Final Score:</strong> {{ number_format($record->final_score, 3) }}</p>
                <p><strong>Rank:</strong> #{{ $record->rank ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Evaluation Results Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    {{-- Table Header --}}
                    <thead>
                        <tr>
                            <th class="border border-gray-400 bg-gray-100 px-4 py-3 text-left font-bold text-lg">
                                Performance Indicators
                            </th>
                            <th class="border border-gray-400 bg-gray-100 px-3 py-3 text-center font-bold w-20">
                                Adviser
                            </th>
                            <th class="border border-gray-400 bg-gray-100 px-3 py-3 text-center font-bold w-20">
                                Peer
                            </th>
                            <th class="border border-gray-400 bg-gray-100 px-3 py-3 text-center font-bold w-20">
                                Self
                            </th>
                            <th class="border border-gray-400 bg-gray-100 px-3 py-3 text-center font-bold w-20">
                                Average
                            </th>
                        </tr>
                    </thead>
                    
                    {{-- Table Body --}}
                    <tbody>
                        @foreach($groupedQuestions as $domainName => $strands)
                            @if(!$loop->first)
                                {{-- Spacing between domains --}}
                                <tr>
                                    <td colspan="5" class="h-5 border-none bg-transparent"></td>
                                </tr>
                            @endif
                            
                            {{-- Domain Header Row --}}
                            <tr>
                                <td colspan="5" class="border border-gray-400 px-4 py-4">
                                    <h4 class="text-xl font-bold text-emerald-800 m-0 leading-tight">
                                        {{ $domainName }}
                                    </h4>
                                </td>
                            </tr>
                            
                            {{-- Domain Description Row --}}
                            <tr>
                                <td colspan="5" class="border border-gray-400 px-4 py-3">
                                    <p class="text-sm text-emerald-700 italic m-0 leading-relaxed">
                                        @switch($domainName)
                                            @case('Domain 1: Paulinian Leadership as Social Responsibility')
                                                This focuses on the account that Paulinian Leaders demonstrate good leadership in the activities of the organization, of the university, and of their respective community.
                                                @break
                                            @case('Domain 2: Paulinian Leadership as a Life of Service')
                                                This gears towards the fulfillment of the Paulinian Leaders' active and utmost involvement in the organization, management, and evaluation of the activities of the organization, university, and community.
                                                @break
                                            @case('Domain 3: Paulinian Leader as Leading by Example (Discipline/Decorum)')
                                                This refers on how the Paulinian Leaders conform to Paulinian norms and conduct.
                                                @break
                                            @case('Length of Service')
                                                Paulinian Leader had served the Department/University
                                                @break
                                        @endswitch
                                    </p>
                                </td>
                            </tr>

                            @foreach($strands as $strandName => $strandQuestions)
                                {{-- Strand Header Row --}}
                                <tr>
                                    <td colspan="5" class="border border-gray-400 px-4 py-3">
                                        <h6 class="text-base font-semibold text-emerald-800 m-0 leading-tight">
                                            {{ $strandName }}
                                        </h6>
                                    </td>
                                </tr>
                                
                                {{-- Questions for this Strand --}}
                                @foreach($strandQuestions as $questionKey => $questionText)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-400 px-4 py-3 text-sm">
                                            {{ $questionText }}
                                        </td>
                                        
                                        {{-- Adviser Score --}}
                                        <td class="border border-gray-400 px-3 py-3 text-center">
                                            @php
                                                $adviserScore = $evaluations->get('adviser')?->answers[$questionKey] ?? null;
                                            @endphp
                                            @if($adviserScore !== null)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                                    @if($adviserScore == 3) bg-green-100 text-green-800
                                                    @elseif($adviserScore == 2) bg-blue-100 text-blue-800
                                                    @elseif($adviserScore == 1) bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif
                                                    text-sm font-semibold">
                                                    {{ $adviserScore }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Peer Score --}}
                                        <td class="border border-gray-400 px-3 py-3 text-center">
                                            @php
                                                $peerScore = $evaluations->get('peer')?->answers[$questionKey] ?? null;
                                            @endphp
                                            @if($peerScore !== null)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                                    @if($peerScore == 3) bg-green-100 text-green-800
                                                    @elseif($peerScore == 2) bg-blue-100 text-blue-800
                                                    @elseif($peerScore == 1) bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif
                                                    text-sm font-semibold">
                                                    {{ $peerScore }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Self Score --}}
                                        <td class="border border-gray-400 px-3 py-3 text-center">
                                            @php
                                                $selfScore = $evaluations->get('self')?->answers[$questionKey] ?? null;
                                            @endphp
                                            @if($selfScore !== null)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                                    @if($selfScore == 3) bg-green-100 text-green-800
                                                    @elseif($selfScore == 2) bg-blue-100 text-blue-800
                                                    @elseif($selfScore == 1) bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif
                                                    text-sm font-semibold">
                                                    {{ $selfScore }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Average Score --}}
                                        <td class="border border-gray-400 px-3 py-3 text-center">
                                            @php
                                                $scores = array_filter([$adviserScore, $peerScore, $selfScore], fn($score) => $score !== null);
                                                $average = count($scores) > 0 ? array_sum($scores) / count($scores) : null;
                                            @endphp
                                            @if($average !== null)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                                    @if($average >= 2.5) bg-green-100 text-green-800
                                                    @elseif($average >= 1.5) bg-blue-100 text-blue-800
                                                    @elseif($average >= 0.5) bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif
                                                    text-sm font-semibold">
                                                    {{ number_format($average, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Summary Section --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Evaluation Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600">Adviser Score</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ $evaluations->get('adviser')?->evaluator_score ? number_format($evaluations->get('adviser')->evaluator_score, 3) : 'N/A' }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Peer Score</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $evaluations->get('peer')?->evaluator_score ? number_format($evaluations->get('peer')->evaluator_score, 3) : 'N/A' }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Self Score</p>
                    <p class="text-2xl font-bold text-purple-600">
                        {{ $evaluations->get('self')?->evaluator_score ? number_format($evaluations->get('self')->evaluator_score, 3) : 'N/A' }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Final Score</p>
                    <p class="text-2xl font-bold text-red-600">
                        {{ number_format($record->final_score, 3) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>