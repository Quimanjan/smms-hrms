<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobOpening;
use App\Services\HrmsNotifier;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    private const EDUCATION = [
        'Elementary', 'High School', 'Senior High School', 'Vocational',
        'Associate Degree', "Bachelor's Degree", "Master's Degree",
    ];

    public function index(Request $request)
    {
        $jobOpenings = JobOpening::where('is_open', true)->orderBy('title')->pluck('title');
        $requestedPosition = $request->query('position');

        return view('application-form', [
            'application' => Application::where('user_id', Auth::id())->first(),
            'user' => Auth::user(),
            // Only positions that are currently open (managed from the Open Positions page)
            'jobOpenings' => $jobOpenings,
            'selectedPosition' => is_string($requestedPosition) && $jobOpenings->contains($requestedPosition)
                ? $requestedPosition
                : null,
        ]);
    }

    public function respondToInterview(Request $request): RedirectResponse
    {
        $application = Application::where('user_id', Auth::id())->firstOrFail();

        if (! $application->interview_date || $application->interview_completed_at || ! $application->can_schedule) {
            return redirect()->route('applications.index')->withErrors([
                'interview_response' => 'There is no active interview schedule to respond to.',
            ]);
        }

        $data = $request->validate([
            'response' => ['required', Rule::in(['confirmed', 'reschedule_requested'])],
            'reason' => ['nullable', 'required_if:response,reschedule_requested', 'string', 'max:1000'],
        ], [
            'reason.required_if' => 'Please provide a reason before requesting a schedule update.',
        ]);

        $application->update([
            'interview_response' => $data['response'],
            'interview_response_reason' => $data['response'] === 'reschedule_requested'
                ? trim($data['reason'])
                : null,
        ]);

        if ($data['response'] === 'confirmed') {
            HrmsNotifier::applicantInterviewConfirmed($application->fresh());
        } else {
            HrmsNotifier::applicantInterviewRescheduleRequested($application->fresh());
        }

        return redirect()->route('applications.index')->with(
            'success',
            $data['response'] === 'confirmed'
                ? 'Your interview schedule is confirmed.'
                : 'Your schedule update request has been sent to HR for review.'
        );
    }

    public function store(Request $request)
    {
        // One application per applicant
        if (Application::where('user_id', Auth::id())->exists()) {
            return redirect()->route('applications.index')
                ->with('error', 'You have already submitted an application.');
        }

        $data = $request->validate([
            'first_name' => 'required|string|max:75',
            'middle_name' => 'nullable|string|max:75',
            'last_name' => 'required|string|max:75',
            'suffix' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'required|digits:11',
            'address_line' => 'required|string|max:160',
            'address_landmark' => 'nullable|string|max:160',
            'barangay' => 'required|string|max:100',
            'city_municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'zip_code' => ['required', 'regex:/^\d{4}$/'],
            'birth_date' => 'required|date|before_or_equal:'.now()->subYears(18)->toDateString(),
            'gender' => 'required|in:Male,Female,Other',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated,Divorced',
            'education' => ['required', Rule::in(self::EDUCATION)],
            'education_school' => 'required|string|max:160',
            'education_year' => ['nullable', 'regex:/^\d{4}$/', 'integer', 'min:1950', 'max:'.now()->year],
            // Must be one of the currently open positions
            'position' => ['required', 'string', Rule::exists('job_openings', 'title')->where('is_open', true)],
            'resume' => 'required|file|mimes:pdf|max:5120',
            'coverletter' => 'required|file|mimes:pdf|max:5120',
        ], [
            'birth_date.before_or_equal' => 'You must be at least 18 years old.',
            'education_school.required' => 'Enter the name of the school where you finished your highest education.',
            'education_year.*' => 'Enter the year you graduated as four digits, for example 2022.',
            'position.exists' => 'That position is no longer open. Please choose another one.',
        ]);

        $jobOpening = JobOpening::where('title', $data['position'])
            ->where('is_open', true)
            ->first();

        if (! $jobOpening) {
            return back()
                ->withErrors(['position' => 'That position is no longer open. Please choose another one.'])
                ->withInput();
        }

        $nameParts = array_filter([
            trim($data['first_name']),
            trim($data['middle_name'] ?? ''),
            trim($data['last_name']),
        ], fn (string $part): bool => $part !== '');
        $data['fullname'] = implode(' ', $nameParts);
        if (filled($data['suffix'] ?? null)) {
            $data['fullname'] .= ', '.trim($data['suffix']);
        }

        $data['first_name'] = trim($data['first_name']);
        $data['middle_name'] = filled($data['middle_name'] ?? null) ? trim($data['middle_name']) : null;
        $data['last_name'] = trim($data['last_name']);
        $data['suffix'] = filled($data['suffix'] ?? null) ? trim($data['suffix']) : null;

        $data['home_address'] = implode(', ', array_filter([
            trim($data['address_line']),
            trim($data['address_landmark'] ?? ''),
            trim($data['barangay']),
            trim($data['city_municipality']),
            trim($data['province']),
            trim($data['zip_code']),
        ], fn (string $part): bool => $part !== ''));
        unset(
            $data['address_line'],
            $data['address_landmark'],
            $data['barangay'],
            $data['city_municipality'],
            $data['province'],
            $data['zip_code'],
        );

        // Use the same disk the Application model reads from
        $disk = Application::DOCUMENT_DISK;
        $stored = [];

        try {
            foreach (array_keys(Application::DOCUMENTS) as $doc) {
                $file = $request->file($doc);
                $originalName = basename(str_replace('\\', '/', $file->getClientOriginalName()));
                $originalName = preg_replace('/[\x00-\x1F\x7F]/', '', $originalName) ?: 'document.pdf';

                $data[$doc] = $stored[] = $file->store('applications/'.Auth::id(), $disk);
                $data[$doc.'_original_name'] = $originalName;
            }

            // Retain the legacy column for existing database schemas; there is only one letter upload.
            $data['appletter'] = $data['coverletter'];
            $data['appletter_original_name'] = $data['coverletter_original_name'];

            $data['user_id'] = Auth::id();
            $data['job_type'] = match ($jobOpening->filterType()) {
                'teaching' => 'Teaching',
                'teaching-related' => 'Teaching-Related',
                default => 'Staff',
            };
            $data['status'] = 'pending';

            $application = Application::create($data);
        } catch (QueryException $e) {
            // Don't leave orphaned files if the insert fails
            Storage::disk($disk)->delete($stored);

            // Double-click / race: the unique user_id index caught a duplicate
            if (($e->errorInfo[1] ?? null) === 1062) {
                return redirect()->route('applications.index')
                    ->with('error', 'You have already submitted an application.');
            }

            throw $e;
        }

        HrmsNotifier::newApplicant($application);

        return redirect()->route('applications.index')->with('success', 'Application submitted!');
    }
}
