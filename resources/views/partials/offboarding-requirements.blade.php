{{-- Fixed offboarding clearance list (client revision: no longer typed by HR). --}}
<ol class="ofb-reqs">
    @foreach (\App\Models\OffboardingCase::REQUIRED_DOCUMENTS as $label)
        <li>
            <span>{{ $label }}</span>
            <small>{{ in_array($label, \App\Models\OffboardingCase::INTERVIEW_LABELS, true) ? 'Interview, marked done by HR' : 'Uploaded by the employee' }}</small>
        </li>
    @endforeach
</ol>
