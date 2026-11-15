<?php

namespace App\Http\Controllers;

use App\Support\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** HR and Admin edit the homepage text. */
class SiteContentController extends Controller
{
    public function edit(): View
    {
        return view('site-content', [
            'fields' => SiteContent::FIELDS,
            'values' => SiteContent::all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];
        foreach (SiteContent::FIELDS as $key => [, , $rows]) {
            $rules[$key] = ['nullable', 'string', $rows > 1 ? 'max:3000' : 'max:200'];
        }
        $rules['email'][] = 'email';
        $rules['facebook'][] = 'url';

        $data = $request->validate($rules);

        foreach ($data as $key => $value) {
            DB::table('site_contents')->updateOrInsert(
                ['key' => $key],
                ['value' => trim((string) $value), 'updated_by' => $request->user()->id, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        SiteContent::forget();

        return back()->with('success', 'Homepage updated. Empty boxes use the original text.');
    }
}
