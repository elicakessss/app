<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                Self Evaluation - {{ $organization->name ?? 'N/A' }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                <p><strong>Organization:</strong> {{ $organization->name ?? 'N/A' }}</p>
                <p><strong>Department:</strong> {{ $organization->department->name ?? 'N/A' }}</p>
                <p><strong>Academic Year:</strong> {{ $organization->year ?? 'N/A' }}</p>
            </div>
            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Instructions:</strong> Please evaluate yourself honestly based on your leadership performance in this organization. Your responses will contribute to your final leadership ranking.
                </p>
            </div>
        </div>

        {{-- Evaluation Form --}}
        <form wire:submit="save">
            {{-- Comprehensive Evaluation Table --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                    {{-- Table Header --}}
                    <thead>
                        <tr>
                            <th class="border border-gray-400 bg-gray-100 px-4 py-3 text-left font-bold text-lg">
                                Performance Indicators
                            </th>
                            <th class="border border-gray-400 bg-gray-100 px-3 py-3 text-center font-bold w-16">
                                3 (E)
                            </th>
                            <th class="border border-gray-400 bg-gray-100 px-3 py-3 text-center font-bold w-16">
                                2 (M)
                            </th>
                            <th class="border border-gray-400 bg-gray-100 px-3 py-3 text-center font-bold w-16">
                                1 (S)
                            </th>
                            <th class="border border-gray-400 bg-gray-100 px-3 py-3 text-center font-bold w-16">
                                0 (X)
                            </th>
                        </tr>
                    </thead>
                    
                    {{-- Table Body --}}
                    <tbody>
                        @php
                            $groupedQuestions = $this->groupQuestions(\App\Models\Evaluation::getSelfQuestionsForStudents());
                        @endphp
                        
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
                                    <x-evaluation-domain-description :domain-name="$domainName" />
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
                                        @for($score = 3; $score >= 0; $score--)
                                            <td class="border border-gray-400 px-3 py-3 text-center">
                                                <input 
                                                    type="radio" 
                                                    name="{{ $questionKey }}" 
                                                    value="{{ $score }}"
                                                    wire:model="data.{{ $questionKey }}"
                                                    class="w-4 h-4 text-blue-600"
                                                    required
                                                >
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </form>
    </div>
</x-filament-panels::page>