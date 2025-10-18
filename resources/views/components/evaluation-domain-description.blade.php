{{-- Domain Description Component --}}
@props(['domainName'])

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