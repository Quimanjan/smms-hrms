@extends('layouts.home')

@section('title', 'Home | St. Mary Magdalene School')

{{-- STYLES: everything from HOMEPAGEStyle.css, inlined --}}
@section('styles')
@verbatim
<style>
:root {
    --primary: #b22222;
    --primary-contrast: #f7f7f7;
    --muted: #f7f7f7;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    height: 100%;
    margin: 0;
    font-family: Inter, system-ui, Segoe UI, Roboto, 'Helvetica Neue', Arial;
    color: #222;
    background: var(--muted);
}

body {
    max-width: 1920px;
    height: auto;
    min-height: 100vh;
    margin: 0 auto;
    padding-top: 90px;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 48px 20px 20px;
    background: transparent linear-gradient(to bottom, rgba(229, 228, 228, 0.977), rgba(255, 255, 255, 0.977));
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 30;
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.08);
    max-width: 1920px;
    margin: 0 auto;
}

.brand-link {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

.brand-logo {
    width: 60px;
    height: 60px;
    object-fit: contain;
    flex-shrink: 0;
}

.brand {
    font-weight: 800;
    color: var(--primary);
    font-size: clamp(18px, 2.2vw, 30px);
    line-height: 1.2;
}

.brand-link:hover .brand {
    color: #8b1a1a;
}

.top-actions {
    display: flex;
    gap: 14px;
    align-items: center;
}

.menu-toggle {
    display: none;
    flex-direction: column;
    justify-content: center;
    gap: 4px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
}

.menu-toggle span {
    display: block;
    width: 24px;
    height: 2px;
    background: var(--primary);
    border-radius: 999px;
    transition: transform 0.25s ease, opacity 0.25s ease;
}

.menu-toggle.active span:nth-child(1) { transform: translateY(6px) rotate(45deg); }
.menu-toggle.active span:nth-child(2) { opacity: 0; }
.menu-toggle.active span:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }

.top-btn {
    display: inline-block;
    text-align: center;
    font-family: inherit;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    color: var(--primary);
    border: 1px solid var(--primary);
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.top-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    transition: left 0.3s ease;
    z-index: -1;
}

.top-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(178, 34, 34, 0.25);
}

.top-btn:active {
    transform: translateY(0);
    box-shadow: 0 4px 8px rgba(178, 34, 34, 0.15);
}

.top-btn.primary {
    background: var(--primary);
    color: var(--primary-contrast);
    border: 1px solid var(--primary);
}

.top-btn.primary:hover {
    background: #ffffff;
    color: var(--primary);
    border-color: #ffffff;
    box-shadow: 0 8px 20px rgba(178, 34, 34, 0.35);
}

.top-btn.primary:active {
    background: #f7f7f7;
    color: var(--primary);
    border-color: #f7f7f7;
}

/* ---------- Banner ---------- */
.banner {
    position: relative;
    overflow: hidden;
    height: 460px;
    background: #ffffff;
}

.banner-pause {
    position: absolute;
    top: 18px;
    right: 24px;
    z-index: 20;
    display: grid;
    width: 42px;
    height: 42px;
    place-items: center;
    border: 1px solid rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    padding: 0;
    color: var(--primary);
    background: rgba(255, 255, 255, 0.92);
    cursor: pointer;
}

.banner-pause svg { width: 18px; height: 18px; }
.banner-pause[hidden] { display: none; }

.banner .container {
    width: 100%;
    margin: 0 auto;
    position: relative;
    height: 100%;
    padding: 0 24px;
}

.slider {
    position: absolute;
    inset: 0;
}

.slide {
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 1s ease;
    display: flex;
}

.slide img {
    display: flex;
    height: 100%;
    width: 100%;
    object-fit: cover;
    object-position: center center;
    background: var(--muted);
    filter: brightness(0.82) contrast(0.96);
    transform-origin: center center;
    transition: transform 0.9s ease;
    will-change: transform;
}

.slide.active { opacity: 1; }

.slide--center img { object-position: center center; }
.slide--top img { object-position: center top; }
.slide--left img { object-position: left center; }
.slide--right img {
    object-position: 70% center;
    transform-origin: 70% center;
}

.banner-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: flex-start;
    justify-content: flex-start;
}

.banner-content {
    width: calc(100% - 64px);
    margin-left: 64px;
    color: var(--primary-contrast);
    z-index: 10;
    padding-top: 80px;
}

.banner-content h1 {
    font-size: clamp(28px, 5vw, 48px);
    margin: 0 0 12px 0;
    letter-spacing: 0.2px;
}

.banner-content p {
    margin: 0 0 18px 0;
    font-size: clamp(14px, 2vw, 18px);
    opacity: 0.95;
}

.banner-content h1, .banner-content p {
    text-shadow: 0 6px 22px rgba(0,0,0,0.6);
}

.cta {
    display: flex;
    gap: 14px;
}

.btn {
    display: inline-block;
    padding: 12px 22px;
    border-radius: 8px;
    text-decoration: none;
    background: transparent;
    color: var(--primary-contrast);
    border: 2px solid rgba(255,255,255,0.9);
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(4px);
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
    z-index: -1;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn:hover {
    transform: scale(1.05);
    border-color: rgba(255,255,255,1);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.btn:active { transform: scale(0.98); }

.btn.primary {
    background: var(--primary);
    border-color: var(--primary);
}

.btn.primary:hover {
    background: #8b1a1a;
    border-color: #8b1a1a;
    box-shadow: 0 10px 25px rgba(139, 26, 26, 0.4);
    transform: scale(1.08);
}

.btn.primary:active {
    transform: scale(0.95);
    background: #6b1515;
}

.dots {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: 22px;
    display: flex;
    gap: 8px;
    z-index: 20;
}

.dots button {
    width: 11px;
    height: 11px;
    border: 0;
    border-radius: 999px;
    background: var(--primary-contrast);
    cursor: pointer;
    transition: transform 0.18s ease, background-color 0.18s ease, width 0.18s ease;
}

.dots button.active {
    width: 15px;
    background: var(--primary);
}

.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
    background: transparent;
    color: #1a1a1a;
    padding: 20px;
}

/* ---------- History ---------- */
.history {
    margin-top: 20px;
    padding: 60px 20px;
    background: var(--primary-contrast);
}

.history .container {
    width: 100%;
    margin: 0 auto;
}

.history h1, .facilities h1 {
    color: #b22222;
    text-align: center;
    font-size: clamp(32px, 5vw, 50px);
    margin-bottom: 30px;
    font-weight: 700;
}

.history .history-content {
    width: 100%;
    padding: 40px 20px;
    border: 1px solid rgba(178, 34, 34, 0.2);
    border-radius: 20px;
    background: #ffffff;
    color: #3b3b3b;
    line-height: 1.8;
    text-align: justify;
    box-shadow: 0px 20px 20px rgba(0,0,0,0.08);
}

.history .history-content p {
    margin: 0 0 18px 0;
    font-size: 16px;
}

/* ---------- Facilities ---------- */
.facilities {
    margin-top: 20px;
    padding: 60px 20px;
    background: #f7f7f7;
}

.facilities .container {
    width: 100%;
    margin: 0 auto;
}

.facilities h2 {
    color: #3f3fdc;
    text-align: center;
    font-size: clamp(24px, 4vw, 34px);
    margin-bottom: 28px;
    font-weight: 700;
}

.fac-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    justify-items: center;
    align-items: stretch;
    padding: 0 10px;
}

.fac-item {
    width: 100%;
    max-width: 420px;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 50px 50px rgba(0,0,0,0.08);
    cursor: pointer;
}

.fac-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.fac-item:hover img { transform: scale(1.50); }

.FacilitiesModal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    -webkit-backdrop-filter: blur(8px); /* Safari */
    backdrop-filter: blur(8px);
    z-index: 100;
    justify-content: center;
    align-items: center;
    cursor: pointer;
}

.FacilitiesModal.active { display: flex; }

.FacilitiesModal-img {
    max-width: 90%;
    max-height: 90%;
    border-radius: 6px;
    box-shadow: 0 0 30px rgba(0,0,0,0.8);
}

.FacilitiesModal-close {
    position: absolute;
    top: 20px;
    right: 30px;
    font-size: 40px;
    font-weight: bold;
    color: white;
    cursor: pointer;
    z-index: 101;
    transition: color 0.3s ease;
}

.FacilitiesModal-close:hover { color: #b22222; }

/* ---------- Password requirements ---------- */
.password-requirements {
    margin-top: 10px;
    padding: 10px 12px;
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.06);
    border-radius: 10px;
    font-size: 13px;
    color: #222;
}

.password-requirements .requirement {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 6px 0;
}

.password-requirements .dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: transparent;
    border: 2px solid #111;
    display: inline-block;
    flex-shrink: 0;
    position: relative;
}

.password-requirements .requirement.met .dot {
    background: #2ecc71;
    border-color: #2ecc71;
    box-shadow: 0 0 0 4px rgba(46,204,113,0.08);
}

.password-requirements .dot::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -52%);
    font-size: 11px;
    color: white;
}

.password-requirements .requirement.met .dot::after { content: '\2713'; }

.password-requirements .text { line-height: 1.1; }

.password-requirements .pr-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 13px;
}

.password-requirements .requirement.met .text {
    color: #2ecc71;
    font-weight: 600;
}

.signup-form button[type="submit"]:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.error-text {
    color: #b30000;
    display: none;
    margin-top: 8px;
    font-size: 0.85rem;
    font-family: sans-serif;
}

/* ---------- Footer ---------- */
.footer {
    background: var(--primary);
    padding: 40px 20px 30px;
    text-align: center;
}

.footer-container {
    width: 100%;
    margin: 0 auto;
}

.footer-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.contact-info {
    display: flex;
    gap: 12px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.contact-link {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 9px;
    text-decoration: none;
    width: auto;
    min-height: 50px;
    max-width: 100%;
    border-radius: 999px;
    padding: 8px 14px;
    background: rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.4);
    color: var(--primary-contrast);
    font-size: 13px;
    font-weight: 600;
    text-align: left;
    overflow-wrap: anywhere;
}

.contact-link span {
    color: var(--primary-contrast);
}

.contact-link:hover {
    transform: scale(1.15);
    background: rgba(255, 255, 255, 0.35);
    border-color: rgba(255, 255, 255, 0.7);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 1;
}

.contact-info:has(.contact-link:hover) .contact-link:not(:hover) {
    transform: scale(0.9);
    opacity: 0.8;
}

.contact-link:active { transform: scale(0.95); }

.contact-icon {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-contrast);
    font-weight: 700;
    stroke: var(--primary-contrast);
}

.footer-copyright {
    flex: 1;
    min-width: 200px;
}

.footer-copyright p {
    color: var(--primary-contrast);
    font-size: 13px;
    font-weight: 600;
}

/* ---------- Apply / Sign-up modal ---------- */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(17, 17, 17, 0.4);
    -webkit-backdrop-filter: blur(8px); /* Safari */
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 100;
}

.modal-overlay[hidden] { display: none; }

.modal-card {
    position: relative;
    width: min(100%, 460px);
    background: linear-gradient(135deg, #ffffff 0%, #fff8f8 100%);
    border: 1px solid rgba(178, 34, 34, 0.2);
    border-radius: 20px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    padding: 32px 24px 24px;
    text-align: center;
}

/* Same two-column Create your account window as the job pages */
#signup-modal .modal-card {
    width: min(100%, 680px);
    max-height: min(850px, calc(100vh - 32px));
    max-height: min(850px, calc(100dvh - 32px));
    overflow-y: auto;
    overscroll-behavior: contain;
}
.signup-name-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); column-gap: 16px; }
.signup-name-grid .form-group { min-width: 0; }
@media (max-width: 540px) { .signup-name-grid { grid-template-columns: 1fr; } }

