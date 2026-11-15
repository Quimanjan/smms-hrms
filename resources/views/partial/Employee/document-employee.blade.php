{{--
    Employee Documents: the school's onboarding requirements.
    Upload a file for each one; HR approves or rejects it. A rejected file is
    removed so it can be uploaded again. There are no deadlines.
--}}
@php($pct = $progress['required'] ? round(100 * $progress['approved'] / $progress['required']) : 0)

<div class="rq">
    @if (session('success'))
        <p class="rq-flash" role="status">{{ session('success') }}</p>
    @endif
    @if ($errors->any())
        <p class="rq-flash rq-flash--error" role="alert">{{ $errors->first() }}</p>
    @endif

    @unless ($employee)
        <p class="rq-empty">Your account is not linked to an employee record yet. Please contact HR.</p>
    @else
        <section class="rq-me" aria-label="Your onboarding progress">
            <div>
                <h3>Your onboarding requirements</h3>
                <p>{{ $progress['approved'] }} of {{ $progress['required'] }} required documents approved{{ $progress['waiting'] ? ', '.$progress['waiting'].' waiting for HR' : '' }}.</p>
            </div>
            <div class="rq-bar rq-bar--lg" role="img" aria-label="{{ $pct }} percent complete"><span style="width: {{ $pct }}%"></span></div>
            <p class="rq-note">Upload a PDF, Word file, or a photo (JPG or PNG), up to 5 MB each. If HR rejects a file, it is removed and you can upload a new one.</p>
        </section>

        <ol class="rq-list rq-list--mine">
            @foreach ($requirements as $r)
                <li class="rq-mine rq-mine--{{ $r['status'] }}">
                    <span class="rq-num">{{ $r['number'] }}</span>
                    <div class="rq-name">
                        <span>{{ $r['name'] }}</span>
                        @if ($r['optional'] && ! str_contains(strtolower($r['name']), 'if '))<small>If applicable</small>@endif
                        @if ($r['file'])
                            <a href="{{ $r['url'] }}" target="_blank" rel="noopener" class="rq-file">{{ $r['file'] }} ({{ $r['size'] }}, {{ $r['uploaded'] }})</a>
                        @endif
                    </div>
                    <div class="rq-mine-act">
                        @switch($r['status'])
                            @case('approved') <span class="rq-chip rq-chip--ok">Approved</span> @break
                            @case('pending') <span class="rq-chip rq-chip--wait">Waiting for HR</span> @break
                            @default <span class="rq-chip">Not submitted</span>
                        @endswitch

                        @if ($r['can_upload'])
                            <form method="POST" action="{{ route('employee.documents.store') }}" enctype="multipart/form-data" class="rq-upload">
                                @csrf
                                <input type="hidden" name="submission_box_id" value="{{ $r['id'] }}">
                                <label class="rq-btn rq-btn--up">
                                    <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required class="sr-only" onchange="this.form.submit()">
                                    {{ $r['status'] === 'pending' ? 'Replace file' : 'Upload' }}
                                </label>
                            </form>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    @endunless
</div>
