<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\SubmissionBox;
use App\Services\HrmsNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    /**
     * HR document management: the school's fixed onboarding requirements, who
     * submitted each one, and every employee's onboarding progress.
     */
    public function index()
    {
        $requirements = SubmissionBox::requirements()
            ->with(['documents' => fn ($q) => $q->with('employee')->reorder()->latest('id')])
            ->get();

        $employees = Employee::query()
            ->whereNotNull('user_id')
            ->orderBy('name')
            ->get(['id', 'name', 'employee_id', 'position']);

        $required = $requirements->where('is_optional', false);
        $status = fn ($doc) => strtolower((string) $doc?->status);

        $progress = $employees->map(function (Employee $e) use ($requirements, $required, $status) {
            $mine = $requirements->map(fn ($r) => $r->documents->firstWhere('employee_id', $e->id))->filter();

            return [
                'id' => $e->id,
                'name' => $e->name,
                'number' => $e->employee_id,
                'position' => $e->position,
                'approved' => $required->filter(fn ($r) => $status($r->documents->firstWhere('employee_id', $e->id)) === 'approved')->count(),
                'waiting' => $mine->filter(fn ($d) => $status($d) === 'pending')->count(),
                'required' => $required->count(),
            ];
        })->values();

        $cards = $requirements->map(function (SubmissionBox $r) use ($employees, $status) {
            // Latest upload per employee.
            $latest = $r->documents->unique('employee_id');

            return [
                'id' => $r->id,
                'number' => $r->sort_order,
                'name' => $r->name,
                'optional' => $r->is_optional,
                'waiting' => $latest->filter(fn ($d) => $status($d) === 'pending')->count(),
                'approved' => $latest->filter(fn ($d) => $status($d) === 'approved')->count(),
                'submissions' => $latest->map(fn ($d) => [
                    'id' => $d->id,
                    'employee' => $d->employee?->name ?? 'Employee',
                    'number' => $d->employee?->employee_id,
                    'file' => $d->name,
                    'size' => $d->size_label,
                    'date' => $d->created_at->timezone(config('app.timezone'))->toDateString(),
                    'date_label' => $d->created_at->timezone(config('app.timezone'))->format('M j, Y g:i A'),
                    'status' => ucfirst($status($d)),
                    'url' => $d->file_path ? route('employee.documents.file', $d) : null,
                ])->values(),
                'missing' => $employees->reject(fn ($e) => $latest->contains('employee_id', $e->id))->pluck('name')->values(),
            ];
        })->values();

        $all = $requirements->flatMap(fn ($r) => $r->documents->unique('employee_id'));

        return view('hr-document', [
            'requirements' => $cards,
            'progress' => $progress,
            'stats' => [
                'waiting' => $all->filter(fn ($d) => $status($d) === 'pending')->count(),
                'approved' => $all->filter(fn ($d) => $status($d) === 'approved')->count(),
                'complete' => $progress->filter(fn ($p) => $p['required'] > 0 && $p['approved'] >= $p['required'])->count(),
                'employees' => $progress->count(),
            ],
            'reopenBox' => session('reopen_box'),
        ]);
    }

    /** Approve or reject an uploaded document. */
    public function decide(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'document_id' => ['required', 'integer', 'exists:employee_documents,id'],
            'decision'    => ['required', Rule::in([EmployeeDocument::APPROVED, EmployeeDocument::REJECTED])],
        ]);

        $document = EmployeeDocument::findOrFail($data['document_id']);
        $boxId = $document->submission_box_id;

        // Rejected files are deleted so the employee can upload a new one.
        if ($data['decision'] === EmployeeDocument::REJECTED) {
            HrmsNotifier::documentsReviewed($document, $data['decision']);

            // Burahin muna ang file sa storage, tapos ang record
            if ($document->file_path) {
                Storage::disk('local')->delete($document->file_path);
            }

            $document->delete();
        } else {
            $document->update([
                'status'      => strtolower($data['decision']),
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
            ]);
            HrmsNotifier::documentsReviewed($document, $data['decision']);
        }

        return redirect()->route('documents.index')
            ->with('success', $data['decision'] === EmployeeDocument::REJECTED
                ? 'Document rejected and removed. The employee can upload a new one.'
                : 'Document approved.')
            ->with('reopen_box', $boxId);
    }

    /** Delete an uploaded document (and its file), then reopen the box it belonged to. */
    public function destroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'document_id' => ['required', 'integer', 'exists:employee_documents,id'],
        ]);

        $document = EmployeeDocument::findOrFail($data['document_id']);

        if ($document->file_path) {
            Storage::disk('local')->delete($document->file_path);
        }

        $boxId = $document->submission_box_id;
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted.')
            ->with('reopen_box', $boxId);
    }

    /** Opens the private file in the browser (only HR can reach this route). */
    public function file(EmployeeDocument $document)
    {
        abort_unless(
            $document->file_path && Storage::disk('local')->exists($document->file_path),
            404
        );

        return response()->file(Storage::disk('local')->path($document->file_path));
    }
}