.modal-close {
    position: absolute;
    top: 12px;
    right: 14px;
    border: none;
    background: transparent;
    color: var(--primary);
    font-size: 26px;
    cursor: pointer;
}

.modal-card h2 {
    color: var(--primary);
    margin-bottom: 22px;
    font-size: 24px;
}

.signup-form { text-align: left; }

.password-input-wrap { position: relative; }
.password-input-wrap input { padding-right: 46px; }

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: none;
    background: transparent;
    color: #777;
    cursor: pointer;
    padding: 0;
}

.password-toggle:hover { color: var(--primary); }
.password-toggle svg { width: 20px; height: 20px; }

.modal-choice {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px 16px;
    margin-bottom: 12px;
    border: 1px solid rgba(178, 34, 34, 0.14);
    border-radius: 14px;
    background: #f7f7f7;
}

.modal-choicetext {
    text-align: left;
    flex: 1;
}

.modal-choicetext p {
    color: #222;
    margin: 0 0 4px;
    font-weight: 700;
    line-height: 1.3;
}

.modal-choicetext span {
    display: block;
    color: #777;
    font-size: 13px;
    line-height: 1.4;
}

.modal-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 16px;
    min-width: 92px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 700;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

button.modal-btn { font-family: inherit; font-size: inherit; cursor: pointer; }

.login-status {
    margin-bottom: 16px;
    padding: 10px 12px;
    border: 1px solid #a3a1a1;
    border-radius: 8px;
    background: #fff5f5;
    color: #353434;
    font-size: 13px;
    line-height: 1.5;
    text-align: left;
}

/* Green flash for success (e.g. email verified) */
.login-status--success {
    border-color: #bbf7d0;
    background: #f0fdf4;
    color: #166534;
}

.modal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(178, 34, 34, 0.25);
}

.modal-btn:active {
    transform: translateY(0);
    box-shadow: 0 4px 8px rgba(178, 34, 34, 0.15);
}

.modal-btn.primary,
.modal-btn.secondary {
    background: var(--primary);
    color: var(--primary-contrast);
    border: 1px solid var(--primary);
}

.modal-btn.primary:hover,
.modal-btn.secondary:hover {
    background: var(--primary-contrast);
    color: var(--primary);
    border-color: var(--primary-contrast);
    box-shadow: 0 8px 20px rgba(178, 34, 34, 0.35);
}

.modal-btn.primary:active,
.modal-btn.secondary:active {
    background: #f7f7f7;
    color: var(--primary);
    border-color: #f7f7f7;
}

.form-row {
    display: flex;
    align-items: center;
    justify-content: end;
    gap: 12px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}

/* FIX: removed leftover debug "background-color: red" */
.remember-me {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13px;
    color: #444;
}

.remember-me input { accent-color: var(--primary); }

.forgot-link {
    color: var(--primary);
    text-decoration: none;
    font-size: 13px;
}

.forgot-link:hover { text-decoration: underline; }

.login-note {
    margin-top: 14px;
    text-align: center;
    font-size: 13px;
    color: #444;
}

.login-note a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 700;
}

.login-note a:hover { text-decoration: underline; }

/* ---------- Login modal ---------- */
.login-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    -webkit-backdrop-filter: blur(8px); /* Safari */
    backdrop-filter: blur(8px);
    z-index: 1000;
    justify-content: center;
    align-items: flex-start;
    padding-top: 60px;
    cursor: pointer;
    overflow-y: auto;
}

.login-modal.active { display: flex; }

.login-modal-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 420px;
    position: relative;
    cursor: auto;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-30px); }
    to   { opacity: 1; transform: translateY(0); }
}

.login-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    font-size: 32px;
    font-weight: bold;
    color: #999;
    cursor: pointer;
    z-index: 1001;
    transition: color 0.3s ease;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-modal-close:hover { color: #b22222; }

.login-modal-inner { padding: 40px 24px 24px; }

.login-header {
    text-align: center;
    margin-bottom: 24px;
}

.login-logo {
    width: 65px;
    height: 65px;
    object-fit: contain;
    margin-bottom: 12px;
}

.login-header h2 {
    color: #b22222;
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.login-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    justify-content: center;
    flex-wrap: wrap;
}

.login-tab {
    min-height: 42px;
    padding: 8px 18px;
    border: 1px solid transparent;
    background: transparent;
    border-radius: 9999px;
    color: #64748b;
    font-weight: 600;
    cursor: pointer;
    transition: color 160ms ease, background-color 160ms ease, border-color 160ms ease;
    font-size: 13px;
}

.login-tab:hover {
    border-color: #fecaca;
    background: #fff5f5;
    color: #7f1d1d;
}

.login-tab.active {
    background: #fee2e2;
    border-color: #fecaca;
    color: #7f1d1d;
}

.login-form { margin-bottom: 18px; }
.form-group { margin-bottom: 16px; }

.form-group label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
    font-size: 13px;
}

.form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 13px;
    font-family: Inter, system-ui, Segoe UI, Roboto, 'Helvetica Neue', Arial;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #b22222;
    box-shadow: 0 0 0 3px rgba(178, 34, 34, 0.1);
}

.form-group input::placeholder { color: #999; }

.login-submit {
    width: 100%;
    padding: 12px;
    background: #b22222;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.login-submit:hover {
    background: #8b1a1a;
    box-shadow: 0 4px 12px rgba(178, 34, 34, 0.3);
}

.login-submit:active { transform: scale(0.98); }

.demo-credentials {
    background: rgba(178, 34, 34, 0.08);
    border: 1px solid rgba(178, 34, 34, 0.2);
    border-radius: 8px;
    padding: 12px;
}

.demo-credentials h3 {
    color: #b22222;
    font-size: 13px;
    font-weight: 700;
    margin: 0 0 6px 0;
}

.demo-credentials p {
    color: #555;
    font-size: 12px;
    line-height: 1.4;
    margin: 0;
}

/* ---------- Login warning (invalid credentials) ---------- */
.login-warning {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 16px;
    padding: 12px 14px;
    border: 1px solid #fca5a5;
    border-left: 4px solid #b22222;
    border-radius: 8px;
    background: #fef2f2;
    color: #7f1d1d;
    text-align: left;
    animation: loginShake 0.4s ease;
}

.login-warning-icon {
    width: 22px;
    height: 22px;
    flex-shrink: 0;
    color: #b22222;
    margin-top: 1px;
}

.login-warning-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    font-size: 13px;
    line-height: 1.4;
}

.login-warning-text strong {
    font-size: 14px;
    font-weight: 700;
}

.form-group input.input-invalid {
    border-color: #b22222;
    background: #fffafa;
}

@keyframes loginShake {
    0%, 100% { transform: translateX(0); }
    20%      { transform: translateX(-6px); }
    40%      { transform: translateX(6px); }
    60%      { transform: translateX(-4px); }
    80%      { transform: translateX(4px); }
}

/* ---------- Shared blurred backdrop for EVERY modal ---------- */
.FacilitiesModal,
.modal-overlay,
.login-modal,
#apply-login-modal,
#apply-modal,
#signup-modal,
#registration-modal,
#login-modal,
#FacilitiesModal {
    -webkit-backdrop-filter: blur(8px); /* Safari */
    backdrop-filter: blur(8px);
}

/* ---------- Media queries ---------- */
@media (max-width: 1024px) {
    .topbar { padding: 20px 24px; }

    .banner { height: 550px; }

    .banner-content {
        width: calc(100% - 32px);
        margin-left: 16px;
        padding-top: 60px;
    }

    .footer-content {
        flex-direction: column;
        gap: 15px;
    }

    .contact-info {
        justify-content: center;
        width: 100%;
    }

    .footer-copyright {
        width: 100%;
        flex: none;
    }
}

@media (max-width: 768px) {
    .menu-toggle { display: flex; }

    .top-actions {
        display: none;
        position: absolute;
        top: calc(100% + 12px);
        right: 24px;
        flex-direction: column;
        align-items: stretch;
        background: var(--primary-contrast);
        padding: 12px;
        border-radius: 10px;
        box-shadow: 0 10px 24px rgba(0,0,0,0.14);
        min-width: 180px;
        z-index: 40;
    }

    .top-actions.open { display: flex; }

    .top-actions .top-btn {
        width: 100%;
        text-align: center;
    }

    .banner { height: 450px; }
    .banner-pause { top: 12px; right: 14px; width: 38px; height: 38px; }

    .footer-content {
        flex-direction: column;
        justify-content: center;
    }

    .contact-info {
        justify-content: center;
        width: 100%;
        gap: 15px;
    }

    .contact-link {
        width: auto;
        min-height: 45px;
        padding: 7px 12px;
    }

    .contact-icon {
        width: 24px;
        height: 24px;
    }

    .footer-copyright {
        width: 100%;
        text-align: center;
    }

    .footer-copyright p { font-size: 12px; }

    .login-modal-content {
        width: 90%;
        margin: 0 auto;
    }

    .login-modal-inner { padding: 32px 20px 20px; }
    .login-header h2 { font-size: 24px; }

    .login-logo {
        width: 60px;
        height: 60px;
    }

    .login-tabs { gap: 8px; }

    .login-tab {
        padding: 8px 14px;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .topbar { padding: 15px 12px; }

    .banner { height: 300px; }
    .banner-pause { top: 10px; right: 10px; width: 36px; height: 36px; }

    .brand { font-size: 16px; }

    .brand-logo {
        width: 45px;
        height: 45px;
    }

    .footer { padding: 25px 15px 20px; }

    .contact-info { gap: 12px; }

    .contact-link {
        min-height: 40px;
        padding: 6px 10px;
        font-size: 12px;
    }

    .contact-icon {
        width: 20px;
        height: 20px;
    }

    .footer-copyright p {
        font-size: 11px;
        line-height: 1.4;
    }

    .modal-card { padding: 28px 18px 20px; }

    .modal-choice {
        flex-direction: column;
        align-items: flex-start;
    }

    .modal-btn { width: 100%; }
}

/* ---------- Extra responsive rules ---------- */
@media (max-width: 1024px) {
    body { height: auto; min-height: 100%; padding-top: 100px; overflow-x: hidden; }
    img, svg, video { max-width: 100%; }
    .history, .facilities { padding: 48px 20px; }
    .history .history-content { padding: 32px 24px; }
    .banner-content h1 { font-size: clamp(24px, 4.5vw, 40px); }
}

@media (max-width: 768px) {
    .topbar { gap: 12px; }
    .brand-link { min-width: 0; flex: 1; }
    .brand {
        font-size: clamp(15px, 4.2vw, 20px);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .top-actions { right: 16px; }
    .top-btn { min-height: 44px; }
    .menu-toggle {
        min-width: 44px;
        min-height: 44px;
        align-items: center;
        flex-shrink: 0;
    }
    .menu-toggle span { margin: 0; }
    .banner .container { padding: 0; }
    .banner-content { margin-left: 16px; padding-top: 32px; }
    .history, .facilities { padding: 40px 16px; margin-top: 0; }
    .history .history-content {
        padding: 24px 18px;
        text-align: left;
        border-radius: 16px;
    }
    .history .history-content p { font-size: 15px; line-height: 1.7; }
    .history h1, .facilities h1 { margin-bottom: 22px; }
    .fac-grid { gap: 16px; padding: 0; }
    .fac-item { max-width: 100%; box-shadow: 0 20px 30px rgba(0,0,0,0.08); }
    .footer { padding: 28px 16px 22px; }
    .login-modal { padding: 24px 12px; align-items: flex-start; }
    .login-modal-content { width: 100%; max-width: 420px; margin: 0 auto; }
    .modal-overlay { padding: 12px; overflow-y: auto; align-items: flex-start; }
    .modal-card { max-height: calc(100vh - 24px); overflow-y: auto; margin: auto; }
    .FacilitiesModal-close { top: 12px; right: 18px; }
}

@media (max-width: 480px) {
    body { padding-top: 76px; }
    .brand { font-size: clamp(13px, 4.4vw, 16px); }
    .top-actions { right: 12px; min-width: 160px; }
    .banner-content { width: calc(100% - 32px); }
    .history h1, .facilities h1 { font-size: 28px; }
    .login-modal-inner { padding: 28px 16px 18px; }
    .login-tabs { gap: 6px; }
    .login-tab { flex: 0 0 auto; text-align: center; }
    .form-row { justify-content: space-between; }
    .FacilitiesModal-img { max-width: 96%; }
}

@media (hover: none) {
    .fac-item:hover img { transform: none; }
}

@media (max-width: 360px) {
    .fac-grid { grid-template-columns: 1fr; }
    .brand-logo { width: 38px; height: 38px; }
    .brand-link { gap: 8px; }
    .modal-card { padding: 24px 14px 18px; }
}

@media (max-height: 520px) and (orientation: landscape) {
    .login-modal { padding-top: 12px; }
    .banner { height: 70vh; }
}

@media (prefers-reduced-motion: reduce) {
    * { animation: none !important; transition: none !important; }
}

#registration-success-modal {
    z-index: 1100;
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
    background: rgba(178, 34, 34, 0.1);
    color: var(--primary);
    font-size: 30px;
    font-weight: 800;
}

#registration-success-modal .registration-success-card h2 {
    margin-bottom: 10px;
}

