<?php

namespace App\Http\Controllers;

use App\Models\JobOpening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JobOpeningController extends Controller
{
    private const JOB_TYPES = ['teaching', 'non-teaching', 'teaching-related'];

    public function store(Request $request)
    {
        $request->merge(['title' => trim((string) $request->input('title'))]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:job_openings,title'],
            'job_type' => ['required', Rule::in(self::JOB_TYPES)],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_open' => ['required', 'boolean'],
        ], [
            'title.unique' => 'That position already exists.',
        ]);

        $data['title'] = trim($data['title']);
        $data['description'] = filled($data['description'] ?? null) ? trim($data['description']) : null;
        $data['is_open'] = $request->boolean('is_open');

        JobOpening::create($data);

        return back()->with('success', 'Job opening added.');
    }

    public function update(Request $request, JobOpening $jobOpening)
    {
        $request->merge(['title' => trim((string) $request->input('title'))]);

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255', Rule::unique('job_openings', 'title')->ignore($jobOpening->getKey())],
            'job_type' => ['required', Rule::in(self::JOB_TYPES)],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_open' => ['required', 'boolean'],
        ], [
            'title.unique' => 'That position already exists.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'jobOpening')
                ->withInput()
                ->with('edit_job_opening', $jobOpening->getKey());
        }

        $data = $validator->validated();
        $data['title'] = trim($data['title']);
        $data['description'] = filled($data['description'] ?? null) ? trim($data['description']) : null;
        $data['is_open'] = $request->boolean('is_open');
        $originalTitle = $jobOpening->title;

        DB::transaction(function () use ($jobOpening, $data, $originalTitle): void {
            $jobOpening->update($data);

            if ($originalTitle !== $jobOpening->title) {
                DB::table('applications')
                    ->where('position', $originalTitle)
                    ->update(['position' => $jobOpening->title]);
            }
        });

        return back()->with('success', 'Job opening updated.');
    }

    public function destroy(JobOpening $jobOpening)
    {
        $jobOpening->delete();

        return back()->with('success', 'Job opening removed.');
    }
}