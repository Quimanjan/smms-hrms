<?php

use App\Models\Application;
use App\Models\JobOpening;

uses(Tests\TestCase::class);

it('shows progress for the applied position and explains other positions', function () {
    $application = new Application(['position' => 'Elementary Teacher']);
    $elementary = new JobOpening(['title' => 'Elementary Teacher', 'is_open' => true]);
    $elementary->setAttribute('id', 1);
    $maintenance = new JobOpening(['title' => 'Maintenance Staff', 'is_open' => true]);
    $maintenance->setAttribute('id', 2);

    $html = view('partial.applicant-job-openings', [
        'application' => $application,
        'jobOpenings' => collect([$elementary, $maintenance]),
    ])->render();

    expect($html)
        ->toContain('In Progress')
        ->toContain('You have already applied for a position')
        ->toContain('class="applicant-jobs__status"')
        ->toContain('applicant-jobs__applied--progress')
        ->toContain('aria-valuenow="50"')
        ->toContain('background: var(--jobs-primary);')
        ->not->toContain('Application Submitted')
        ->not->toContain('Apply Now');
});

it('advances the applied position progress bar when the application is complete', function () {
    $application = new Application([
        'position' => 'Elementary Teacher',
        'status' => 'qualified',
        'interview_completed_at' => now(),
    ]);
    $job = new JobOpening(['title' => 'Elementary Teacher', 'is_open' => true]);
    $job->setAttribute('id', 1);

    $html = view('partial.applicant-job-openings', [
        'application' => $application,
        'jobOpenings' => collect([$job]),
    ])->render();

    expect($html)->toContain('aria-valuenow="100"')
        ->toContain('style="width: 100%"');
});

it('keeps apply actions available when the applicant has no application', function () {
    $job = new JobOpening(['title' => 'Elementary Teacher', 'is_open' => true]);
    $job->setAttribute('id', 1);

    $html = view('partial.applicant-job-openings', [
        'application' => null,
        'jobOpenings' => collect([$job]),
    ])->render();

    expect($html)->toContain('Apply Now')
        ->not->toContain('In Progress')
        ->not->toContain('You have already applied for a position');
});

it('uses the saved job type when an opening has an edited category', function () {
    $job = new JobOpening([
        'title' => 'Office Coordinator',
        'job_type' => 'teaching-related',
    ]);

    expect($job->filterType())->toBe('teaching-related');
});

it('keeps closed positions visible without offering an application action', function () {
    $job = new JobOpening([
        'title' => 'Office Coordinator',
        'is_open' => false,
    ]);
    $job->setAttribute('id', 3);

    $html = view('partial.applicant-job-openings', [
        'application' => null,
        'jobOpenings' => collect([$job]),
    ])->render();

    expect($html)
        ->toContain('Not accepting applications')
        ->toContain('Applications Closed')
        ->not->toContain('Apply Now');
});

it('renders editable job-opening details in the HR management list', function () {
    $html = view('partial.jobopening', [
        'jobOpenings' => collect([[
            'id' => 9,
            'title' => 'Office Coordinator',
            'job_type' => 'non-teaching',
            'description' => 'Coordinate school office operations.',
            'is_open' => true,
            'applicants' => 2,
        ]]),
        'errors' => new \Illuminate\Support\ViewErrorBag,
    ])->render();

    expect($html)
        ->toContain('Office Coordinator')
        ->toContain('Coordinate school office operations.')
        ->toContain('x-show="editingJobId === 9"')
        ->toContain('Edit job opening')
        ->toContain('name="job_type"')
        ->toContain('name="description"')
        ->toContain('Resume or Curriculum Vitae')
        ->toContain('Application or Cover Letter')
        ->toContain('both documents are required')
        ->not->toContain('name="requirements"')
        ->toContain('name="is_open"')
        ->toContain('Save changes');
});

it('shows the predefined application submissions on the job detail page', function () {
    $job = new JobOpening([
        'title' => 'Office Coordinator',
        'description' => 'Coordinate school office operations.',
        'is_open' => true,
    ]);
    $job->setAttribute('id', 10);

    $html = view('job-opening-detail', [
        'jobOpening' => $job,
        'jobType' => 'Non-Teaching',
    ])->render();

    expect($html)
        ->toContain('Required Documents')
        ->toContain('Resume or Curriculum Vitae (PDF, max 5 MB)')
        ->toContain('Application or Cover Letter (PDF, max 5 MB)')
        ->not->toContain('Application Letter (PDF, max 5 MB)');
});

it('renders a single application-or-cover-letter upload and supports legacy documents', function () {
    $html = view('application-form', [
        'application' => null,
        'user' => new \App\Models\User(['name' => 'Sample Applicant']),
        'jobOpenings' => collect(['Teaching Position']),
        'selectedPosition' => null,
        'errors' => new \Illuminate\Support\ViewErrorBag,
    ])->render();

    $legacyApplication = new \App\Models\Application([
        'appletter' => 'applications/legacy/application-letter.pdf',
        'appletter_original_name' => 'application-letter.pdf',
    ]);

    expect($html)
        ->toContain('Application or Cover Letter')
        ->toContain('name="coverletter"')
        ->not->toContain('name="appletter"')
        ->and(\App\Models\Application::REQUIRED_SUBMISSIONS)->toHaveCount(2)
        ->and($legacyApplication->documentPath('coverletter'))->toBe('applications/legacy/application-letter.pdf')
        ->and($legacyApplication->documentOriginalName('coverletter'))->toBe('application-letter.pdf');
});
