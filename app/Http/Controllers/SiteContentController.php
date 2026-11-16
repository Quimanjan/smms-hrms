<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Banner;
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
            'bannerSlides' => Banner::slidesFor('homepage'),
            'maxBanners' => AdminDashboardController::MAX_BANNERS,
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

    /* ---------- Homepage banner (the slider at the top of the homepage) ---------- */

    public function storeBanners(Request $request): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpeg,png', 'max:5120'],
        ], [
            'images.required' => 'Choose at least one image to upload.',
            'images.*.mimes' => 'Banner images must be PNG or JPEG.',
            'images.*.max' => 'Each banner image can be up to 5 MB.',
        ]);

        $files = $request->file('images', []);
        $max = AdminDashboardController::MAX_BANNERS;
        Banner::slidesFor('homepage');
        if (Banner::where('placement', 'homepage')->count() + count($files) > $max) {
            return back()->withErrors(['images' => "The homepage banner can have up to {$max} images. Remove some first."])->withFragment('banner');
        }

        $order = (int) Banner::where('placement', 'homepage')->max('sort_order');
        File::ensureDirectoryExists(public_path('banner'));
        foreach ($files as $file) {
            $name = Str::uuid().'.'.$file->guessExtension();
            $file->move(public_path('banner'), $name);
            Banner::create([
                'placement' => 'homepage',
                'path' => 'banner/'.$name,
                'alt' => Str::limit(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 100, ''),
                'sort_order' => ++$order,
                'uploaded_by' => $request->user()->id,
            ]);
        }

        return back()->with('success', count($files) === 1 ? 'Banner image added.' : count($files).' banner images added.')->withFragment('banner');
    }

    public function destroyBanner(Banner $banner): RedirectResponse
    {
        abort_unless($banner->placement === 'homepage', 404);
        if (Banner::where('placement', 'homepage')->count() <= 1) {
            return back()->withErrors(['images' => 'Keep at least one banner image on the homepage.'])->withFragment('banner');
        }
        $banner->deleteImage();
        $banner->delete();

        return back()->with('success', 'Banner image removed.')->withFragment('banner');
    }

    public function resetBanners(): RedirectResponse
    {
        Banner::restoreDefaults('homepage');

        return back()->with('success', 'The homepage banner is back to the original images.')->withFragment('banner');
    }
}
