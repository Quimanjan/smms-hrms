{{-- data-has-errors lets HOMEPAGEScript.js reopen the modal after a failed server validation --}}
<form
    method="POST"
    action="{{ route('register-create') }}"
    id="signup-form"
    class="signup-form"
    data-has-errors="{{ $errors->hasAny(['first_name', 'middle_name', 'last_name', 'suffix', 'email', 'password', 'password_confirmation']) ? 'true' : 'false' }}"
>
    @csrf
    @if (filled($registrationContext ?? null))
        <input type="hidden" name="registration_context" value="{{ $registrationContext }}">
    @endif

    <div class="signup-name-grid">
        <div class="form-group">
            <x-input-label for="signup-first-name" :value="__('First Name')" />

            <x-text-input
                id="signup-first-name"
                class="block mt-1 w-full"
                type="text"
                name="first_name"
                placeholder="Enter your first name"
                :value="old('first_name')"
                required
                maxlength="75"
                autocomplete="given-name"
            />

            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="signup-middle-name" :value="__('Middle Name (optional)')" />

            <x-text-input
                id="signup-middle-name"
                class="block mt-1 w-full"
                type="text"
                name="middle_name"
                placeholder="Enter your middle name"
                :value="old('middle_name')"
                maxlength="75"
                autocomplete="additional-name"
            />

            <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="signup-last-name" :value="__('Last Name')" />

            <x-text-input
                id="signup-last-name"
                class="block mt-1 w-full"
                type="text"
                name="last_name"
                placeholder="Enter your last name"
                :value="old('last_name')"
                required
                maxlength="75"
                autocomplete="family-name"
            />

            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="signup-suffix" :value="__('Suffix (optional)')" />

            <x-text-input
                id="signup-suffix"
                class="block mt-1 w-full"
                type="text"
                name="suffix"
                placeholder="e.g. Jr., Sr., III"
                :value="old('suffix')"
                maxlength="20"
                autocomplete="honorific-suffix"
            />

            <x-input-error :messages="$errors->get('suffix')" class="mt-2" />
        </div>
    </div>

    <!-- Email Address -->
    <div class="form-group">
        <x-input-label for="signup-email" :value="__('Email Address')" />

        <x-text-input
            id="signup-email"
            class="block mt-1 w-full"
            type="email"
            name="email"
            placeholder="Enter your email address"
            :value="old('email')"
            required
            autocomplete="username"
        />

        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Password -->
    <div class="form-group">
        <x-input-label for="signup-password" :value="__('Password')" />

        <div class="password-input-wrap">
            <x-text-input
                id="signup-password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                placeholder="Create a password"
                required
                autocomplete="new-password"
            />

           {{-- Eye icon is written here so it always shows; the JS swaps it on click --}}
            <button
                class="password-toggle"
                type="button"
                data-target="signup-password"
                aria-label="Show password"
                aria-pressed="false"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                          fill="none" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 17l-10-10"
                          fill="none" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

        </div>

        <x-input-error :messages="$errors->get('password')" class="mt-2" />

        <!-- Password Requirements -->
        <div id="password-requirements" class="password-requirements mt-3" aria-live="polite">
            <div class="pr-header">
                <strong>Password requirements</strong>
                <span id="password-count">(0/4)</span>
            </div>

            <div class="requirement" data-requirement="length">
                <span class="dot"></span>
                <span class="text">Minimum 12 characters</span>
            </div>

            <div class="requirement" data-requirement="capital">
                <span class="dot"></span>
                <span class="text">One capitalized letter</span>
            </div>

            <div class="requirement" data-requirement="number">
                <span class="dot"></span>
                <span class="text">One number</span>
            </div>

            <div class="requirement" data-requirement="symbol">
                <span class="dot"></span>
                <span class="text">At least one special character (e.g. !@#$%^&*)</span>
            </div>
        </div>
    </div>

    <!-- Confirm Password -->
    <div class="form-group">
        <x-input-label for="signup-confirm-password" :value="__('Confirm Password')" />

        <div class="password-input-wrap">
            <x-text-input
                id="signup-confirm-password"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                placeholder="Confirm your password"
                required
                autocomplete="new-password"
            />

              <button
                class="password-toggle"
                type="button"
                data-target="signup-confirm-password"
                aria-label="Show password"
                aria-pressed="false"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                          fill="none" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 17l-10-10"
                          fill="none" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>

        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

        <span id="signup-error-message" class="error-text" hidden>
            Passwords do not match
        </span>
    </div>

    <!-- Submit -->
    <div class="form-group mt-4">
        <x-primary-button class="login-submit w-full justify-center">
            {{ __('Submit') }}
        </x-primary-button>
    </div>
</form>