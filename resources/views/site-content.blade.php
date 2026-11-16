<x-app-layout>
    <x-slot name="title">Homepage Content</x-slot>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold leading-tight text-red-700 sm:text-3xl md:text-4xl">Homepage</h2>
    </x-slot>

    <div class="mx-auto max-w-[1100px] px-4 pb-12 pt-2 sm:px-6 lg:px-10">
        <div class="cms">
            <div class="cms-intro">
                <p>Change what visitors see on the public homepage. Every change shows on the site right away.</p>
                <div class="cms-links">
                    <a href="{{ url('/') }}" target="_blank" rel="noopener">Open the homepage</a>
                    <a href="#announcements">Announcements</a>
                    <a href="#campus">Campus photos</a>
                    <a href="#page-text">Page text</a>
                    <a href="{{ route('dashboard') }}">Announcement slider images (on the dashboard)</a>
                </div>
            </div>

            @if (session('success'))
                <p class="rq-flash" role="status">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                <p class="rq-flash rq-flash--error" role="alert">{{ $errors->first() }}</p>
            @endif

            {{-- ---------- Announcements ---------- --}}
            <section class="cms-group" id="announcements" aria-labelledby="ann-title">
                <h3 class="cms-h" id="ann-title">Announcements</h3>
                <p class="cms-help">Posts show in the Announcements section of the homepage, newest first (up to 6). A post with a future date appears on that date.</p>

                <details class="cms-new" @if ($errors->hasAny(['title', 'body', 'posted_on']) && ! old('_announcement')) open @endif>
                    <summary>Post an announcement</summary>
                    <form method="POST" action="{{ route('site-content.announcements.store') }}" class="cms-ann-form">
                        @csrf
                        <label class="cms-field"><span>Title</span><input name="title" maxlength="160" required value="{{ old('_announcement') ? '' : old('title') }}" placeholder="e.g., Enrollment for School Year 2027 to 2028"></label>
                        <label class="cms-field"><span>Message</span><textarea name="body" rows="4" maxlength="3000" required placeholder="What should visitors know?">{{ old('_announcement') ? '' : old('body') }}</textarea></label>
                        <div class="cms-row">
                            <label class="cms-field"><span>Date</span><input type="date" name="posted_on" required value="{{ old('_announcement') ? today()->toDateString() : old('posted_on', now(config('app.display_timezone'))->toDateString()) }}"></label>
                            <label class="cms-check"><input type="checkbox" name="is_published" value="1" checked> Show on the homepage</label>
                        </div>
                        <div class="cms-row-end"><button type="submit" class="cms-btn">Post announcement</button></div>
                    </form>
                </details>

                <ul class="cms-ann-list">
                    @forelse ($announcements as $a)
                        <li x-data="{ editing: {{ old('_announcement') == $a->id ? 'true' : 'false' }} }">
                            <div class="cms-ann-view" x-show="!editing">
                                <div class="cms-ann-meta">
                                    <time>{{ $a->posted_on->format('F j, Y') }}</time>
                                    @if (! $a->is_published)
                                        <span class="cms-tag">Hidden</span>
                                    @elseif ($a->posted_on->isFuture())
                                        <span class="cms-tag">Scheduled</span>
                                    @endif
                                </div>
                                <strong>{{ $a->title }}</strong>
                                <p>{{ $a->body }}</p>
                                <div class="cms-ann-actions">
                                    <button type="button" class="cms-link" @click="editing = true">Edit</button>
                                    <form method="POST" action="{{ route('site-content.announcements.destroy', $a) }}" onsubmit="return confirm('Delete this announcement? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="cms-link cms-link--danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('site-content.announcements.update', $a) }}" class="cms-ann-form" x-show="editing" x-cloak>
                                @csrf @method('PUT')
                                <input type="hidden" name="_announcement" value="{{ $a->id }}">
                                <label class="cms-field"><span>Title</span><input name="title" maxlength="160" required value="{{ old('_announcement') == $a->id ? old('title') : $a->title }}"></label>
                                <label class="cms-field"><span>Message</span><textarea name="body" rows="4" maxlength="3000" required>{{ old('_announcement') == $a->id ? old('body') : $a->body }}</textarea></label>
                                <div class="cms-row">
                                    <label class="cms-field"><span>Date</span><input type="date" name="posted_on" required value="{{ $a->posted_on->toDateString() }}"></label>
                                    <label class="cms-check"><input type="checkbox" name="is_published" value="1" @checked($a->is_published)> Show on the homepage</label>
                                </div>
                                <div class="cms-row-end">
                                    <button type="button" class="cms-link" @click="editing = false">Cancel</button>
                                    <button type="submit" class="cms-btn">Save</button>
                                </div>
                            </form>
                        </li>
                    @empty
                        <li class="cms-empty">No announcements yet. Post one above and it will show on the homepage.</li>
                    @endforelse
                </ul>
            </section>

            {{-- ---------- Campus photos ---------- --}}
            <section class="cms-group" id="campus" aria-labelledby="campus-title">
                <h3 class="cms-h" id="campus-title">Campus photos</h3>
                <p class="cms-help">These photos show in "The campus" section. The first photo is shown largest. JPG, PNG, or WebP, up to 5 MB each.</p>

                <form method="POST" action="{{ route('site-content.photos.store') }}" enctype="multipart/form-data" class="cms-photo-add">
                    @csrf
                    <label class="cms-field"><span>Photo</span><input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required></label>
                    <label class="cms-field"><span>Caption (describes the photo for everyone)</span><input name="caption" maxlength="200" required placeholder="e.g., The school chapel" value="{{ old('caption') }}"></label>
                    <button type="submit" class="cms-btn">Add photo</button>
                </form>

                <ol class="cms-photos">
                    @forelse ($campusPhotos as $photo)
                        <li>
                            <img src="{{ $photo->url }}" alt="{{ $photo->caption }}" loading="lazy">
                            <form method="POST" action="{{ route('site-content.photos.update', $photo) }}" class="cms-photo-caption">
                                @csrf @method('PUT')
                                <label class="sr-only" for="cap-{{ $photo->id }}">Caption</label>
                                <input id="cap-{{ $photo->id }}" name="caption" maxlength="200" required value="{{ $photo->caption }}">
                                <button type="submit" class="cms-link">Save caption</button>
                            </form>
                            <div class="cms-photo-actions">
                                <form method="POST" action="{{ route('site-content.photos.move', $photo) }}">
                                    @csrf <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="cms-link" @disabled($loop->first) aria-label="Move earlier">Move earlier</button>
                                </form>
                                <form method="POST" action="{{ route('site-content.photos.move', $photo) }}">
                                    @csrf <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="cms-link" @disabled($loop->last) aria-label="Move later">Move later</button>
                                </form>
                                <form method="POST" action="{{ route('site-content.photos.destroy', $photo) }}" onsubmit="return confirm('Remove this photo from the homepage?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="cms-link cms-link--danger">Remove</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="cms-empty">No campus photos. The campus section is hidden until you add one.</li>
                    @endforelse
                </ol>
            </section>

        <form method="POST" action="{{ route('site-content.update') }}" class="cms" id="page-text">
            @csrf
            @method('PUT')
            <h3 class="cms-h cms-h--outside">Page text</h3>
            <p class="cms-help">Leave a box empty to go back to the original text.</p>

            @foreach (collect($fields)->groupBy(fn ($f) => $f[3], true) as $group => $items)
                <fieldset class="cms-group">
                    <legend>{{ $group }}</legend>
                    @foreach ($items as $key => [$label, $default, $rows])
                        <label class="cms-field">
                            <span>{{ $label }}</span>
                            @if ($rows > 1)
                                <textarea name="{{ $key }}" rows="{{ $rows }}">{{ old($key, $values[$key]) }}</textarea>
                            @else
                                <input type="{{ $key === 'email' ? 'email' : ($key === 'facebook' ? 'url' : 'text') }}" name="{{ $key }}" value="{{ old($key, $values[$key]) }}">
                            @endif
                            @error($key) <small class="cms-error">{{ $message }}</small> @enderror
                        </label>
                    @endforeach
                </fieldset>
            @endforeach

            <div class="cms-actions">
                <button type="submit">Save page text</button>
            </div>
        </form>
        </div>
    </div>
</x-app-layout>
