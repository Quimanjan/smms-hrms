{{--
    Log in, Sign up and Apply now modals for the public pages (job openings list and a job's own page).
    Usage: @include('partials.public-account-modals', ['context' => 'job-openings'])
    Buttons: [data-open-apply-modal] opens the apply choice, [data-open-signup] opens Create your account,
    #jobs-login-trigger and #jobs-signup-trigger are the header buttons.
--}}
@php(view()->share("errors", $errors ?? new \Illuminate\Support\ViewErrorBag))
<style>
    :root { --primary: #b22222; }
    .modal-overlay {
        position: fixed;
        z-index: 100;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(17, 17, 17, .4);
        backdrop-filter: blur(8px);
    }

    .modal-overlay[hidden] { display: none; }
    .modal-card {
        position: relative;
        width: min(100%, 460px);
        border: 1px solid rgba(178, 34, 34, .2);
        border-radius: 20px;
        padding: 32px 24px 24px;
        background: linear-gradient(135deg, #fff 0%, #fff8f8 100%);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .2);
        text-align: center;
    }
    #signup-modal.job-openings-registration .modal-card {
        width: min(100%, 680px);
        max-height: min(850px, calc(100vh - 32px));
        max-height: min(850px, calc(100dvh - 32px));
        overflow-y: auto;
        overscroll-behavior: contain;
    }
    .signup-name-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 16px;
    }
    .signup-name-grid .form-group { min-width: 0; }
    .modal-close {
        position: absolute;
        top: 12px;
        right: 14px;
        border: 0;
        background: transparent;
        color: var(--primary);
        font-size: 26px;
        cursor: pointer;
    }
    .modal-card h2 { margin: 0 0 22px; color: var(--primary); font-size: 24px; }
    .modal-choice {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 12px;
        border: 1px solid rgba(178, 34, 34, .14);
        border-radius: 14px;
        padding: 14px 16px;
        background: #fff5f5;
    }
    .modal-choicetext { flex: 1; text-align: left; }
    .modal-choicetext p { margin: 0 0 4px; color: #222; font-weight: 700; line-height: 1.3; }
    .modal-choicetext span { display: block; color: #777; font-size: 13px; line-height: 1.4; }
    .modal-btn {
        display: inline-flex;
        min-width: 92px;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 999px;
        padding: 10px 16px;
        color: #fff;
        background: var(--primary);
        font: inherit;
        font-weight: 700;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease, color .2s ease;
    }
    .modal-btn.secondary { border: 1px solid var(--primary); color: #fff; background: var(--primary); }
    .modal-btn:not(:disabled):hover {
        transform: translateY(-2px);
        color: var(--primary);
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 8px 20px rgba(178, 34, 34, .35);
    }
    .modal-btn:not(:disabled):active {
        transform: translateY(0);
        color: var(--primary);
        border-color: #fff5f5;
        background: #fff5f5;
        box-shadow: 0 4px 8px rgba(178, 34, 34, .15);
    }
    .modal-btn.is-disabled, .modal-btn:disabled {
        opacity: .5;
        cursor: not-allowed;
        background: #999;
        border-color: #999;
        color: #fff;
    }

    .login-modal {
        position: fixed;
        z-index: 110;
        inset: 0;
        display: none;
        align-items: flex-start;
        justify-content: center;
        overflow-y: auto;
        padding: 60px 16px 24px;
        background: rgba(0, 0, 0, .4);
        backdrop-filter: blur(8px);
        cursor: pointer;
    }
    .login-modal.active { display: flex; }
    .login-modal-content {
        position: relative;
        width: 100%;
        max-width: 420px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 10px 40px rgba(0, 0, 0, .2);
        cursor: auto;
    }
    .login-modal-close {
        position: absolute;
        z-index: 1;
        top: 10px;
        right: 16px;
        display: grid;
        width: 32px;
        height: 32px;
        place-items: center;
        color: #999;
        font-size: 32px;
        cursor: pointer;
    }
    .login-modal-inner { padding: 40px 24px 24px; }
    .login-header { margin-bottom: 24px; text-align: center; }
    .login-header h2 { margin: 0; color: var(--primary); font-size: 24px; }
    .form-group { margin-bottom: 16px; text-align: left; }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #333;
        font-size: 13px;
        font-weight: 600;
    }
    .form-group input {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 12px;
        font: inherit;
        font-size: 13px;
    }
    .form-group input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(178, 34, 34, .1); }
    .form-group input::placeholder { color: #999; }
    .login-modal-inner form.form-group > div + div { margin-top: 14px; }
    .login-modal-inner form.form-group > br { display: none; }
    .password-input-wrap { position: relative; }
    .password-input-wrap input { padding-right: 46px; }
    .password-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        display: inline-flex;
        width: 28px;
        height: 28px;
        align-items: center;
        justify-content: center;
        transform: translateY(-50%);
        border: 0;
        padding: 0;
        color: #777;
        background: transparent;
        cursor: pointer;
    }
    .password-toggle svg { width: 20px; height: 20px; }
    .login-submit {
        width: 100%;
        justify-content: center;
        border: 0;
        border-radius: 8px;
        padding: 12px;
        color: #fff;
        background: var(--primary);
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }
    .login-submit:disabled { opacity: .5; cursor: not-allowed; }
    .login-note { margin-top: 14px; color: #444; font-size: 13px; text-align: center; }
    .login-note a { color: var(--primary); font-weight: 700; text-decoration: none; }
    .login-warning {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
        border: 1px solid #fca5a5;
        border-left: 4px solid var(--primary);
        border-radius: 8px;
        padding: 12px 14px;
        color: #7f1d1d;
        background: #fef2f2;
        text-align: left;
    }
    .login-warning-icon { width: 22px; height: 22px; flex-shrink: 0; }
    .login-warning-text { display: flex; flex-direction: column; font-size: 13px; line-height: 1.4; }
    .login-warning-text strong { font-size: 14px; }
    .form-row { display: flex; justify-content: flex-end; margin-top: 16px; }
    .forgot-link { color: var(--primary); font-size: 13px; text-decoration: none; }
    .login-modal-inner form.form-group > div:last-child { margin-top: 14px; }
    .password-requirements {
        margin-top: 10px;
        border: 1px solid rgba(0, 0, 0, .06);
        border-radius: 10px;
        padding: 10px 12px;
        color: #222;
        background: #fff;
        font-size: 13px;
    }
    .password-requirements .requirement { display: flex; align-items: center; gap: 10px; margin: 6px 0; }
    .password-requirements .dot { position: relative; display: inline-block; width: 12px; height: 12px; flex-shrink: 0; border: 2px solid #111; border-radius: 50%; }
    .password-requirements .requirement.met .dot { border-color: #2ecc71; background: #2ecc71; }
    .password-requirements .requirement.met .dot::after { position: absolute; top: 50%; left: 50%; color: #fff; content: '\2713'; font-size: 11px; transform: translate(-50%, -52%); }
    .password-requirements .pr-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .password-requirements .requirement.met .text { color: #2ecc71; font-weight: 600; }
    .error-text { display: none; margin-top: 8px; color: #b30000; font-size: .85rem; }
    #registration-success-modal {
        z-index: 120;
        align-items: center;
        overflow-y: auto;
    }
    #registration-success-modal .registration-success-card {
        width: min(100%, 440px);
        max-height: calc(100vh - 32px);
        overflow-y: auto;
        margin: auto;
        padding: 34px 28px 28px;
    }
    .registration-success-mark {
        display: grid;
        width: 54px;
        height: 54px;
        margin: 0 auto 16px;
        place-items: center;
        border-radius: 50%;
        color: var(--primary);
        background: rgba(178, 34, 34, .1);
        font-size: 30px;
        font-weight: 800;
    }
    #registration-success-modal .registration-success-card h2 { margin-bottom: 10px; }
    #registration-success-message { margin: 0; color: #555; line-height: 1.6; overflow-wrap: anywhere; }
    .registration-continue { width: 100%; margin-top: 24px; }
    @media (max-width: 700px) {
        .login-modal { padding: 24px 12px; }
        .login-modal-inner { padding: 32px 20px 20px; }
        .modal-card { max-height: calc(100dvh - 32px); overflow-y: auto; padding: 30px 18px 20px; }
    }
    @media (max-width: 540px) {
        .signup-name-grid { grid-template-columns: 1fr; }
    }
</style>

<x-apply-now-modal />
<x-apply-login-modal :login-context="$context" />
<x-registration-modal :registration-context="$context" />
@if (session('registration_success'))
    <div class="modal-overlay registration-success-overlay" id="registration-success-modal" role="presentation">
        <section class="modal-card registration-success-card" role="dialog" aria-modal="true"
            aria-labelledby="registration-success-title" aria-describedby="registration-success-message">
            <div class="registration-success-mark" aria-hidden="true">✓</div>
            <h2 id="registration-success-title">Account created</h2>
            <p id="registration-success-message">
                We sent a verification link to <strong>{{ session('registered_email') }}</strong>.
                Verify your email before signing in to complete your application.
            </p>
            <button class="modal-btn primary registration-continue" type="button" id="registration-continue">
                Continue to login
            </button>
        </section>
    </div>
@endif

<script>
(() => {
    const applyModal = document.getElementById('apply-modal');
    const applyModalClose = applyModal?.querySelector('.modal-close');
    const applyLoginModal = document.getElementById('apply-login-modal');
    const signupModal = document.getElementById('signup-modal');
    const signupForm = document.getElementById('signup-form');
    const applyLoginForm = document.getElementById('apply-login-form');
    const applyLoginBtn = document.getElementById('apply-login-btn');
    const signupOpenBtn = document.getElementById('signup-open-btn');
    const applyLoginSignupLink = document.getElementById('apply-login-signup-link');
    const signupPassword = document.getElementById('signup-password');
    const signupConfirmPassword = document.getElementById('signup-confirm-password');
    const signupErrorMessage = document.getElementById('signup-error-message');
    const signupRequirements = document.getElementById('password-requirements');
    const signupCount = document.getElementById('password-count');
    const signupSubmit = signupForm?.querySelector('button[type="submit"]');
    const registrationSuccessModal = document.getElementById('registration-success-modal');
    const registrationContinueButton = document.getElementById('registration-continue');

    function openApplyLoginModal() {
        if (!applyLoginModal) return;
        applyLoginModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeApplyLoginModal() {
        if (!applyLoginModal) return;
        applyLoginModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function openSignupModal() {
        if (!signupModal) return;
        signupModal.hidden = false;
        document.body.style.overflow = 'hidden';
        signupPassword?.focus();
    }

    function closeSignupModal() {
        if (!signupModal) return;
        signupModal.hidden = true;
        document.body.style.overflow = '';
    }

    window.closeApplyLoginModal = closeApplyLoginModal;
    window.closeSignupModal = closeSignupModal;

    document.getElementById('jobs-login-trigger')?.addEventListener('click', openApplyLoginModal);
    document.getElementById('jobs-signup-trigger')?.addEventListener('click', openSignupModal);
    document.querySelectorAll('[data-open-signup]').forEach((button) => button.addEventListener('click', openSignupModal));

    function closeApplyModal() {
        if (!applyModal) return;
        applyModal.hidden = true;
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-open-apply-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!applyModal) return;
            applyModal.hidden = false;
            document.body.style.overflow = 'hidden';
            applyModal.querySelector('.modal-close')?.focus();
        });
    });

    applyModalClose?.addEventListener('click', closeApplyModal);
    applyModal?.addEventListener('click', (event) => {
        if (event.target === applyModal) closeApplyModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && applyModal && !applyModal.hidden) closeApplyModal();
        if (event.key === 'Escape' && applyLoginModal?.classList.contains('active')) closeApplyLoginModal();
        if (event.key === 'Escape' && signupModal && !signupModal.hidden) closeSignupModal();
    });

    applyLoginModal?.querySelector('.login-modal-content')?.addEventListener('click', (event) => event.stopPropagation());
    applyLoginBtn?.addEventListener('click', () => {
        closeApplyModal();
        openApplyLoginModal();
    });
    signupOpenBtn?.addEventListener('click', () => {
        closeApplyModal();
        openSignupModal();
    });
    applyLoginSignupLink?.addEventListener('click', (event) => {
        event.preventDefault();
        closeApplyLoginModal();
        openSignupModal();
    });
    signupModal?.querySelector('.modal-close')?.addEventListener('click', closeSignupModal);
    signupModal?.addEventListener('click', (event) => {
        if (event.target === signupModal) closeSignupModal();
    });
    registrationContinueButton?.addEventListener('click', () => {
        if (registrationSuccessModal) registrationSuccessModal.remove();
        document.body.style.overflow = '';
        openApplyLoginModal();
        document.getElementById('apply-email')?.focus();
    });
    if (registrationSuccessModal) document.body.style.overflow = 'hidden';

    const passwordRules = [
        ['length', (password) => password.length >= 12],
        ['capital', (password) => /[A-Z]/.test(password)],
        ['number', (password) => /\d/.test(password)],
        ['symbol', (password) => /[^A-Za-z0-9]/.test(password)],
    ];

    function updateSignupPassword() {
        if (!signupPassword || !signupRequirements) return;
        const password = signupPassword.value;
        let metCount = 0;
        passwordRules.forEach(([key, test]) => {
            const requirement = signupRequirements.querySelector(`[data-requirement="${key}"]`);
            const met = test(password);
            requirement?.classList.toggle('met', met);
            if (met) metCount++;
        });
        if (signupCount) signupCount.textContent = `(${metCount}/${passwordRules.length})`;
        if (signupSubmit) signupSubmit.disabled = metCount !== passwordRules.length;
    }

    function checkSignupPasswords() {
        updateSignupPassword();
        if (!signupPassword || !signupConfirmPassword || !signupErrorMessage) return;
        signupErrorMessage.style.display = signupConfirmPassword.value
            && signupPassword.value !== signupConfirmPassword.value ? 'block' : 'none';
    }

    signupPassword?.addEventListener('input', checkSignupPasswords);
    signupConfirmPassword?.addEventListener('input', checkSignupPasswords);
    signupSubmit?.addEventListener('click', (event) => {
        checkSignupPasswords();
        if (signupConfirmPassword?.value && signupPassword?.value !== signupConfirmPassword.value) {
            event.preventDefault();
        }
    });

    document.querySelectorAll('.password-toggle').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const input = document.getElementById(toggle.dataset.target);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            const visible = input.type === 'text';
            toggle.setAttribute('aria-pressed', String(visible));
            toggle.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
        });
    });

    if (applyLoginForm?.dataset.hasErrors === 'true') openApplyLoginModal();
    if (signupForm?.dataset.hasErrors === 'true') openSignupModal();
    if (signupForm) {
        signupSubmit && (signupSubmit.disabled = true);
        updateSignupPassword();
    }

})();
</script>
