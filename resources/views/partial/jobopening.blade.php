{{-- Expects: $jobOpenings (collection of job-opening details and applicant counts) --}}
<section aria-labelledby="openPositionsTitle"
    class="mt-6 max-w-[1100px] rounded-[22px] border border-[#f0dada] bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.05)] sm:p-7">

    <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 id="openPositionsTitle" class="flex items-center gap-3 text-2xl font-extrabold tracking-wide text-slate-900">
                <i class="fas fa-briefcase text-2xl text-[#b22222]" aria-hidden="true"></i>
                Job Openings
            </h2>
            <p class="mt-1.5 text-sm font-medium text-gray-500">
                {{ $jobOpenings->where('is_open', true)->count() }} open
                @if ($jobOpenings->isNotEmpty())
                    · {{ $jobOpenings->count() }} total
                @endif
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="button" @click="managingJobs = true"
                class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-[#b22222] px-[18px] py-2.5 text-sm font-semibold text-white shadow-[0_2px_6px_rgba(220,38,38,0.2)] transition hover:bg-[#b22222]">
                <i class="fas fa-list" aria-hidden="true"></i> Job Openings
            </button>
            {{-- <button type="button" @click="addingJob = true"
                class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-[#b22222] px-[18px] py-2.5 text-sm font-semibold text-white shadow-[0_2px_6px_rgba(220,38,38,0.2)] transition hover:bg-[#b22222]">
                <i class="fas fa-plus-circle" aria-hidden="true"></i> Add Job Opening
            </button> --}}
        </div>
    </div>

    @if ($jobOpenings->isNotEmpty())
        <div class="grid gap-3 [grid-template-columns:repeat(auto-fit,minmax(180px,1fr))]">
            @foreach ($jobOpenings as $job)
                <div class="min-w-0 rounded-[10px] border border-gray-200 bg-white p-4">
                    <h3 class="text-sm font-semibold leading-snug text-slate-900">{{ $job['title'] }}</h3>
                    <div class="mt-2 text-[22px] font-bold text-[#b22222]">{{ $job['applicants'] }}</div>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $job['applicants'] !== 1 ? 'Applicants' : 'Applicant' }} applied ·
                        {{ $job['is_open'] ? 'Open' : 'Closed' }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Reopen the add modal if adding failed validation (e.g. duplicate title) --}}
    @if ($errors->hasAny(['title', 'job_type', 'description', 'is_open']))
        <div x-init="addingJob = true" class="hidden"></div>
    @endif

    @if (session('edit_job_opening'))
        <div x-init="managingJobs = true; editingJobId = {{ (int) session('edit_job_opening') }}" class="hidden"></div>
    @endif

    {{-- ============ Add job opening modal ============ --}}
    <div x-show="addingJob" x-cloak x-transition.opacity
        x-effect="if (addingJob) $nextTick(() => $refs.newJobTitle && $refs.newJobTitle.focus())"
        class="fixed inset-0 z-[70] grid place-items-center p-5">
        <div class="absolute inset-0 bg-[rgba(17,24,39,0.6)]" @click="addingJob = false"></div>

        <section role="dialog" aria-modal="true" aria-labelledby="addJobTitle"
            class="relative max-h-[90vh] w-full max-w-[560px] overflow-auto rounded-[20px] bg-white p-[26px] shadow-[0_30px_90px_rgba(0,0,0,0.3)]">

            <div class="mb-4 flex items-start justify-between gap-5">
                <div>
                    <h2 id="addJobTitle" class="text-xl font-bold">Add job opening</h2>
                    <p class="mt-1 text-sm text-gray-500">Enter the position details applicants will see.</p>
                </div>
                <button type="button" @click="addingJob = false" aria-label="Close add job opening dialog"
                    class="h-9 w-9 shrink-0 cursor-pointer rounded-full bg-gray-100 text-2xl leading-none text-gray-900 transition hover:bg-gray-200">&times;</button>
            </div>

            <form method="POST" action="{{ route('job-openings.store') }}" class="grid gap-3">
                @csrf

                <label for="newJobTitle" class="text-sm font-bold text-gray-800">Position title</label>
                <input type="text" id="newJobTitle" name="title" x-ref="newJobTitle" required maxlength="255"
                    value="{{ old('title') }}" placeholder="e.g. Math Teacher"
                    class="w-full rounded-md border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#b22222] focus:ring-2 focus:ring-[#b22222]/10">

                @error('title')
                    <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror

                <label for="newJobType" class="mt-1 text-sm font-bold text-gray-800">Job type</label>
                <select id="newJobType" name="job_type" required
                    class="w-full rounded-md border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#b22222] focus:ring-2 focus:ring-[#b22222]/10">
                    <option value="teaching" @selected(old('job_type', 'teaching') === 'teaching')>Teaching</option>
                    <option value="non-teaching" @selected(old('job_type') === 'non-teaching')>Non-Teaching</option>
                    <option value="teaching-related" @selected(old('job_type') === 'teaching-related')>Teaching-Related</option>
                </select>
                @error('job_type')
                    <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror

                <label for="newJobDescription" class="mt-1 text-sm font-bold text-gray-800">Job description</label>
                <textarea id="newJobDescription" name="description" rows="4" maxlength="5000"
                    placeholder="Describe the role and its responsibilities"
                    class="w-full resize-y rounded-md border border-gray-200 p-3 text-sm outline-none focus:border-[#b22222] focus:ring-2 focus:ring-[#b22222]/10">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-1 grid gap-1">
                    <span class="text-sm font-bold text-gray-800">Required document submissions</span>
                    <ul class="grid gap-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">
                        @foreach (\App\Models\Application::REQUIRED_SUBMISSIONS as $submission)
                            <li>{{ $submission }}</li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-gray-500">Predefined by the application form; both documents are required.</p>
                </div>

                <label for="newJobStatus" class="mt-1 text-sm font-bold text-gray-800">Application status</label>
                <select id="newJobStatus" name="is_open" required
                    class="w-full rounded-md border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#b22222] focus:ring-2 focus:ring-[#b22222]/10">
                    <option value="1" @selected(old('is_open', '1') === '1')>Open - accepting applications</option>
                    <option value="0" @selected(old('is_open') === '0')>Closed - not accepting applications</option>
                </select>
                @error('is_open')
                    <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-1 flex justify-end gap-3">
                    <button type="button" @click="addingJob = false"
                        class="cursor-pointer rounded-lg bg-gray-100 px-5 py-2.5 font-bold text-gray-800 transition hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="cursor-pointer rounded-lg bg-[#b22222] px-5 py-2.5 font-bold text-white transition hover:bg-[#b22222]">
                        Add job opening
                    </button>
                </div>
            </form>
        </section>
    </div>

    {{-- ============ Manage job openings modal (list + delete) ============ --}}
    <div x-show="managingJobs" x-cloak x-transition.opacity class="fixed inset-0 z-[60] grid place-items-center p-5">
        <div class="absolute inset-0 bg-[rgba(17,24,39,0.55)]" @click="managingJobs = false"></div>

        <section role="dialog" aria-modal="true" aria-labelledby="manageJobsTitle"
            class="relative max-h-[90vh] w-full max-w-[720px] overflow-auto rounded-[20px] bg-white p-[26px] shadow-[0_30px_80px_rgba(15,23,42,0.25)]">

            <div class="mb-4 flex items-start justify-between gap-5">
                <div>
                    <h2 id="manageJobsTitle" class="text-xl font-bold">Job openings</h2>
                    <p class="mt-1 text-sm text-gray-500">Edit position details, change application status, or remove a position.</p>
                </div>
                <button type="button" @click="managingJobs = false" aria-label="Close"
                    class="h-9 w-9 shrink-0 cursor-pointer rounded-full bg-gray-100 text-2xl leading-none text-gray-900 transition hover:bg-gray-200">&times;</button>
            </div>

            <button type="button" @click="addingJob = true"
                class="mb-4 inline-flex cursor-pointer items-center gap-2 rounded-lg bg-[#b22222] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#b22222]">
                <i class="fas fa-plus" aria-hidden="true"></i> Add new
            </button>

            @if ($jobOpenings->isNotEmpty())
                <ul class="grid gap-2">
                    @foreach ($jobOpenings as $job)
                        <li class="grid gap-3 rounded-[10px] border border-gray-200 px-4 py-3">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="block break-words text-sm font-semibold text-slate-900">{{ $job['title'] }}</span>
                                    <span class="text-xs text-gray-500">
                                        {{ ucfirst(str_replace('-', ' ', $job['job_type'])) }} ·
                                        {{ $job['is_open'] ? 'Open' : 'Closed' }} ·
                                        {{ $job['applicants'] }} applicant{{ $job['applicants'] !== 1 ? 's' : '' }}
                                    </span>
                                </div>

                                <div class="flex shrink-0 items-center gap-2">
                                    <button type="button" @click="editingJobId = {{ $job['id'] }}"
                                        class="cursor-pointer rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 transition hover:bg-gray-50">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('job-openings.destroy', $job['id']) }}" class="m-0"
                                        data-confirm="{{ $job['applicants'] > 0
                                            ? 'This position has ' . $job['applicants'] . ' applicant(s). Their applications are kept. Remove the opening anyway?'
                                            : 'Remove this job opening?' }}"
                                        data-confirm-button="Delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Remove {{ $job['title'] }}"
                                            class="cursor-pointer rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm font-semibold text-[#b22222] transition hover:border-red-300 hover:bg-red-50">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </li>
                    @endforeach
                </ul>
            @else
                <p class="rounded-[10px] border border-dashed border-[#e5caca] px-4 py-6 text-center text-sm text-gray-500">
                    No job openings yet. Click "Add new" to create the first one.
                </p>
            @endif
        </section>
    </div>

    @foreach ($jobOpenings as $job)
        @php
            $reopeningEdit = (int) session('edit_job_opening') === (int) $job['id'];
            $editTitle = $reopeningEdit ? old('title', $job['title']) : $job['title'];
            $editJobType = $reopeningEdit ? old('job_type', $job['job_type']) : $job['job_type'];
            $editDescription = $reopeningEdit ? old('description', $job['description']) : $job['description'];
            $editIsOpen = $reopeningEdit
                ? old('is_open', $job['is_open'] ? '1' : '0') === '1'
                : $job['is_open'];
        @endphp

        <div x-show="editingJobId === {{ $job['id'] }}" x-cloak x-transition.opacity
            class="fixed inset-0 z-[80] grid place-items-center p-5">
            <div class="absolute inset-0 bg-[rgba(17,24,39,0.65)]" @click="editingJobId = null"></div>

            <section role="dialog" aria-modal="true" aria-labelledby="editJobTitleHeading{{ $job['id'] }}"
                class="relative max-h-[90vh] w-full max-w-[560px] overflow-auto rounded-[20px] bg-white p-[26px] shadow-[0_30px_90px_rgba(0,0,0,0.35)]">
                <div class="mb-4 flex items-start justify-between gap-5">
                    <div>
                        <h2 id="editJobTitleHeading{{ $job['id'] }}" class="text-xl font-bold">Edit job opening</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $job['title'] }}</p>
                    </div>
                    <button type="button" @click="editingJobId = null" aria-label="Close edit job opening dialog"
                        class="h-9 w-9 shrink-0 cursor-pointer rounded-full bg-gray-100 text-2xl leading-none text-gray-900 transition hover:bg-gray-200">&times;</button>
                </div>

                <form method="POST" action="{{ route('job-openings.update', $job['id']) }}" class="grid gap-3">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-1">
                        <label for="editJobTitle{{ $job['id'] }}" class="text-sm font-bold text-gray-800">Position title</label>
                        <input id="editJobTitle{{ $job['id'] }}" type="text" name="title" required maxlength="255"
                            value="{{ $editTitle }}"
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-[#b22222] focus:ring-2 focus:ring-[#b22222]/10">
                        @if ($reopeningEdit)
                            @error('title', 'jobOpening')
                                <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div class="grid gap-1">
                        <label for="editJobType{{ $job['id'] }}" class="text-sm font-bold text-gray-800">Job type</label>
                        <select id="editJobType{{ $job['id'] }}" name="job_type" required
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-[#b22222] focus:ring-2 focus:ring-[#b22222]/10">
                            <option value="teaching" @selected($editJobType === 'teaching')>Teaching</option>
                            <option value="non-teaching" @selected($editJobType === 'non-teaching')>Non-Teaching</option>
                            <option value="teaching-related" @selected($editJobType === 'teaching-related')>Teaching-Related</option>
                        </select>
                        @if ($reopeningEdit)
                            @error('job_type', 'jobOpening')
                                <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div class="grid gap-1">
                        <label for="editJobDescription{{ $job['id'] }}" class="text-sm font-bold text-gray-800">Job description</label>
                        <textarea id="editJobDescription{{ $job['id'] }}" name="description" rows="4" maxlength="5000"
                            class="w-full resize-y rounded-md border border-gray-200 p-3 text-sm outline-none focus:border-[#b22222] focus:ring-2 focus:ring-[#b22222]/10">{{ $editDescription }}</textarea>
                        @if ($reopeningEdit)
                            @error('description', 'jobOpening')
                                <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div class="grid gap-1">
                        <span class="text-sm font-bold text-gray-800">Required document submissions</span>
                        <ul class="grid gap-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">
                            @foreach (\App\Models\Application::REQUIRED_SUBMISSIONS as $submission)
                                <li>{{ $submission }}</li>
                            @endforeach
                        </ul>
                        <p class="text-xs text-gray-500">Predefined by the application form; both documents are required.</p>
                    </div>

                    <div class="grid gap-1">
                        <label for="editJobStatus{{ $job['id'] }}" class="text-sm font-bold text-gray-800">Application status</label>
                        <select id="editJobStatus{{ $job['id'] }}" name="is_open" required
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm outline-none focus:border-[#b22222] focus:ring-2 focus:ring-[#b22222]/10">
                            <option value="1" @selected($editIsOpen)>Open - accepting applications</option>
                            <option value="0" @selected(! $editIsOpen)>Closed - not accepting applications</option>
                        </select>
                        @if ($reopeningEdit)
                            @error('is_open', 'jobOpening')
                                <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div class="mt-1 flex justify-end gap-3">
                        <button type="button" @click="editingJobId = null"
                            class="cursor-pointer rounded-lg bg-gray-100 px-5 py-2.5 font-bold text-gray-800 transition hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="cursor-pointer rounded-lg bg-[#b22222] px-5 py-2.5 font-bold text-white transition hover:bg-[#b22222]">
                            Save changes
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endforeach
</section>