#registration-success-message {
    margin: 0;
    color: #555;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.registration-continue {
    width: 100%;
    margin-top: 24px;
}
</style>
@endverbatim

{{-- =====================================================================
     Landing page design (2026 revision). The block above still styles the
     login / sign-up / apply modals; everything below styles the page itself.
     ===================================================================== --}}
<link rel="preload" href="{{ asset('fonts/marcellus-400.woff2') }}" as="font" type="font/woff2" crossorigin>
@verbatim
<style>
:root {
    --mg-oxblood: #4a0a12;
    --mg-oxblood-deep: #33060c;
    --mg-crimson: #a5121e;
    --mg-gold: #c9a24b;
    --mg-gold-soft: #e6cf9a;
    --mg-ink: #21171a;
    --mg-stone: #6d6164;
    --mg-mist: #f6f2f1;
    --mg-line: #e6ddda;
    --mg-display: 'Marcellus', 'Times New Roman', serif;
    --mg-body: 'Instrument Sans', system-ui, 'Segoe UI', Roboto, Arial, sans-serif;
    --mg-wrap: 1180px;
    --mg-bar: 72px;
}

html { scroll-behavior: smooth; scroll-padding-top: var(--mg-bar); }

html, body {
    font-family: var(--mg-body);
    color: var(--mg-ink);
    background: #fff;
}

body {
    max-width: none;
    padding-top: var(--mg-bar);
    font-size: 17px;
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
}

.mg-wrap {
    width: min(100% - 48px, var(--mg-wrap));
    margin-inline: auto;
}

.mg-page :focus-visible,
.mg-bar :focus-visible,
.mg-foot :focus-visible {
    outline: 2px solid var(--mg-gold);
    outline-offset: 3px;
    border-radius: 4px;
}

/* ---------- Top bar ---------- */
.mg-bar {
    position: fixed;
    inset: 0 0 auto 0;
    z-index: 50;
    height: var(--mg-bar);
    background: rgba(255, 255, 255, 0.94);
    -webkit-backdrop-filter: saturate(160%) blur(10px);
    backdrop-filter: saturate(160%) blur(10px);
    border-bottom: 1px solid var(--mg-line);
}

.mg-bar-inner {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}

.mg-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--mg-ink);
    text-decoration: none;
    min-width: 0;
}

.mg-brand img { width: 44px; height: 44px; flex: none; }

