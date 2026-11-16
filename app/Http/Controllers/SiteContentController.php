<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\CampusPhoto;
use App\Support\SiteContent;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
            'announcements' => Announcement::orderByDesc('posted_on')->orderByDesc('id')->get(),
            'campusPhotos' => CampusPhoto::orderBy('sort_order')->orderBy('id')->get(),
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

    /* ---------- Announcements ---------- */

    private function announcementRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:3000'],
            'posted_on' => ['required', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        $data = $request->validate($this->announcementRules());
        Announcement::create($data + ['is_published' => $request->boolean('is_published'), 'created_by' => $request->user()->id]);

        return back()->with('success', 'Announcement posted.')->withFragment('announcements');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validate($this->announcementRules());
        $announcement->update($data + ['is_published' => $request->boolean('is_published')]);

        return back()->with('success', 'Announcement saved.')->withFragment('announcements');
    }

    public function destroyAnnouncement(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.')->withFragment('announcements');
    }

    /* ---------- Campus photos ---------- */

    public function storePhoto(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'caption' => ['required', 'string', 'max:200'],
        ], ['photo.max' => 'The photo must be 5 MB or smaller.']);

        $file = $request->file('photo');
        $name = Str::uuid().'.'.$file->guessExtension();
        File::ensureDirectoryExists(public_path(CampusPhoto::UPLOAD_DIR));
        $file->move(public_path(CampusPhoto::UPLOAD_DIR), $name);

        CampusPhoto::create([
            'path' => CampusPhoto::UPLOAD_DIR.'/'.$name,
            'caption' => $data['caption'],
            'sort_order' => (int) CampusPhoto::max('sort_order') + 1,
            'uploaded_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Photo added to the campus section.')->withFragment('campus');
    }

    public function updatePhoto(Request $request, CampusPhoto $photo): RedirectResponse
    {
        $data = $request->validate(['caption' => ['required', 'string', 'max:200']]);
        $photo->update($data);

        return back()->with('success', 'Caption saved.')->withFragment('campus');
    }

    /** Moves a photo one place earlier ("up") or later ("down"). */
    public function movePhoto(Request $request, CampusPhoto $photo): RedirectResponse
    {
        $up = $request->input('direction') === 'up';
        $photos = CampusPhoto::orderBy('sort_order')->orderBy('id')->get()->values();
        $i = $photos->search(fn ($p) => $p->id === $photo->id);
        $j = $up ? $i - 1 : $i + 1;

        if ($i !== false && isset($photos[$j])) {
            $ordered = $photos->all();
            [$ordered[$i], $ordered[$j]] = [$ordered[$j], $ordered[$i]];
            foreach ($ordered as $n => $p) {
                $p->update(['sort_order' => $n + 1]);
            }
        }

        return back()->withFragment('campus');
    }

    public function destroyPhoto(CampusPhoto $photo): RedirectResponse
    {
        $photo->deleteFile();
        $photo->delete();

        return back()->with('success', 'Photo removed.')->withFragment('campus');
    }
}
