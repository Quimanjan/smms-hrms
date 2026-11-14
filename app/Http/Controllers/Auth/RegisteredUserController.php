<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('welcome', [
            'homepageBannerSlides' => Banner::slidesFor('homepage'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:75'],
            'middle_name' => ['nullable', 'string', 'max:75'],
            'last_name' => ['required', 'string', 'max:75'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $nameParts = array_filter([
            trim($data['first_name']),
            trim($data['middle_name'] ?? ''),
            trim($data['last_name']),
        ], fn (string $part): bool => $part !== '');
        $name = implode(' ', $nameParts);

        if (filled($data['suffix'] ?? null)) {
            $name .= ', '.trim($data['suffix']);
        }

        $user = User::create([
            'name' => $name,
            'email' => $data['email'],
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('Applicant');

        event(new Registered($user));

        if ($request->input('registration_context') === 'job-openings') {
            return redirect()
                ->route('public.job-openings')
                ->with('registration_success', true)
                ->with('registered_email', $user->email);
        }

        return redirect()
            ->route('login')
            ->with('registration_success', true)
            ->with('registered_email', $user->email);
    }
}