.mg-brand span {
    font-family: var(--mg-display);
    font-size: 1.22rem;
    letter-spacing: 0.01em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mg-nav {
    display: flex;
    align-items: center;
    gap: 6px;
}

.mg-nav a.mg-nav-link {
    padding: 9px 14px;
    color: var(--mg-ink);
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    border-radius: 999px;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.mg-nav a.mg-nav-link:hover { background: var(--mg-mist); color: var(--mg-crimson); }

.mg-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 0 24px;
    border: 1px solid transparent;
    border-radius: 999px;
    font: 600 0.98rem/1 var(--mg-body);
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}

.mg-btn-crimson { background: var(--mg-crimson); color: #fff; }
.mg-btn-crimson:hover { background: var(--mg-oxblood); }

.mg-btn-gold { background: var(--mg-gold); color: var(--mg-oxblood-deep); }
.mg-btn-gold:hover { background: var(--mg-gold-soft); }

.mg-btn-ghost { border-color: rgba(255, 255, 255, 0.45); color: #fff; background: transparent; }
.mg-btn-ghost:hover { border-color: #fff; background: rgba(255, 255, 255, 0.08); }

.mg-nav .mg-btn { min-height: 40px; padding: 0 20px; margin-left: 8px; }

.mg-bar .menu-toggle {
    display: none;
    width: 44px;
    height: 44px;
    padding: 0;
    border: 1px solid var(--mg-line);
    border-radius: 12px;
    background: #fff;
    cursor: pointer;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.mg-bar .menu-toggle span {
    display: block;
    width: 20px;
    height: 2px;
    margin: 0;
    background: var(--mg-ink);
    border-radius: 2px;
    transition: transform 0.2s ease, opacity 0.2s ease;
}

.mg-bar .menu-toggle.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.mg-bar .menu-toggle.active span:nth-child(2) { opacity: 0; }
.mg-bar .menu-toggle.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* ---------- Hero ---------- */
.mg-hero {
    position: relative;
    overflow: hidden;
    color: #fff;
    background:
        radial-gradient(900px 520px at 78% 30%, rgba(165, 18, 30, 0.55), transparent 70%),
        linear-gradient(180deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
}

.mg-hero-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.08fr) minmax(0, 0.92fr);
    align-items: end;
    gap: clamp(32px, 6vw, 88px);
    padding-top: clamp(56px, 8vw, 104px);
}

.mg-hero-copy { padding-bottom: clamp(56px, 8vw, 104px); }

.mg-hero-place {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 26px;
    color: var(--mg-gold-soft);
    font-size: 0.95rem;
    font-weight: 500;
}

.mg-hero-place::before {
    content: '';
    width: 28px;
    height: 1px;
    background: var(--mg-gold);
}

.mg-hero h1 {
    max-width: 15ch;
    font-family: var(--mg-display);
    font-weight: 400;
    font-size: clamp(2.5rem, 5.2vw, 4.4rem);
    line-height: 1.06;
    letter-spacing: -0.01em;
    text-wrap: balance;
}

.mg-hero-lede {
    max-width: 46ch;
    margin-top: 24px;
    color: rgba(255, 255, 255, 0.82);
    font-size: 1.1rem;
}

.mg-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 36px;
}

/* The arch: the one decorative gesture on the page, echoing the parish shrine. */
.mg-arch {
    position: relative;
    width: min(100%, 440px);
    margin-inline: auto 0;
    aspect-ratio: 4 / 4.9;
}

.mg-arch::before {
    content: '';
    position: absolute;
    inset: -18px 18px 0 -18px;
    border: 1px solid var(--mg-gold);
    border-bottom: 0;
    border-radius: 999px 999px 0 0;
    opacity: 0.75;
}

.mg-arch img {
    position: relative;
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: 38% 60%;
    border-radius: 999px 999px 0 0;
}

@media (prefers-reduced-motion: no-preference) {
    .mg-hero-copy > * { animation: mg-rise 0.7s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
    .mg-hero-copy > :nth-child(2) { animation-delay: 0.08s; }
    .mg-hero-copy > :nth-child(3) { animation-delay: 0.16s; }
    .mg-hero-copy > :nth-child(4) { animation-delay: 0.24s; }
    .mg-arch { animation: mg-arch-in 0.9s 0.1s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
}

@keyframes mg-rise {
    from { opacity: 0; transform: translateY(18px); }
    to { opacity: 1; transform: none; }
}

@keyframes mg-arch-in {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: none; }
}

/* ---------- Sections ---------- */
.mg-section { padding-block: clamp(64px, 8vw, 112px); }
.mg-section-mist { background: var(--mg-mist); }

.mg-h2 {
    font-family: var(--mg-display);
    font-weight: 400;
    font-size: clamp(1.9rem, 3.2vw, 2.7rem);
    line-height: 1.12;
    color: var(--mg-oxblood);
    text-wrap: balance;
}

.mg-sub {
    max-width: 56ch;
    margin-top: 12px;
    color: var(--mg-stone);
}

/* ---------- Announcements (banner slider) ---------- */
.mg-notices { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin: 0 0 28px; padding: 0; list-style: none; }
.mg-notice { padding: 22px 24px; border: 1px solid rgba(122, 20, 32, 0.14); border-top: 4px solid var(--mg-gold, #c9a24b); border-radius: 16px; background: #fff; }
.mg-notice time { font-size: 0.82rem; font-weight: 600; letter-spacing: 0.02em; color: #8a6d3b; }
.mg-notice h3 { margin: 6px 0 8px; font-family: 'Marcellus', serif; font-weight: 400; font-size: 1.3rem; line-height: 1.25; color: #4a0a12; }
.mg-notice p { margin: 0; color: #3b2a2c; line-height: 1.6; white-space: pre-line; }
.mg-news-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 28px;
}

.mg-slider-frame {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: var(--mg-oxblood-deep);
    box-shadow: 0 30px 60px -30px rgba(74, 10, 18, 0.45);
}

.mg-slider {
    position: relative;
    aspect-ratio: 1700 / 620;
}

.mg-slider .slide {
    position: absolute;
    inset: 0;
    display: block;
    width: 100%;
    height: 100%;
    padding: 0;
    border: 0;
    background: transparent;
    cursor: zoom-in;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.6s ease, visibility 0s linear 0.6s;
}

.mg-slider .slide.active {
    opacity: 1;
    visibility: visible;
    transition: opacity 0.6s ease;
}

.mg-slider .slide img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    /* Undo the older slider's dimming and grey backing. */
    filter: none;
    background: transparent;
    transition: none;
}

.mg-slider-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-top: 18px;
    min-height: 44px;
}

.mg-dots { display: flex; gap: 4px; }

.mg-dots button {
    position: relative;
    width: 32px;
    height: 32px;
    padding: 0;
    border: 0;
    background: transparent;
    cursor: pointer;
}

.mg-dots button::after {
    content: '';
    position: absolute;
    inset: 50% 6px auto 6px;
    height: 3px;
    transform: translateY(-50%);
    border-radius: 3px;
    background: #d9cdca;
    transition: background-color 0.2s ease;
}

.mg-dots button.active::after { background: var(--mg-crimson); }

.mg-pause {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 1px solid var(--mg-line);
    border-radius: 50%;
    background: #fff;
    color: var(--mg-ink);
    cursor: pointer;
}

.mg-pause[hidden] { display: none; }
.mg-pause svg { width: 16px; height: 16px; }
.mg-pause:hover { border-color: var(--mg-crimson); color: var(--mg-crimson); }

/* ---------- History ---------- */
.mg-history-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
    gap: clamp(40px, 7vw, 104px);
    align-items: start;
}

.mg-prose p {
    max-width: 62ch;
    margin-top: 20px;
    color: #3b2f32;
}

.mg-timeline {
    list-style: none;
    margin: 6px 0 0;
    padding: 0;
    border-left: 1px solid var(--mg-gold);
}

.mg-timeline li {
    position: relative;
    padding: 0 0 34px 30px;
}

.mg-timeline li:last-child { padding-bottom: 0; }

.mg-timeline li::before {
    content: '';
    position: absolute;
    left: -5px;
    top: 14px;
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--mg-gold);
    box-shadow: 0 0 0 5px var(--mg-mist);
}

.mg-timeline time {
    display: block;
    font-family: var(--mg-display);
    font-size: 2.1rem;
    line-height: 1.1;
    color: var(--mg-crimson);
}

.mg-timeline p { margin-top: 6px; color: #3b2f32; }

/* ---------- Campus ---------- */
.mg-campus-grid {
    display: grid;
    grid-template-columns: 1.55fr 1fr;
    grid-template-rows: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-top: 36px;
    aspect-ratio: 16 / 8.2;
}

.mg-campus-item {
    position: relative;
    display: block;
    padding: 0;
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    background: var(--mg-mist);
    cursor: zoom-in;
}

.mg-campus-item:first-child { grid-row: 1 / span 2; }

/* Any number of photos other than three: first photo full width, the rest in a row grid. */
.mg-campus-grid--flex { display: flex; flex-wrap: wrap; aspect-ratio: auto; }
.mg-campus-grid--flex .mg-campus-item { flex: 1 1 240px; aspect-ratio: 3 / 2; }
.mg-campus-grid--flex .mg-campus-item:first-child { flex-basis: 100%; aspect-ratio: 21 / 9; }

.mg-campus-item img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.mg-campus-item:hover img { transform: scale(1.03); }

/* ---------- Careers band ---------- */
.mg-careers {
    color: #fff;
    background: linear-gradient(180deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
}

.mg-careers-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 28px 48px;
    padding-block: clamp(56px, 7vw, 88px);
}

.mg-careers h2 {
    font-family: var(--mg-display);
    font-weight: 400;
    font-size: clamp(2.1rem, 4vw, 3.2rem);
    line-height: 1.08;
    color: var(--mg-gold-soft);
}

.mg-careers p {
    max-width: 48ch;
    margin-top: 12px;
    color: rgba(255, 255, 255, 0.82);
}

/* ---------- Footer ---------- */
.mg-foot {
    background: var(--mg-oxblood-deep);
    color: rgba(255, 255, 255, 0.78);
    border-top: 1px solid rgba(201, 162, 75, 0.35);
    font-size: 0.97rem;
}

.mg-foot-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 40px 64px;
    padding-block: 56px 40px;
}

.mg-foot-brand { display: flex; gap: 16px; align-items: flex-start; }

.mg-foot-brand img {
    width: 60px;
    height: 60px;
    flex: none;
    padding: 4px;
    border-radius: 14px;
    background: #fff;
}

.mg-foot-brand strong {
    display: block;
    font-family: var(--mg-display);
    font-weight: 400;
    font-size: 1.3rem;
    color: #fff;
}

.mg-foot-brand span { display: block; margin-top: 4px; }

.mg-contact {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(2, max-content);
    gap: 6px 48px;
}

.mg-contact a {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 44px;
    color: inherit;
    text-decoration: none;
    overflow-wrap: anywhere;
}

.mg-contact a:hover { color: #fff; }
.mg-contact svg { width: 20px; height: 20px; flex: none; color: var(--mg-gold); }

.mg-foot-legal {
    padding-block: 20px 28px;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.55);
    font-size: 0.88rem;
}

/* ---------- Modals: bring type and color in line with the page ---------- */
.modal-card,
.login-modal-content,
.login-modal-content input,
.login-modal-content button,
.modal-card input,
.modal-card button { font-family: var(--mg-body); }

.login-modal-content { border-radius: 18px; }
.login-header h2,
.modal-card h2 { font-family: var(--mg-display); font-weight: 400; color: var(--mg-oxblood); }
.login-submit { background: var(--mg-crimson); border-radius: 999px; }
.login-submit:hover { background: var(--mg-oxblood); box-shadow: none; }

/* ---------- Responsive ---------- */
@media (max-width: 900px) {
    .mg-hero-grid { grid-template-columns: 1fr; padding-top: 48px; }
    .mg-hero-copy { padding-bottom: 8px; }
    .mg-hero h1 { max-width: 18ch; }
    .mg-arch { width: min(78%, 360px); margin-inline: auto; }
    .mg-history-grid { grid-template-columns: 1fr; }
    .mg-foot-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    :root { --mg-bar: 64px; }
    body { font-size: 16px; }
    .mg-wrap { width: min(100% - 36px, var(--mg-wrap)); }
    .mg-brand img { width: 38px; height: 38px; }
    .mg-brand span { font-size: 1.02rem; }

    .mg-bar .menu-toggle { display: flex; }

    .mg-nav {
        position: absolute;
        inset: var(--mg-bar) 0 auto 0;
        display: none;
        flex-direction: column;
        align-items: stretch;
        gap: 4px;
        padding: 12px 18px 20px;
        background: #fff;
        border-bottom: 1px solid var(--mg-line);
        box-shadow: 0 24px 40px -24px rgba(33, 23, 26, 0.35);
    }

    .mg-nav.open { display: flex; }
    .mg-nav a.mg-nav-link { padding: 13px 12px; border-radius: 10px; }
    .mg-nav .mg-btn { min-height: 48px; margin: 8px 0 0; }

    .mg-news-head { flex-direction: column; align-items: start; gap: 8px; }
    .mg-slider-frame { border-radius: 12px; }

    .mg-campus-grid {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto;
        aspect-ratio: auto;
        gap: 10px;
    }

    .mg-campus-item { aspect-ratio: 4 / 3; border-radius: 12px; }
    .mg-campus-item:first-child { grid-column: 1 / -1; grid-row: auto; aspect-ratio: 16 / 10; }

    .mg-contact { grid-template-columns: 1fr; }
    .mg-careers-inner .mg-btn { width: 100%; }
}

@media (max-width: 420px) {
    .mg-hero-actions .mg-btn { width: 100%; }
}

@media (prefers-reduced-motion: reduce) {
    html { scroll-behavior: auto; }
    .mg-slider .slide,
    .mg-campus-item img { transition: none; }
}
</style>
@endverbatim
@verbatim
<style>
/* =====================================================================
   Landing page: motion and interactive features.
   Everything here is progressive: without JavaScript, or with reduced
   motion turned on, the page shows the same content, simply still.
   ===================================================================== */

/* Scroll progress */
.mg-progress {
    position: fixed;
    inset: 0 0 auto 0;
    z-index: 60;
    height: 3px;
    transform: scaleX(var(--mg-scroll, 0));
    transform-origin: 0 50%;
    background: linear-gradient(90deg, var(--mg-crimson), var(--mg-gold));
    pointer-events: none;
}

/* Hero: drifting light, pointer glow, slow zoom */
.mg-motes {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.mg-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: radial-gradient(520px circle at var(--mg-px, 70%) var(--mg-py, 30%), rgba(230, 207, 154, 0.16), transparent 60%);
    opacity: var(--mg-glow, 0);
    transition: opacity 0.5s ease;
}

.mg-hero-grid { position: relative; z-index: 1; }

.mg-arch { will-change: transform; }
.mg-arch-photo {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
    border-radius: 999px 999px 0 0;
}
.mg-arch-photo img { border-radius: 0; }

@media (prefers-reduced-motion: no-preference) {
    .mg-arch-photo img { animation: mg-zoom 26s ease-in-out infinite alternate; }
}

@keyframes mg-zoom {
    from { transform: scale(1); }
    to { transform: scale(1.12); }
}

/* Figures */
.mg-figures {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    border-bottom: 1px solid var(--mg-line);
}

.mg-figure {
    padding: clamp(28px, 4vw, 48px) clamp(16px, 3vw, 40px);
    border-left: 1px solid var(--mg-line);
}

.mg-figure:first-child { border-left: 0; padding-left: 0; }

.mg-figure-num {
    display: block;
    font-family: var(--mg-display);
    font-size: clamp(2.6rem, 5.4vw, 4.4rem);
    line-height: 1;
    color: var(--mg-crimson);
    font-variant-numeric: tabular-nums;
}

.mg-figure-label {
    display: block;
    max-width: 24ch;
    margin-top: 10px;
    color: var(--mg-stone);
}

/* Open positions */
.mg-jobs-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px 32px;
    margin-bottom: 32px;
}

.mg-link {
    color: var(--mg-crimson);
    font-weight: 600;
    text-decoration: underline;
    text-decoration-color: rgba(165, 18, 30, 0.35);
    text-underline-offset: 5px;
}

.mg-link:hover { text-decoration-color: currentColor; }

.mg-jobs {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 18px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.mg-job {
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 190px;
    padding: 26px 26px 24px;
    border-radius: 18px;
    color: #fff;
    text-decoration: none;
    overflow: hidden;
    isolation: isolate;
    background: linear-gradient(160deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

/* A light that follows the pointer across each card. */
.mg-job::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: -1;
    background: radial-gradient(340px circle at var(--mx, 50%) var(--my, 0%), rgba(201, 162, 75, 0.42), transparent 62%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.mg-job:hover::before,
.mg-job:focus-visible::before { opacity: 1; }

.mg-job:hover { transform: translateY(-4px); box-shadow: 0 26px 44px -26px rgba(74, 10, 18, 0.7); }

.mg-job-type {
    align-self: flex-start;
    padding: 5px 12px;
    border: 1px solid rgba(230, 207, 154, 0.55);
    border-radius: 999px;
    color: var(--mg-gold-soft);
    font-size: 0.82rem;
    font-weight: 600;
}

.mg-job-title {
    margin-top: 18px;
    font-family: var(--mg-display);
    font-weight: 400;
    font-size: 1.55rem;
    line-height: 1.15;
}

.mg-job-go {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 22px;
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.95rem;
    font-weight: 500;
}

.mg-job-go svg { width: 22px; height: 22px; color: var(--mg-gold); transition: transform 0.25s ease; }
.mg-job:hover .mg-job-go svg { transform: translateX(5px); }

.mg-empty {
    padding: 36px;
    border: 1px dashed var(--mg-line);
    border-radius: 18px;
    color: var(--mg-stone);
}

/* How to apply */
.mg-steps {
    position: relative;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 28px;
    margin: 44px 0 0;
    padding: 0;
    list-style: none;
    counter-reset: mg-step;
}

.mg-steps::before,
.mg-steps::after {
    content: '';
    position: absolute;
    top: 21px;
    left: 22px;
    /* Stop at the centre of the fourth step's circle. */
    right: calc((100% - 84px) / 4 - 22px);
    height: 2px;
    background: var(--mg-line);
}

.mg-steps::after {
    background: linear-gradient(90deg, var(--mg-crimson), var(--mg-gold));
    transform: scaleX(var(--mg-steps, 1));
    transform-origin: 0 50%;
    transition: transform 1.6s cubic-bezier(0.2, 0.7, 0.2, 1);
}

.mg-step { position: relative; counter-increment: mg-step; }

.mg-step::before {
    content: counter(mg-step);
    position: relative;
    z-index: 1;
    display: grid;
    place-items: center;
    width: 44px;
    height: 44px;
    border: 2px solid var(--mg-crimson);
    border-radius: 50%;
    background: #fff;
    color: var(--mg-crimson);
    font-family: var(--mg-display);
    font-size: 1.2rem;
}

.mg-step h3 {
    margin-top: 18px;
    font-family: var(--mg-display);
    font-weight: 400;
    font-size: 1.3rem;
    color: var(--mg-oxblood);
}

.mg-step p { margin-top: 8px; color: var(--mg-stone); font-size: 0.98rem; }

/* History: the gold line draws itself as the reader scrolls. */
.mg-js .mg-timeline { border-left-color: var(--mg-line); position: relative; }

.mg-js .mg-timeline::after {
    content: '';
    position: absolute;
    left: -1px;
    top: 0;
    width: 1px;
    height: 100%;
    background: var(--mg-gold);
    transform: scaleY(var(--mg-draw, 0));
    transform-origin: 50% 0;
}

.mg-js .mg-timeline li::before { background: #d9cdca; transition: background-color 0.4s ease, transform 0.4s ease; z-index: 1; }
.mg-js .mg-timeline li.is-on::before { background: var(--mg-gold); transform: scale(1.35); }
.mg-js .mg-timeline time { transition: color 0.4s ease; color: #b9a9a6; }
.mg-js .mg-timeline li.is-on time { color: var(--mg-crimson); }

/* Campus: photos tilt toward the pointer. */
.mg-campus-grid { perspective: 1400px; }
.mg-campus-item { transform-style: preserve-3d; transition: transform 0.35s ease, box-shadow 0.35s ease; will-change: transform; }
.mg-campus-item.is-tilting { transition: box-shadow 0.35s ease; box-shadow: 0 34px 50px -30px rgba(33, 23, 26, 0.55); }

/* Announcement banners: the people stand out from the background and move. */
.mg-slider .slide { overflow: hidden; }

.mg-slider .slide img.mg-slide-people {
    position: absolute;
    inset: 0;
    transform-origin: 50% 100%;
    filter: drop-shadow(0 14px 16px rgba(33, 23, 26, 0.38));
    pointer-events: none;
}

.mg-slider .slide::after {
    content: '';
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(105deg, transparent 40%, rgba(255, 255, 255, 0.38) 50%, transparent 60%);
    transform: translateX(-100%);
}

@media (prefers-reduced-motion: no-preference) {
    /* Pointer parallax: background and people shift in opposite directions. */
    .mg-slider .slide img.mg-slide-bg {
        scale: 1.035;
        translate: calc(var(--mg-tilt-x, 0) * -9px) calc(var(--mg-tilt-y, 0) * -6px);
        transition: translate 0.35s ease-out;
    }

    .mg-slider .slide img.mg-slide-people {
        translate: calc(var(--mg-tilt-x, 0) * 16px) calc(var(--mg-tilt-y, 0) * 9px);
        transition: translate 0.35s ease-out;
    }

    /* Each time a banner comes up: the people step forward, then keep a slow drift. */
    .mg-slider .slide.active img.mg-slide-people {
        animation:
            mg-people-in 1s cubic-bezier(0.2, 0.9, 0.3, 1.25) both,
            mg-people-drift 6s ease-in-out 1s infinite alternate;
    }

    .mg-slider .slide.active img.mg-slide-flat { animation: mg-flat-zoom 9s ease-out both; }
    .mg-slider .slide.active::after { animation: mg-shine 1.5s ease-out 0.35s both; }
}

@keyframes mg-people-in {
    from { transform: translateY(5%) scale(0.955); }
    to { transform: none; }
}

@keyframes mg-people-drift {
    from { transform: none; }
    to { transform: translateY(-1.6%) scale(1.018); }
}

@keyframes mg-flat-zoom {
    from { transform: scale(1); }
    to { transform: scale(1.05); }
}

@keyframes mg-shine {
    from { transform: translateX(-100%); }
    to { transform: translateX(100%); }
}

/* ---------- More motion across the page ---------- */

/* Top bar: lifts off the page once scrolling starts; current section is marked. */
.mg-bar { transition: box-shadow 0.3s ease, background-color 0.3s ease; }
.mg-bar.is-scrolled { background: rgba(255, 255, 255, 0.98); box-shadow: 0 10px 30px -18px rgba(33, 23, 26, 0.45); }

.mg-nav a.mg-nav-link { position: relative; }
.mg-nav a.mg-nav-link::after {
    content: '';
    position: absolute;
    left: 14px;
    right: 14px;
    bottom: 4px;
    height: 2px;
    border-radius: 2px;
    background: var(--mg-crimson);
    transform: scaleX(0);
    transform-origin: 0 50%;
    transition: transform 0.3s ease;
}
.mg-nav a.mg-nav-link.is-current { color: var(--mg-crimson); }
.mg-nav a.mg-nav-link.is-current::after { transform: scaleX(1); }

/* Buttons: a band of light crosses on hover. */
.mg-btn { position: relative; overflow: hidden; }
.mg-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, transparent 35%, rgba(255, 255, 255, 0.5) 50%, transparent 65%);
    transform: translateX(-120%);
    pointer-events: none;
}

/* Section headings: a short gold rule draws in under each one. */
.mg-h2::after {
    content: '';
    display: block;
    width: 64px;
    height: 2px;
    margin-top: 16px;
    background: var(--mg-gold);
    transform-origin: 0 50%;
}

/* Careers band */
.mg-careers { position: relative; overflow: hidden; }
.mg-careers-inner { position: relative; z-index: 1; }
.mg-careers h2 {
    background: linear-gradient(100deg, var(--mg-gold-soft) 0%, var(--mg-gold-soft) 40%, #fffaf0 50%, var(--mg-gold-soft) 60%, var(--mg-gold-soft) 100%);
    background-size: 250% 100%;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: var(--mg-gold-soft);
}

/* Footer links */
.mg-contact svg { transition: transform 0.25s ease; }
.mg-contact a:hover svg { transform: translateY(-3px) scale(1.12); }

@media (prefers-reduced-motion: no-preference) {
    .mg-btn:hover::after { transform: translateX(120%); transition: transform 0.7s ease; }

    .mg-js .mg-h2::after { transform: scaleX(0); transition: transform 0.9s cubic-bezier(0.2, 0.7, 0.2, 1) 0.3s; }
    .mg-js .mg-h2.is-in::after { transform: scaleX(1); }

    /* Hero: the gold arch outline breathes. */
    .mg-arch::before { animation: mg-arch-glow 5s ease-in-out infinite alternate; }

    /* Figures: a gold rule grows under each number. */
    .mg-js .mg-figure-num { position: relative; display: inline-block; padding-bottom: 12px; }
    .mg-js .mg-figure-num::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 2px;
        background: var(--mg-gold);
        transform: scaleX(0);
        transform-origin: 0 50%;
        transition: transform 1.2s cubic-bezier(0.2, 0.7, 0.2, 1) calc(var(--i, 0) * 90ms + 0.2s);
    }
    .mg-js .mg-figure.is-in .mg-figure-num::after { transform: scaleX(1); }

    /* How to apply: each circle pops in and fills as the line reaches it. */
    .mg-js .mg-step::before {
        transform: scale(0);
        transition:
            transform 0.5s cubic-bezier(0.2, 0.9, 0.3, 1.5) calc(var(--i, 0) * 0.42s + 0.1s),
            background-color 0.4s ease calc(var(--i, 0) * 0.42s + 0.5s),
            color 0.4s ease calc(var(--i, 0) * 0.42s + 0.5s);
    }
    .mg-js .mg-step.is-in::before { transform: scale(1); background: var(--mg-crimson); color: #fff; }
    .mg-js .mg-step[data-reveal] { transition-delay: calc(var(--i, 0) * 0.42s); }

    /* Campus: each photo settles from a slight zoom as it appears. */
    .mg-js .mg-campus-item[data-reveal] img { transform: scale(1.18); transition: transform 1.6s cubic-bezier(0.2, 0.7, 0.2, 1); }
    .mg-js .mg-campus-item[data-reveal].is-in img { transform: none; }
    .mg-js .mg-campus-item[data-reveal].is-in:hover img { transform: scale(1.03); transition-duration: 0.5s; }

    /* Position cards rise a little more each, like a hand of cards. */
    .mg-js .mg-jobs li[data-reveal] { transform: translateY(40px) rotate(-1.2deg); }
    .mg-js .mg-jobs li[data-reveal].is-in { transform: none; }

    /* Careers band: light passes over the headline; the button draws the eye. */
    .mg-careers h2 { animation: mg-headline-shine 7s ease-in-out infinite; }
    .mg-careers .mg-btn-gold { animation: mg-pulse 2.8s ease-out infinite; }

    /* Footer: the school seal turns to face you on hover. */
    .mg-foot-brand img { transition: transform 0.6s cubic-bezier(0.2, 0.7, 0.2, 1); }
    .mg-foot-brand:hover img { transform: rotateY(360deg); }
}

@keyframes mg-arch-glow {
    from { opacity: 0.45; filter: drop-shadow(0 0 0 rgba(201, 162, 75, 0)); }
    to { opacity: 1; filter: drop-shadow(0 0 10px rgba(201, 162, 75, 0.7)); }
}

@keyframes mg-headline-shine {
    0%, 55% { background-position: 100% 0; }
    100% { background-position: 0 0; }
}

@keyframes mg-pulse {
    0% { box-shadow: 0 0 0 0 rgba(201, 162, 75, 0.55); }
    70%, 100% { box-shadow: 0 0 0 18px rgba(201, 162, 75, 0); }
}

/* Reveal on scroll */
@media (prefers-reduced-motion: no-preference) {
    .mg-js [data-reveal] {
        opacity: 0;
        transform: translateY(26px);
        transition: opacity 0.8s cubic-bezier(0.2, 0.7, 0.2, 1), transform 0.8s cubic-bezier(0.2, 0.7, 0.2, 1);
        transition-delay: calc(var(--i, 0) * 90ms);
    }
    .mg-js [data-reveal].is-in { opacity: 1; transform: none; }
    .mg-js .mg-steps:not(.is-in) { --mg-steps: 0; }
}

@media (max-width: 900px) {
    .mg-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); row-gap: 36px; }
    .mg-steps::before, .mg-steps::after { display: none; }
}

@media (max-width: 768px) {
    .mg-figures { grid-template-columns: 1fr; }
    .mg-figure { display: flex; align-items: baseline; gap: 18px; padding: 20px 0; border-left: 0; border-top: 1px solid var(--mg-line); }
    .mg-figure:first-child { border-top: 0; }
    .mg-figure-num { min-width: 2.4ch; font-size: 2.6rem; }
    .mg-figure-label { margin-top: 0; }
    .mg-job { min-height: 168px; }
}

@media (max-width: 520px) {
    .mg-steps { grid-template-columns: 1fr; row-gap: 26px; }
    .mg-step { display: grid; grid-template-columns: 44px 1fr; column-gap: 16px; }
    .mg-step::before { grid-row: 1 / span 2; }
    .mg-step h3 { margin-top: 6px; }
}
</style>
@endverbatim
@endsection



@section('content')

    <script>document.documentElement.classList.add('mg-js');</script>
    <div class="mg-progress" aria-hidden="true"></div>

    <header class="mg-bar">
        <div class="mg-wrap mg-bar-inner">
            <a class="mg-brand" href="{{ route('home') }}" aria-label="St. Mary Magdalene School home">
                <img src="{{ asset('image/SMMS Logo.png') }}" alt="" width="44" height="44">
                <span>St. Mary Magdalene School</span>
            </a>
            <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="top-nav">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <nav class="mg-nav" id="top-nav" aria-label="Main">
                <a class="mg-nav-link" href="#announcements">Announcements</a>
                <a class="mg-nav-link" href="#positions">Open positions</a>
                <a class="mg-nav-link" href="#history">History</a>
                <a class="mg-nav-link" href="#campus">Campus</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="mg-btn mg-btn-crimson">Dashboard</a>
                @else
                    <button id="login-btn" class="mg-btn mg-btn-crimson" type="button">Log in</button>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mg-page">
        {{-- State flag read by the script: reopen login modal after a failed login or on /login --}}
        @if (session('status'))
            <div id="login-status-message" hidden data-type="{{ session('status_type', 'error') }}">{{ session('status') }}</div>
        @endif

        {{-- Reopen the login modal when:
             - a failed login came back with old('form_origin') === 'login' and validation errors
             - /login was visited, or a status message was flashed --}}
        <div id="login-state" hidden
             data-open="{{ (old('form_origin') === 'login' || request()->boolean('login') || ($openLogin ?? false) || session('status')) ? 'true' : 'false' }}"></div>
        <div id="signup-state" hidden data-open="{{ request()->boolean('signup') ? 'true' : 'false' }}"></div>

        <section class="mg-hero">
            <canvas class="mg-motes" aria-hidden="true"></canvas>
            <div class="mg-wrap mg-hero-grid">
                <div class="mg-hero-copy">
                    <p class="mg-hero-place">{{ $cms['hero_place'] }}</p>
                    <h1>{{ $cms['hero_title'] }}</h1>
                    <p class="mg-hero-lede">{{ $cms['hero_lede'] }}</p>
                    <div class="mg-hero-actions">
                        <a class="mg-btn mg-btn-gold" href="{{ route('public.job-openings') }}">View job openings</a>
                        @auth
                            <a class="mg-btn mg-btn-ghost" href="{{ route('dashboard') }}">Go to your dashboard</a>
                        @else
                            <button class="mg-btn mg-btn-ghost" type="button" onclick="openLoginModal()">Log in</button>
                        @endauth
                    </div>
                </div>
                <div class="mg-arch">
                    <div class="mg-arch-photo">
                        <img src="{{ asset('image/campus-2.webp') }}" alt="The main building of St. Mary Magdalene School"
                            width="1400" height="933" fetchpriority="high">
                    </div>
                </div>
            </div>
        </section>

        <div class="mg-wrap">
            <dl class="mg-figures">
                <div class="mg-figure" data-reveal style="--i: 0">
                    <dd class="mg-figure-num" data-count="{{ $homeYearsOfService }}">{{ $homeYearsOfService }}</dd>
                    <dt class="mg-figure-label">years of Catholic education in Kawit, since 1961</dt>
                </div>
                <div class="mg-figure" data-reveal style="--i: 1">
                    <dd class="mg-figure-num" data-count="{{ $homeOpenings->count() }}">{{ $homeOpenings->count() }}</dd>
                    <dt class="mg-figure-label">{{ $homeOpenings->count() === 1 ? 'position' : 'positions' }} open for application today</dt>
                </div>
                <div class="mg-figure" data-reveal style="--i: 2">
                    <dd class="mg-figure-num" data-count="15">15</dd>
                    <dt class="mg-figure-label">pupils in the first kindergarten class, held in the parish convent</dt>
                </div>
            </dl>
        </div>

        <section class="mg-section" id="announcements" aria-labelledby="announcements-title">
            <div class="mg-wrap">
                <div class="mg-news-head">
                    <div>
                        <h2 class="mg-h2" id="announcements-title">Announcements</h2>
                        <p class="mg-sub">News and hiring notices posted by the school. Select one to view it larger.</p>
                    </div>
                </div>

                @if (($homeAnnouncements ?? collect())->isNotEmpty())
                    <ul class="mg-notices">
                        @foreach ($homeAnnouncements as $note)
                            <li class="mg-notice" data-reveal style="--i: {{ $loop->index }}">
                                <time datetime="{{ $note->posted_on->toDateString() }}">{{ $note->posted_on->format('F j, Y') }}</time>
                                <h3>{{ $note->title }}</h3>
                                <p>{{ $note->body }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mg-slider-frame" data-reveal>
                    <div class="mg-slider" id="slider">
                        @foreach ($homepageBannerSlides as $index => $slide)
                            <button type="button" class="slide {{ $index === 0 ? 'active' : '' }}"
                                onclick="openFacilitiesModal(@js($slide['url']), @js($slide['alt']))"
                                aria-label="View larger: {{ $slide['alt'] }}">
                                @if (! empty($slide['layers']))
                                    {{-- Layered banner: the people sit on their own layer so they can move. --}}
                                    <img class="mg-slide-bg" src="{{ $slide['layers']['background'] }}" alt="{{ $slide['alt'] }}"
                                        @if ($index > 0) loading="lazy" @endif>
                                    <img class="mg-slide-people" src="{{ $slide['layers']['people'] }}" alt=""
                                        @if ($index > 0) loading="lazy" @endif>
                                @else
                                    <img class="mg-slide-flat" src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}"
                                        @if ($index > 0) loading="lazy" @endif>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mg-slider-controls">
                    <div class="mg-dots" id="dots"></div>
                    <button id="homepageBannerPause" class="mg-pause" type="button" hidden
                        aria-label="Pause banner rotation" aria-pressed="false" title="Pause banner rotation">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                            <path data-toggle-icon d="M6 4h4v16H6z M14 4h4v16h-4z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <section class="mg-section mg-section-mist" id="positions" aria-labelledby="positions-title">
            <div class="mg-wrap">
                <div class="mg-jobs-head">
                    <div>
                        <h2 class="mg-h2" id="positions-title">Open positions</h2>
                        <p class="mg-sub">Updated by the school's HR office. Select a position to read its details and apply.</p>
                    </div>
                    <a class="mg-link" href="{{ route('public.job-openings') }}">See all job openings</a>
                </div>

                @if ($homeOpenings->isEmpty())
                    <p class="mg-empty">No positions are open right now. Check back soon, or follow the school's announcements.</p>
                @else
                    <ul class="mg-jobs">
                        @foreach ($homeOpenings->take(6) as $opening)
                            <li data-reveal style="--i: {{ $loop->index }}">
                                <a class="mg-job" href="{{ route('public.job-openings.show', $opening) }}">
                                    <span class="mg-job-type">
                                        {{ match ($opening->filterType()) {
                                            'teaching' => 'Teaching',
                                            'teaching-related' => 'Teaching-related',
                                            default => 'Non-teaching',
                                        } }}
                                    </span>
                                    <span class="mg-job-title">{{ $opening->title }}</span>
                                    <span class="mg-job-go">
                                        View and apply
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>

        <section class="mg-section" aria-labelledby="apply-title">
            <div class="mg-wrap">
                <h2 class="mg-h2" id="apply-title">How to apply</h2>
                <p class="mg-sub">{{ $cms['apply_intro'] }}</p>
                <ol class="mg-steps">
                    @foreach (\App\Support\SiteContent::applySteps() as [$stepTitle, $stepText])
                        <li class="mg-step">
                            <h3>{{ $stepTitle }}</h3>
                            <p>{{ $stepText }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="mg-section mg-section-mist" id="history" aria-labelledby="history-title">
            <div class="mg-wrap mg-history-grid">
                <div class="mg-prose">
                    <h2 class="mg-h2" id="history-title">Our history</h2>
                    <p>{{ $cms['history_p1'] }}</p>
                    <p>{{ $cms['history_p2'] }}</p>
                </div>

                <ol class="mg-timeline" aria-label="Key dates">
                    @foreach (\App\Support\SiteContent::timeline() as [$year, $text])
                        <li>
                            <time datetime="{{ preg_replace('/\D/', '', $year) }}">{{ $year }}</time>
                            <p>{{ $text }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        @if (($campusPhotos ?? collect())->isNotEmpty())
        <section class="mg-section" id="campus" aria-labelledby="campus-title">
            <div class="mg-wrap">
                <h2 class="mg-h2" id="campus-title">The campus</h2>
                <p class="mg-sub">Select a photo to view it larger.</p>

                <div class="mg-campus-grid {{ $campusPhotos->count() === 3 ? '' : 'mg-campus-grid--flex' }}">
                    @foreach ($campusPhotos as $photo)
                        <button class="mg-campus-item" type="button" data-reveal style="--i: {{ $loop->index }}"
                            onclick="openFacilitiesModal(@js($photo->full_url), @js($photo->caption))"
                            aria-label="View larger: {{ $photo->caption }}">
                            <img src="{{ $photo->url }}" alt="{{ $photo->caption }}" loading="lazy"
                                width="1400" height="933">
                        </button>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <section class="mg-careers" aria-labelledby="careers-title">
            <canvas class="mg-motes" aria-hidden="true"></canvas>
            <div class="mg-wrap mg-careers-inner" data-reveal>
                <div>
                    <h2 id="careers-title">{{ $cms['careers_title'] }}</h2>
                    <p>{{ $cms['careers_text'] }}</p>
                </div>
                <a class="mg-btn mg-btn-gold" href="{{ route('public.job-openings') }}">View job openings</a>
            </div>
        </section>

        {{-- Apply Now Modal --}}
        <x-apply-now-modal />

        {{-- registration --}}
        <x-registration-modal/>

        <!-- Facilities Modal -->
        <div id="FacilitiesModal" class="FacilitiesModal" onclick="closeFacilitiesModal()">
            <span class="FacilitiesModal-close" onclick="closeFacilitiesModal(); event.stopPropagation();">&times;</span>
            <img class="FacilitiesModal-img" id="FacilitiesModal-img" src="" alt="">
        </div>

        {{-- Apply Login Modal --}}
        <x-apply-login-modal />

        @if (session('registration_success'))
            <div class="modal-overlay registration-success-overlay" id="registration-success-modal"
                role="presentation">
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

        {{-- Login Modal --}}
        <x-login-modal />

    </main>

    @include('partials.mg-footer')
@endsection


{{-- =====================================================================
     SCRIPTS (everything from HOMEPAGEScript.js, inlined)
     ===================================================================== --}}
@section('scripts')


@verbatim
<script>
// True kapag ang error ay galing sa LOGIN (mali ang credentials).
// Pinipigilan nito na magbukas ang Create Account / signup modal dahil sa email/password error.
function isLoginErrorActive() {
    return document.getElementById('login-state')?.dataset.open === 'true';
}

/* ------------------------------------------------------------------
   Modal helpers (defined at top level so inline onclick="..." works)
   ------------------------------------------------------------------ */

// Login modal
function openLoginModal() {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeLoginModal() {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Apply-login modal
function openApplyLoginModal() {
    const modal = document.getElementById('apply-login-modal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeApplyLoginModal() {
    const modal = document.getElementById('apply-login-modal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Facilities modal
function openFacilitiesModal(src, alt) {
    const modal = document.getElementById('FacilitiesModal');
    const img = document.getElementById('FacilitiesModal-img');
    if (!modal || !img) return;
    img.src = src;
    img.alt = alt;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeFacilitiesModal() {
    const modal = document.getElementById('FacilitiesModal');
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

window.openLoginModal = openLoginModal;
window.closeLoginModal = closeLoginModal;
window.openApplyLoginModal = openApplyLoginModal;
window.closeApplyLoginModal = closeApplyLoginModal;
window.openFacilitiesModal = openFacilitiesModal;
window.closeFacilitiesModal = closeFacilitiesModal;


/* ------------------------------------------------------------------
   Main page behaviour: slider, nav menu, apply + signup modals
   ------------------------------------------------------------------ */
document.addEventListener('DOMContentLoaded', function () {
    const slides = Array.from(document.querySelectorAll('.slide'));
    const dotsContainer = document.getElementById('dots');
    const menuToggle = document.querySelector('.menu-toggle');
    const topNav = document.getElementById('top-nav');
    const loginBtn = document.getElementById('login-btn');
    const applyBtn = document.getElementById('apply-btn');
    const applyModal = document.getElementById('apply-modal');
    const signupModal = document.getElementById('signup-modal');
    const signupOpenBtn = document.getElementById('signup-open-btn');
    const signupCloseBtn = signupModal?.querySelector('.modal-close');
    const signupForm = document.getElementById('signup-form');
    const closeModalBtn = applyModal?.querySelector('.modal-close');
    let current = 0;
    let timer = null;
    let manuallyPaused = false;
    let signupHandlersAttached = false;

    // ----- Apply modal -----
    function openApplyModal() {
        if (!applyModal) return;
        applyModal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function closeApplyModal() {
        if (!applyModal) return;
        applyModal.hidden = true;
        document.body.style.overflow = '';
    }

    // ----- Sign-up modal -----
    function openSignupModal() {
        if (!signupModal) return;
        signupModal.hidden = false;
        document.body.style.overflow = 'hidden';
        const sp = document.getElementById('signup-password');
        const scp = document.getElementById('signup-confirm-password');
        const pr = document.getElementById('password-requirements');
        if (pr) pr.style.display = 'block';

        if (!signupHandlersAttached) {
            if (sp) {
                sp.addEventListener('input', function () {
                    updatePasswordRequirements(sp.value || '');
                    checkPasswords();
                });
                updatePasswordRequirements(sp.value || '');
            }
            if (scp) {
                scp.addEventListener('input', checkPasswords);
            }
            signupHandlersAttached = true;
        }
    }

    function closeSignupModal() {
        if (!signupModal) return;
        signupModal.hidden = true;
        document.body.style.overflow = '';
    }

    // Make these reachable from the Escape handler and inline onclick
    window.openApplyModal = openApplyModal;
    window.closeApplyModal = closeApplyModal;
    window.openSignupModal = openSignupModal;
    window.closeSignupModal = closeSignupModal;

    if (applyBtn && applyModal) {
        applyBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openApplyModal();
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeApplyModal);
    }

    if (applyModal) {
        applyModal.addEventListener('click', function (e) {
            if (e.target === applyModal) closeApplyModal();
        });
    }

    const applyLoginBtn = document.getElementById('apply-login-btn');
    const applyLoginModal = document.getElementById('apply-login-modal');
    const applyLoginForm = document.getElementById('apply-login-form');
    const applyLoginSignupLink = document.getElementById('apply-login-signup-link');
    const registrationSuccessModal = document.getElementById('registration-success-modal');
    const registrationContinueButton = document.getElementById('registration-continue');

    if (signupOpenBtn && signupModal) {
        signupOpenBtn.addEventListener('click', function (e) {
            e.preventDefault();
            closeApplyModal();
            openSignupModal();
        });
    }

    // Opened from another page (?signup=1): wait until the whole script has set up the
    // password checks and the close button, otherwise the window opens half-wired.
    if (document.getElementById('signup-state')?.dataset.open === 'true') {
        setTimeout(openSignupModal, 0);
    }

    if (applyLoginBtn && applyLoginModal) {
        applyLoginBtn.addEventListener('click', function (e) {
            e.preventDefault();
            closeApplyModal();
            openApplyLoginModal();
        });
    }

    if (applyLoginSignupLink) {
        applyLoginSignupLink.addEventListener('click', function (e) {
            e.preventDefault();
            closeApplyLoginModal();
            openSignupModal();
        });
    }

    if (signupCloseBtn) {
        signupCloseBtn.addEventListener('click', closeSignupModal);
    }

    if (signupModal) {
        signupModal.addEventListener('click', function (e) {
            if (e.target === signupModal) closeSignupModal();
        });
    }

    if (signupForm?.dataset.hasErrors === 'true' && !isLoginErrorActive()) {
        setTimeout(openSignupModal, 0);
    }

    if (registrationSuccessModal) {
        document.body.style.overflow = 'hidden';
        registrationContinueButton?.addEventListener('click', function () {
            registrationSuccessModal.remove();
            document.body.style.overflow = '';
            openApplyLoginModal();
            document.getElementById('apply-email')?.focus();
        });
    }

    if (applyLoginForm?.dataset.hasErrors === 'true') {
        openApplyLoginModal();
    }

    // Reopen login modal after a failed login, or when visiting /login
    if (document.getElementById('login-state')?.dataset.open === 'true') {
        // Show the flashed status (e.g. "email not verified") inside the login modal
        const statusEl = document.getElementById('login-status-message');
        const statusText = statusEl?.textContent.trim();
        const statusType = statusEl?.dataset.type === 'success' ? 'success' : 'error';
        const loginInner = document.querySelector('#login-modal .login-modal-inner');
        if (statusText && loginInner && !loginInner.querySelector('.login-status')) {
            const note = document.createElement('div');
            note.className = 'login-status' + (statusType === 'success' ? ' login-status--success' : '');
            note.setAttribute('role', statusType === 'success' ? 'status' : 'alert');
            note.textContent = statusText;
            const header = loginInner.querySelector('.login-header');
            if (header) {
                header.insertAdjacentElement('afterend', note);
            } else {
                loginInner.prepend(note);
            }
        }
        openLoginModal();
    }

    // ----- Password visibility toggles -----
    function updatePasswordIcon(toggle, isVisible) {
        if (isVisible) {
            toggle.innerHTML = `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>`;
        } else {
            toggle.innerHTML = `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 17l-10-10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
        }
    }

    Array.from(document.querySelectorAll('.password-toggle')).forEach(toggle => {
        toggle.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;
            const shouldShow = input.type === 'password';
            input.type = shouldShow ? 'text' : 'password';
            this.setAttribute('aria-pressed', String(shouldShow));
            this.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
            updatePasswordIcon(this, shouldShow);
        });
    });

    // ----- Sign-up password validation -----
    const signupPassword = document.getElementById('signup-password');
    const signupConfirmPassword = document.getElementById('signup-confirm-password');
    const signupErrorMessage = document.getElementById('signup-error-message');
    const passwordRequirementsContainer = document.getElementById('password-requirements');
    const passwordCount = document.getElementById('password-count');
    const signupSubmitButton = signupForm ? signupForm.querySelector('button[type="submit"]') : null;
    if (signupSubmitButton) signupSubmitButton.disabled = true;

    const passwordRequirements = [
        { key: 'length',  test: p => p.length >= 12 },
        { key: 'capital', test: p => /[A-Z]/.test(p) },
        { key: 'number',  test: p => /\d/.test(p) },
        { key: 'symbol',  test: p => /[^A-Za-z0-9]/.test(p) }
    ];

    function updatePasswordRequirements(pass) {
        if (!passwordRequirementsContainer) return;
        let metCount = 0;
        passwordRequirements.forEach(r => {
            const el = passwordRequirementsContainer.querySelector(`.requirement[data-requirement="${r.key}"]`);
            if (!el) return;
            const met = r.test(pass);
            if (met) { el.classList.add('met'); metCount++; } else { el.classList.remove('met'); }
        });
        if (passwordCount) passwordCount.textContent = `(${metCount}/${passwordRequirements.length})`;
        if (signupSubmitButton) signupSubmitButton.disabled = metCount !== passwordRequirements.length;
    }

    function checkPasswords() {
        if (!signupPassword || !signupConfirmPassword || !signupErrorMessage) return true;

        const pass = signupPassword.value.trim();
        const confirmPass = signupConfirmPassword.value.trim();

        updatePasswordRequirements(pass);

        if (confirmPass.length > 0) {
            if (pass !== confirmPass) {
                signupErrorMessage.style.display = 'block';
                return false;
            }
            signupErrorMessage.style.display = 'none';
            return true;
        }
        signupErrorMessage.style.display = 'none';
        return true;
    }

    // ----- Login button -----
    if (loginBtn) {
        loginBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openLoginModal();
        });
    }

    // ----- Mobile navigation menu -----
    if (menuToggle && topNav) {
        menuToggle.addEventListener('click', function () {
            const isOpen = topNav.classList.toggle('open');
            menuToggle.classList.toggle('active', isOpen);
            menuToggle.setAttribute('aria-expanded', String(isOpen));
        });

        Array.from(topNav.querySelectorAll('a, button')).forEach(link => {
            link.addEventListener('click', function () {
                topNav.classList.remove('open');
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                topNav.classList.remove('open');
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ----- Banner slider -----
    const pauseButton = document.getElementById('homepageBannerPause');
    if (!slides.length) return;
    pauseButton.hidden = slides.length < 2;

    slides.forEach((s, i) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.setAttribute('aria-label', 'Show announcement ' + (i + 1));
        b.addEventListener('click', () => { show(i); restartTimer(); });
        if (i === 0) b.classList.add('active');
        dotsContainer.appendChild(b);
    });

    function show(index) {
        slides.forEach((s, i) => s.classList.toggle('active', i === index));
        Array.from(dotsContainer.children).forEach((d, i) => d.classList.toggle('active', i === index));
        current = index;
    }

    function next() { show((current + 1) % slides.length); }
    function stopTimer() {
        clearInterval(timer);
        timer = null;
    }
    function restartTimer() {
        stopTimer();
        if (!manuallyPaused && slides.length > 1) {
            // Six seconds per banner: long enough to read a hiring notice.
            timer = setInterval(next, 6000);
        }
    }
    function updatePauseButton() {
        const label = `${manuallyPaused ? 'Resume' : 'Pause'} banner rotation`;
        pauseButton.querySelector('[data-toggle-icon]').setAttribute(
            'd',
            manuallyPaused
                ? 'M7 4.8v14.4a1 1 0 0 0 1.5.86l11-7.2a1 1 0 0 0 0-1.72l-11-7.2A1 1 0 0 0 7 4.8z'
                : 'M6 4h4v16H6z M14 4h4v16h-4z'
        );
        pauseButton.setAttribute('aria-label', label);
        pauseButton.title = label;
        pauseButton.setAttribute('aria-pressed', String(manuallyPaused));
    }

    pauseButton.addEventListener('click', () => {
        manuallyPaused = !manuallyPaused;
        updatePauseButton();
        restartTimer();
    });

    restartTimer();
});


/* ------------------------------------------------------------------
   Modal overlay-click closing and Escape key
   ------------------------------------------------------------------ */
document.addEventListener('DOMContentLoaded', function () {
    const modalClosers = {
        'login-modal': closeLoginModal,
        'apply-login-modal': closeApplyLoginModal,
        'FacilitiesModal': closeFacilitiesModal
    };

    Object.keys(modalClosers).forEach(function (id) {
        const overlay = document.getElementById(id);
        if (!overlay) return;

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) modalClosers[id]();
        });

        overlay.querySelectorAll('[class*="close"]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                modalClosers[id]();
            });
        });
    });

    // Escape key closes every modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeLoginModal();
            closeApplyLoginModal();
            closeFacilitiesModal();
            if (typeof window.closeSignupModal === 'function') window.closeSignupModal();
            if (typeof window.closeApplyModal === 'function') window.closeApplyModal();
        }
    });
});


/* ------------------------------------------------------------------
   Registration modal (reloads the page on close)
   ------------------------------------------------------------------ */
document.addEventListener('DOMContentLoaded', function () {

    const signupModal = document.getElementById('signup-modal');
    const signupOpenBtn = document.getElementById('signup-open-btn');
    const signupCloseBtn = signupModal?.querySelector('.modal-close');
    const signupForm = document.getElementById('signup-form');

    let signupHandlersAttached = false;

    // OPEN REGISTRATION MODAL
    function openSignupModal() {

        if (!signupModal) return;

        signupModal.hidden = false;

        document.body.style.overflow = 'hidden';

        const sp = document.getElementById('signup-password');
        const scp = document.getElementById('signup-confirm-password');
        const pr = document.getElementById('password-requirements');

        if (pr) {
            pr.style.display = 'block';
        }

        // Attach password validation handlers only once
        if (!signupHandlersAttached) {

            if (sp) {

                sp.addEventListener('input', function () {

                    updatePasswordRequirements(sp.value || '');

                    checkPasswords();

                });

                updatePasswordRequirements(sp.value || '');

            }

            if (scp) {
                scp.addEventListener('input', checkPasswords);
            }

            signupHandlersAttached = true;

        }

    }

    // CLOSE REGISTRATION MODAL (refresh page)
    function closeSignupModal() {

        if (!signupModal) return;

        // Reload without ?signup=1, otherwise the window opens again right away and can never be closed.
        const url = new URL(window.location.href);
        url.searchParams.delete('signup');
        window.location.replace(url.toString());

    }

    // MAKE FUNCTIONS AVAILABLE GLOBALLY
    window.openSignupModal = openSignupModal;
    window.closeSignupModal = closeSignupModal;

    // OPEN REGISTRATION MODAL BUTTON
    if (signupOpenBtn && signupModal) {

        signupOpenBtn.addEventListener('click', function (e) {

            e.preventDefault();

            openSignupModal();

        });

    }

    // CLOSE USING X BUTTON
    if (signupCloseBtn) {

        signupCloseBtn.addEventListener('click', function (e) {

            e.preventDefault();

            closeSignupModal();

        });

    }

    // CLOSE WHEN CLICKING OUTSIDE THE MODAL
    if (signupModal) {

        signupModal.addEventListener('click', function (e) {

            // Only reload if the backdrop itself is clicked
            if (e.target === signupModal) {

                closeSignupModal();

            }

        });

    }

    // REOPEN MODAL IF REGISTRATION HAS ERRORS
    if (signupForm?.dataset.hasErrors === 'true' && !isLoginErrorActive()) {

        openSignupModal();

    }

    // CLOSE WHEN PRESSING ESCAPE
    document.addEventListener('keydown', function (e) {

        if (
            e.key === 'Escape' &&
            signupModal &&
            !signupModal.hidden
        ) {

            closeSignupModal();

        }

    });

});
</script>
@endverbatim

{{-- Landing page motion: scroll progress, hero light, counters, reveals, timeline, card glow, photo tilt. --}}
<script>
(function () {
    const root = document.documentElement;
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    /* ---- Reveal on scroll, counters, steps ---- */
    const countUp = (el) => {
        const target = Number(el.dataset.count || 0);
        if (calm || target === 0) { el.textContent = target; return; }
        const started = performance.now();
        const duration = 1400;
        const tick = (now) => {
            const t = Math.min(1, (now - started) / duration);
            el.textContent = Math.round(target * (1 - Math.pow(1 - t, 3)));
            if (t < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    };

    const seen = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-in');
            entry.target.querySelectorAll('[data-count]').forEach(countUp);
            if (entry.target.matches('[data-count]')) countUp(entry.target);
            seen.unobserve(entry.target);
        });
    }, { threshold: 0.18, rootMargin: '0px 0px -6% 0px' });

    /* Headings, paragraphs, steps and footer items also ease in. They are
       tagged here, in script, so the page reads normally without JavaScript. */
    document.querySelectorAll('.mg-h2, .mg-sub, .mg-prose p, .mg-jobs-head .mg-link, .mg-foot-brand').forEach((el) => {
        if (!el.closest('[data-reveal]')) el.setAttribute('data-reveal', '');
    });
    document.querySelectorAll('.mg-steps, .mg-contact, .mg-timeline').forEach((list) => {
        Array.from(list.children).forEach((item, index) => {
            item.style.setProperty('--i', index);
            if (!list.classList.contains('mg-timeline')) item.setAttribute('data-reveal', '');
        });
    });

    document.querySelectorAll('[data-reveal], .mg-steps').forEach((el) => seen.observe(el));

    /* ---- Top bar: highlight the section being read ---- */
    const navLinks = Array.from(document.querySelectorAll('.mg-nav-link[href^="#"]'));
    const spy = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            navLinks.forEach((link) => {
                const current = link.getAttribute('href') === '#' + entry.target.id;
                link.classList.toggle('is-current', current);
                if (current) link.setAttribute('aria-current', 'true'); else link.removeAttribute('aria-current');
            });
        });
    }, { rootMargin: '-45% 0px -50% 0px' });
    navLinks.forEach((link) => {
        const section = document.querySelector(link.getAttribute('href'));
        if (section) spy.observe(section);
    });
    const bar = document.querySelector('.mg-bar');

    /* ---- Scroll-linked: progress bar, history line, arch parallax ---- */
    const timeline = document.querySelector('.mg-timeline');
    const arch = document.querySelector('.mg-arch');
    let queued = false;

    const onScroll = () => {
        queued = false;
        const max = root.scrollHeight - window.innerHeight;
        root.style.setProperty('--mg-scroll', max > 0 ? (window.scrollY / max).toFixed(4) : 0);
        if (bar) bar.classList.toggle('is-scrolled', window.scrollY > 24);

        if (timeline) {
            const box = timeline.getBoundingClientRect();
            const mark = window.innerHeight * 0.62;
            const drawn = Math.max(0, Math.min(1, (mark - box.top) / box.height));
            timeline.style.setProperty('--mg-draw', drawn.toFixed(4));
            timeline.querySelectorAll('li').forEach((li) => {
                li.classList.toggle('is-on', li.getBoundingClientRect().top + 18 < mark);
            });
        }

        if (arch && !calm && window.innerWidth > 900) {
            arch.style.transform = 'translateY(' + Math.min(60, window.scrollY * 0.08).toFixed(1) + 'px)';
        }
    };

    const requestScroll = () => {
        if (queued) return;
        queued = true;
        requestAnimationFrame(onScroll);
    };

    window.addEventListener('scroll', requestScroll, { passive: true });
    window.addEventListener('resize', requestScroll);
    onScroll();

    /* ---- Hero: a soft light follows the pointer ---- */
    const hero = document.querySelector('.mg-hero');
    if (hero && finePointer && !calm) {
        hero.addEventListener('pointermove', (e) => {
            const box = hero.getBoundingClientRect();
            hero.style.setProperty('--mg-px', (e.clientX - box.left) + 'px');
            hero.style.setProperty('--mg-py', (e.clientY - box.top) + 'px');
            hero.style.setProperty('--mg-glow', 1);
        });
        hero.addEventListener('pointerleave', () => hero.style.setProperty('--mg-glow', 0));
    }

    /* ---- Drifting points of light (hero and the careers band) ---- */
    const startMotes = (host) => {
        const canvas = host.querySelector('.mg-motes');
        if (!canvas || calm) return;
        const ctx = canvas.getContext('2d');
        let motes = [];
        let w = 0, h = 0, running = false, frame = 0;

        const size = () => {
            const ratio = Math.min(window.devicePixelRatio || 1, 2);
            w = host.clientWidth;
            h = host.clientHeight;
            canvas.width = w * ratio;
            canvas.height = h * ratio;
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
            const count = Math.round(Math.min(70, Math.max(18, (w * h) / 16000)));
            motes = Array.from({ length: count }, () => ({
                x: Math.random() * w,
                y: Math.random() * h,
                r: 0.6 + Math.random() * 1.8,
                vy: 0.08 + Math.random() * 0.28,
                sway: Math.random() * Math.PI * 2,
                swaySpeed: 0.004 + Math.random() * 0.01,
                glow: 0.25 + Math.random() * 0.55,
            }));
        };

        const draw = () => {
            if (!running) return;
            ctx.clearRect(0, 0, w, h);
            motes.forEach((m) => {
                m.y -= m.vy;
                m.sway += m.swaySpeed;
                m.x += Math.sin(m.sway) * 0.25;
                if (m.y < -6) { m.y = h + 6; m.x = Math.random() * w; }
                const twinkle = m.glow * (0.65 + 0.35 * Math.sin(m.sway * 2.3));
                ctx.beginPath();
                ctx.arc(m.x, m.y, m.r, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(230, 207, 154, ' + twinkle.toFixed(3) + ')';
                ctx.fill();
            });
            frame = requestAnimationFrame(draw);
        };

        const stop = () => { running = false; cancelAnimationFrame(frame); };
        const start = () => { if (!running) { running = true; draw(); } };

        size();
        window.addEventListener('resize', size);

        /* Only animate while this block is on screen and the tab is visible. */
        let onScreen = false;
        new IntersectionObserver(([entry]) => {
            onScreen = entry.isIntersecting;
            if (onScreen && !document.hidden) start(); else stop();
        }).observe(host);

        document.addEventListener('visibilitychange', () => {
            if (document.hidden || !onScreen) stop(); else start();
        });
    };

    document.querySelectorAll('.mg-hero, .mg-careers').forEach(startMotes);

    /* ---- Announcement banners: people and background shift with the pointer ---- */
    const sliderFrame = document.querySelector('.mg-slider-frame');
    if (sliderFrame && finePointer && !calm) {
        sliderFrame.addEventListener('pointermove', (e) => {
            const box = sliderFrame.getBoundingClientRect();
            sliderFrame.style.setProperty('--mg-tilt-x', (((e.clientX - box.left) / box.width - 0.5) * 2).toFixed(3));
            sliderFrame.style.setProperty('--mg-tilt-y', (((e.clientY - box.top) / box.height - 0.5) * 2).toFixed(3));
        });
        sliderFrame.addEventListener('pointerleave', () => {
            sliderFrame.style.setProperty('--mg-tilt-x', 0);
            sliderFrame.style.setProperty('--mg-tilt-y', 0);
        });
    }

    /* ---- Position cards: light follows the pointer ---- */
    if (finePointer) {
        document.querySelectorAll('.mg-job').forEach((card) => {
            card.addEventListener('pointermove', (e) => {
                const box = card.getBoundingClientRect();
                card.style.setProperty('--mx', (e.clientX - box.left) + 'px');
                card.style.setProperty('--my', (e.clientY - box.top) + 'px');
            });
        });
    }

    /* ---- Campus photos tilt toward the pointer ---- */
    if (finePointer && !calm) {
        document.querySelectorAll('.mg-campus-item').forEach((item) => {
            item.addEventListener('pointermove', (e) => {
                const box = item.getBoundingClientRect();
                const x = (e.clientX - box.left) / box.width - 0.5;
                const y = (e.clientY - box.top) / box.height - 0.5;
                item.classList.add('is-tilting');
                item.style.transform = 'rotateY(' + (x * 7).toFixed(2) + 'deg) rotateX(' + (-y * 7).toFixed(2) + 'deg) translateZ(8px)';
            });
            item.addEventListener('pointerleave', () => {
                item.classList.remove('is-tilting');
                item.style.transform = '';
            });
        });
    }
})();
</script>
@endsection