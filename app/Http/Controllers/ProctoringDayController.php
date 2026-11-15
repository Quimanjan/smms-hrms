<?php

namespace App\Http\Controllers;

use App\Models\ProctoringDay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProctoringDayController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('proctoring', [
            'date' => ['required', 'date', 'unique:proctoring_days,date'],
            'name' => ['required', 'string', 'max:100'],
        ], [
            'date.unique' => 'That date is already marked as a proctoring day.',
        ]);

        ProctoringDay::create($data);

        return back()->with('success', 'Proctoring day saved.');
    }

    public function destroy(ProctoringDay $proctoringDay): RedirectResponse
    {
        $proctoringDay->delete();

        return back()->with('success', 'Proctoring day removed.');
    }
}
