<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ ucfirst($this->type) }} Evaluation - {{ $this->student->name }}</h2>
            <p class="text-gray-600">Organization: {{ $this->organization->name }}</p>
        </div>

        <!-- Evaluation Form -->
        <form wire:submit="save">
            @php
                $questions = App\Models\Evaluation::getQuestionsForEvaluator($this->type);
                $grouped = $this->groupQuestions($questions);
            @endphp

            <!-- Comprehensive Evaluation Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <!-- Table Header -->
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
                        
                        <!-- Table Body -->
                        <tbody>
                            @foreach($grouped as $domainName => $strands)
                                @if(!$loop->first)
                                    <!-- Spacing between domains -->
                                    <tr>
                                        <td colspan="5" style="height: 20px; background: transparent; border: none;"></td>
                                    </tr>
                                @endif
                                
                                <!-- Domain Header Row -->
                                <tr>
                                    <td colspan="5" class="border border-gray-400 px-4 py-4">
                                        <h4 style="font-size: 1.25rem; font-weight: 700; color: #064e3b; margin: 0; line-height: 1.2;">
                                            {{ $domainName }}
                                        </h4>
                                    </td>
                                </tr>
                                
                                <!-- Domain Description Row -->
                                <tr>
                                    <td colspan="5" class="border border-gray-400 px-4 py-3">
                                        <p style="font-size: 0.875rem; color: #065f46; font-style: italic; margin: 0; line-height: 1.4;">
                                            @if($domainName == 'Domain 1: Paulinian Leadership as Social Responsibility')
                                                This focuses on the account that Paulinian Leaders demonstrate good leadership in the activities of the organization, of the university, and of their respective community.
                                            @elseif($domainName == 'Domain 2: Paulinian Leadership as a Life of Service')
                                                This gears towards the fulfillment of the Paulinian Leaders' active and utmost involvement in the organization, management, and evaluation of the activities of the organization, university, and community.
                                            @elseif($domainName == 'Domain 3: Paulinian Leader as Leading by Example (Discipline/Decorum)')
                                                This refers on how the Paulinian Leaders conform to Paulinian norms and conduct.
                                            @elseif($domainName == 'Length of Service')
                                                Paulinian Leader had served the Department/University
                                            @endif
                                        </p>
                                    </td>
                                </tr>

                                @foreach($strands as $strandName => $strandQuestions)
                                    <!-- Strand Header Row -->
                                    <tr>
                                        <td colspan="5" class="border border-gray-400 px-4 py-3">
                                            <h6 style="font-size: 1rem; font-weight: 600; color: #064e3b; margin: 0; line-height: 1.3;">
                                                {{ $strandName }}
                                            </h6>
                                        </td>
                                    </tr>
                                    
                                    <!-- Questions for this Strand -->
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

                <!-- Submit Button -->
                <div class="p-6 bg-gray-50 border-t">
                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-8 rounded-lg transition duration-200"
                        >
                            Save Evaluation
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-filament-panels::page>
