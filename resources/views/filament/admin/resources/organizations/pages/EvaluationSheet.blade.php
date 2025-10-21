<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                {{ ucfirst($this->type) }} Evaluation - {{ $this->student->name }}
            </h2>
            <p class="text-gray-600">Organization: {{ $this->organization->name }}</p>
        </div>

        {{-- Evaluation Form --}}
        <form wire:submit="save">
            @php
                $questions = App\Models\Evaluation::getQuestionsForEvaluator($this->type);
                $grouped = $this->groupQuestions($questions);
            @endphp

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
                            @foreach($grouped as $domainName => $strands)
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
                                                        @if($this->isLocked) disabled @endif
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
            @if(!$this->isLocked)
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="filament-button filament-button--success">
                        Submit Evaluation
                    </button>
                </div>
            @endif
        </form>
    </div>
</x-filament-panels::page>