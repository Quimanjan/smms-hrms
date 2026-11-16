
    <x-slot name="title">Applicant Dashboard</x-slot>

    @php
        // Data is fetched straight from the database
        $user        = auth()->user();
        $application = \App\Models\Application::where('user_id', $user->id)->latest()->first();

        $status = $application->status ?? null; // null | pending | qualified | rejected

        // Status badge / banner look
        $statusUi = match ($status) {
            'qualified', 'approved' => ['label' => 'Qualified', 'icon' => 'fa-circle-check', 'badge' => 'bg-green-100 text-green-800', 'box' => 'border-[#28a745] from-[#d4edda] to-[#c3e6cb] text-[#155724]', 'msg' => 'Congratulations! HR will contact you with the next steps.'],
            'hr_approved' => ['label' => 'Pending Review', 'icon' => 'fa-hourglass-half', 'badge' => 'bg-yellow-100 text-yellow-800', 'box' => 'border-[#ffc107] from-[#fff3cd] to-[#ffeaa7] text-[#856404]', 'msg' => 'Your application is awaiting final review by Admin.'],
            'hr_rejected' => ['label' => 'Not Qualified', 'icon' => 'fa-circle-xmark', 'badge' => 'bg-red-100 text-red-800', 'box' => 'border-[#b22222] from-[#f8d7da] to-[#f5c6cb] text-[#721c24]', 'msg' => 'Thank you for applying. Unfortunately, your application was not successful.'],
            'rejected' => ['label' => 'Not Qualified',   'icon' => 'fa-circle-xmark',  'badge' => 'bg-red-100 text-red-800',       'box' => 'border-[#b22222] from-[#f8d7da] to-[#f5c6cb] text-[#721c24]', 'msg' => 'Thank you for applying. Unfortunately, your application was not successful.'],
            'pending', 'scheduled' => $application?->interview_date && ! $application?->interview_completed_at
                ? [
                    'label' => 'Interview Scheduled',
                    'icon' => 'fa-calendar-check',
                    'badge' => 'bg-yellow-100 text-yellow-800',
                    'box' => 'border-[#ffc107] from-[#fff3cd] to-[#ffeaa7] text-[#856404]',
                    'msg' => 'Your interview is scheduled for '.$application->interview_date->format('F j, Y')
                        .' at '.($application->interview_time ?: 'To be announced')
                        .' at '.($application->interview_location ?: 'St. Mary Magdalene School').'.',
                ]
                : [
                    'label' => 'Pending Review',
                    'icon' => 'fa-hourglass-half',
                    'badge' => 'bg-yellow-100 text-yellow-800',
                    'box' => 'border-[#ffc107] from-[#fff3cd] to-[#ffeaa7] text-[#856404]',
                    'msg' => $application?->interview_completed_at
                        ? 'Your interview is complete. HR will review your application first, followed by Admin.'
                        : 'Your application is awaiting interview and HR review.',
                ],
            default     => ['label' => 'Not Submitted',   'icon' => 'fa-file-circle-plus', 'badge' => 'bg-gray-100 text-gray-700',  'box' => 'border-gray-300 from-gray-50 to-gray-100 text-gray-700',      'msg' => 'You have not submitted an application yet.'],
        };

        // Progress timeline: done = green, current = red, upcoming = gray
        $submitted   = (bool) $application;
        $hasInterview = $submitted && $application->interview_date;
        $interviewCompleted = $submitted && $application->interview_completed_at;
        $decided     = in_array($status, ['qualified', 'approved', 'hr_rejected', 'rejected'], true);
        $rejected    = in_array($status, ['hr_rejected', 'rejected'], true);

        $timeline = [
            ['label' => 'Application Submitted', 'desc' => $submitted ? 'Submitted ' . $application->created_at->format('M j, Y') : 'Fill out the application form',      'done' => $submitted],
            ['label' => 'Documents Received',    'desc' => $submitted ? 'Resume, cover letter and application letter' : 'Upload your required PDFs',                         'done' => $submitted],
            ['label' => 'Interview',             'desc' => $interviewCompleted ? 'Completed ' . $application->interview_completed_at->format('M j, Y') : ($hasInterview ? 'Scheduled ' . $application->interview_date->format('F j, Y') : 'Waiting for schedule'), 'done' => (bool) $interviewCompleted],
            ['label' => 'Final Decision',        'desc' => $decided ? $statusUi['label'] : ($status === 'hr_approved' ? 'Waiting for Admin approval' : 'Awaiting HR and Admin approval'), 'done' => $decided],
        ];
        $currentIndex = collect($timeline)->search(fn ($t) => ! $t['done']); // false when all done

    @endphp

    @if (request()->query('tab') === 'job-openings')
        @include('partial.applicant-job-openings', ['jobOpenings' => $jobOpenings ?? collect()])
    @else
    <div class="bg-gradient-to-br from-[#f5f7fa] to-[#f5f7fa] px-5 py-10">
        <div class="mx-auto max-w-[1100px] space-y-8">
            <div class="space-y-8">

                    {{-- Status card --}}
                    <div class="rounded-2xl bg-white p-6 shadow-[0_20px_60px_rgba(0,0,0,0.12)] sm:p-8">
                        <h2 class="mb-5 border-b-2 border-gray-100 pb-4 text-xl font-extrabold text-[#b22222]">Application Status</h2>

                        <div class="flex flex-col items-center justify-center rounded-xl border-2 bg-gradient-to-br p-8 text-center shadow-sm {{ $statusUi['box'] }}">
                            <i class="fa-solid {{ $statusUi['icon'] }} text-5xl"></i>
                            <h3 class="mt-3 text-2xl font-bold">{{ $statusUi['label'] }}</h3>
                            <p class="mt-2 max-w-md font-semibold">{{ $statusUi['msg'] }}</p>

                            @if (in_array($status, ['hr_rejected', 'rejected'], true) && $application->rejection_reason)
                                <p class="mt-2 max-w-md text-sm font-medium">{{ $application->rejection_reason }}</p>
                            @endif

                            @unless ($application)
                                <a href="{{ Route::has('applications.index') ? route('applications.index') : '#' }}"
                                    class="mt-5 rounded-lg bg-[#b22222] px-6 py-3 font-bold text-white transition hover:-translate-y-0.5 hover:bg-[#8f1c1c] hover:shadow-[0_6px_20px_rgba(178,34,34,0.4)]">
                                    Start Application
                                </a>
                            @endunless
                        </div>

                    </div>

                    {{-- Progress timeline --}}
                    <div class="rounded-2xl bg-white p-6 shadow-[0_20px_60px_rgba(0,0,0,0.12)] sm:p-8">
                        <h2 class="mb-6 border-b-2 border-gray-100 pb-4 text-xl font-extrabold text-[#b22222]">Progress</h2>

                        <ol class="relative space-y-6">
                            @foreach ($timeline as $i => $t)
                                @php $current = $currentIndex === $i; @endphp
                                <li class="relative flex gap-4">
                                    {{-- connecting line --}}
                                    @unless ($loop->last)
                                        <span class="absolute left-5 top-10 h-full w-[3px] -translate-x-1/2 {{ $t['done'] ? 'bg-[#28a745]' : 'bg-gray-200' }}"
                                            @if ($i === 2 && $rejected) style="background-color: #b22222;" @endif></span>
                                    @endunless

                                    <span class="z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-base font-bold
                                        {{ $i === 3 && $rejected ? 'bg-[#b22222] text-white shadow-[0_6px_20px_rgba(220,53,69,0.25)]' : ($t['done'] ? 'bg-[#28a745] text-white shadow-[0_6px_20px_rgba(40,167,69,0.25)]' : ($current ? 'scale-110 bg-[#b22222] text-white shadow-[0_6px_20px_rgba(178,34,34,0.35)]' : 'bg-gray-200 text-gray-400')) }}"
                                        @if ($i === 3 && $rejected) style="background-color: #b22222; color: #fff; box-shadow: 0 6px 20px rgba(220, 53, 69, 0.25);" @endif>
                                        @if ($i === 3 && $rejected)
                                            <i class="fa-solid fa-xmark"></i>
                                        @elseif ($t['done'])
                                            <i class="fa-solid fa-check"></i>
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </span>

                                    <div class="pb-2">
                                        <p class="font-bold {{ $i === 3 && $rejected ? 'text-[#b22222]' : ($t['done'] ? 'text-[#28a745]' : ($current ? 'text-[#b22222]' : 'text-gray-400')) }}"
                                            @if ($i === 3 && $rejected) style="color: #b22222;" @endif>{{ $t['label'] }}</p>
                                        <p class="text-sm text-gray-500">{{ $t['desc'] }}</p>
                                    </div>

                                </li>
                            @endforeach
                        </ol>
                    </div>

                    {{-- Interview + documents --}}
                    @if ($application)
                        <div class="grid gap-8 sm:grid-cols-2">
                            {{-- Interview --}}
                            <div class="rounded-[10px] border-l-[5px] border-[#b22222] bg-gradient-to-br from-[#fff9f9] to-white p-6 shadow-[0_4px_12px_rgba(0,0,0,0.08)]">
                                <h3 class="mb-4 flex items-center gap-2 text-lg font-bold text-[#b22222]">
                                    <i class="fa-solid fa-calendar-check"></i> Interview
                                </h3>
                                @if ($hasInterview)
                                    <dl class="space-y-2 text-[15px]">
                                        <div><dt class="inline font-semibold text-gray-800">Date: </dt><dd class="inline text-gray-600">{{ $application->interview_date->format('F j, Y') }}</dd></div>
                                        <div><dt class="inline font-semibold text-gray-800">Time: </dt><dd class="inline text-gray-600">{{ $application->interview_time ?? 'To be announced' }}</dd></div>
                                        <div><dt class="inline font-semibold text-gray-800">Location: </dt><dd class="inline text-gray-600">{{ $application->interview_location ?? 'St. Mary Magdalene School' }}</dd></div>
                                        <div>
                                            <dt class="inline font-semibold text-gray-800">Status: </dt>
                                            <dd class="inline font-semibold {{ $interviewCompleted || $application->interview_response === 'confirmed' ? 'text-green-700' : 'text-amber-700' }}">
                                                {{ $interviewCompleted
                                                    ? 'Interview completed'
                                                    : match ($application->interview_response) {
                                                        'confirmed' => 'Schedule confirmed',
                                                        'reschedule_requested' => 'Schedule update requested',
                                                        default => 'Scheduled',
                                                    } }}
                                            </dd>
                                        </div>
                                    </dl>
                                @else
                                    <p class="text-sm text-gray-500">No interview scheduled yet. Please check back soon.</p>
                                @endif
                            </div>

                            {{-- Documents --}}
                            <div class="rounded-[10px] border-2 border-dashed border-gray-200 bg-[#f9fbfc] p-6">
                                <h3 class="mb-4 flex items-center gap-2 text-lg font-bold text-[#b22222]">
                                    <i class="fa-solid fa-folder-open"></i> Documents
                                </h3>
                                <ul class="space-y-3 text-[15px]">
                                    @foreach (\App\Models\Application::REQUIRED_SUBMISSIONS as $key => $label)
                                        <li class="flex items-center justify-between gap-2">
                                            <span class="text-gray-700">{{ $key === 'resume' ? '📄' : '✉️' }} {{ $label }}</span>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-bold text-green-800">
                                                <i class="fa-solid fa-check"></i> Submitted
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                        </div>

                        <div class="text-right">
                            <a href="{{ Route::has('applications.index') ? route('applications.index') : '#' }}"
                                class="inline-block rounded-lg bg-[#b22222] px-7 py-3.5 font-bold text-white transition hover:-translate-y-0.5 hover:bg-[#8f1c1c] hover:shadow-[0_6px_20px_rgba(178,34,34,0.4)]">
                                View Full Application
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @endif
