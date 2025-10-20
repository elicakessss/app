{{-- Custom two-column InfoList layout for Organization view --}}
<div class="flex flex-col md:flex-row gap-6">
    <div class="bg-white rounded-lg shadow p-6 flex-1">
        <h2 class="text-lg font-bold mb-4">Organization Details</h2>
        @foreach ($organizationDetails as $entry)
            {!! $entry->toHtml() !!}
        @endforeach
    </div>
    <div class="bg-white rounded-lg shadow p-6 flex-1">
        <h2 class="text-lg font-bold mb-4">Peer Evaluators</h2>
        {!! $peerEvaluators->toHtml() !!}
    </div>
</div>