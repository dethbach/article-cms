@extends('layouts.sidebar')
@php
$sideTitle = 'Profile';
@endphp

@section('css')
 <link rel="stylesheet" href="{{asset('storage/css/contents.css')}}">

<style>
/* ==========================================
   PROFILE HEADER
========================================== */

.ui-profile {
    --ui-profile-accent: #6366f1;
    --ui-profile-accent-2: #8b5cf6;
    --ui-profile-text: #fff;
    --ui-profile-muted: rgba(255, 255, 255, .72);
    --ui-profile-avatar-bg: #fff;
    --ui-profile-avatar-placeholder: #eef0ff;
    --ui-profile-avatar-text: #4f46e5;

    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 24px;
}

/* Avatar */

.ui-profile__avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}

.ui-profile__avatar {
    width: 108px;
    height: 108px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 4px solid rgba(255, 255, 255, .25);
    border-radius: 1rem;
    object-fit: cover;
    object-position: top;
    background: var(--ui-profile-avatar-bg);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .18);
}

.ui-profile__avatar--placeholder {
    color: var(--ui-profile-avatar-text);
    background: var(--ui-profile-avatar-placeholder);
    font-size: 52px;
}

.ui-profile__avatar-edit {
    position: absolute;
    right: -7px;
    bottom: -7px;
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #303481;
    border-radius: .65rem;
    color: #303481;
    background: #fff;
    cursor: pointer;
    box-shadow: 0 3px 8px rgba(0, 0, 0, .15);
    transition: all .25s ease;
}

.ui-profile__avatar-edit:hover {
    color: #fff;
    background: linear-gradient(
        135deg,
        var(--ui-profile-accent),
        var(--ui-profile-accent-2)
    );
    border-color: var(--ui-profile-accent);
    transform: scale(1.08) rotate(-5deg);
}

/* Identity */

.ui-profile__identity {
    min-width: 0;
    color: var(--ui-profile-text);
}

.ui-profile__identity h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -.4px;
}

.ui-profile__identity p {
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 4px 0 14px;
    color: var(--ui-profile-muted);
    font-size: 13px;
}

/* Delete Picture Button */

/* ==========================================
   PROFILE ACTION BUTTONS
========================================== */

.ui-profile__actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 9px;
}

/* Change Password */

.ui-profile__password {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    min-height: 2.75rem;
    padding: 0 1rem;

    color: #fff;
    font-size: .82rem;
    font-weight: 600;

    background: rgba(255, 255, 255, .12);
    border: 1px solid rgba(255, 255, 255, .22);
    border-radius: .75rem;

    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);

    cursor: pointer;
    transition: all .25s ease;
}

.ui-profile__password i {
    font-size: 1rem;
}

.ui-profile__password:hover {
    color: #fff;
    background: rgba(255, 255, 255, .22);
    border-color: rgba(255, 255, 255, .4);
    transform: translateY(-2px);
}

/* Delete Picture */

.ui-profile__delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 2.75rem;
    height: 2.75rem;
    padding: 0;

    color: rgba(255, 255, 255, .75);
    font-size: 1rem;

    background: rgba(255, 255, 255, .07);
    border: 1px solid rgba(255, 255, 255, .15);
    border-radius: .75rem;

    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);

    cursor: pointer;
    transition: all .25s ease;
}

.ui-profile__delete:hover {
    color: #fff;
    background: rgba(239, 68, 68, .85);
    border-color: rgba(255, 255, 255, .2);
    box-shadow: 0 5px 15px rgba(239, 68, 68, .2);
    transform: translateY(-2px);
}

.ui-profile__password:focus-visible,
.ui-profile__delete:focus-visible {
    outline: none;
    box-shadow: 0 0 0 4px rgba(255, 255, 255, .2);
}

/* Responsive */

@media (max-width: 575.98px) {
    .ui-profile__actions {
        gap: 7px;
    }

    .ui-profile__password {
        padding: 0 .8rem;
        font-size: .78rem;
    }
}

/* Reduced Motion */

@media (prefers-reduced-motion: reduce) {
    .ui-profile__password,
    .ui-profile__delete {
        transition: none;
    }

    .ui-profile__password:hover,
    .ui-profile__delete:hover {
        transform: none;
    }
}

/* Responsive */

@media (max-width: 575.98px) {
    .ui-profile {
        align-items: flex-start;
        gap: 16px;
    }

    .ui-profile__avatar {
        width: 84px;
        height: 84px;
        border-radius: .85rem;
    }

    .ui-profile__avatar--placeholder {
        font-size: 40px;
    }

    .ui-profile__identity h3 {
        font-size: 20px;
        overflow-wrap: anywhere;
    }

    .ui-profile__identity p {
        margin-bottom: 10px;
    }
}

/* Dark Mode */

[data-bs-theme="dark"] .ui-profile {
    --ui-profile-avatar-bg: #1e2433;
    --ui-profile-avatar-placeholder: rgba(129, 140, 248, .14);
    --ui-profile-avatar-text: #a5b4fc;
}

[data-bs-theme="dark"] .ui-profile__avatar {
    border-color: rgba(255, 255, 255, .16);
}



/* ==========================================
   PROFILE UPLOAD DIALOG
========================================== */

.ui-profile-upload {
    --ui-profile-upload-accent: #6366f1;
    --ui-profile-upload-accent-2: #8b5cf6;
    --ui-profile-upload-bg: #f8f9ff;
    --ui-profile-upload-text: #0f172a;
    --ui-profile-upload-muted: #64748b;
    --ui-profile-upload-border: rgba(100, 116, 139, .2);

    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.1rem;
    width: 100%;
}

/* Preview */

.ui-profile-upload__preview-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .55rem;
}

.ui-profile-upload__preview {
    width: 112px;
    height: 112px;
    display: grid;
    place-items: center;
    overflow: hidden;
    color: #818cf8;
    background: var(--ui-profile-upload-bg);
    border: 3px solid rgba(99, 102, 241, .15);
    border-radius: 1.1rem;
    font-size: 3.5rem;
    box-shadow: 0 8px 20px -10px rgba(99, 102, 241, .3);
}

.ui-profile-upload__preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top;
}

.ui-profile-upload__preview-label {
    font-size: .65rem;
    font-weight: 750;
    letter-spacing: 1.2px;
    color: var(--ui-profile-upload-muted);
}

/* Dropzone */

.ui-profile-upload__dropzone {
    width: 100%;
    min-height: 175px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .7rem;
    padding: 1.4rem;
    text-align: center;
    background: var(--ui-profile-upload-bg);
    border: 2px dashed rgba(99, 102, 241, .3);
    border-radius: 1rem;
    cursor: pointer;
    transition: all .25s ease;
}

.ui-profile-upload__dropzone:hover,
.ui-profile-upload__dropzone.is-dragover {
    background: rgba(99, 102, 241, .07);
    border-color: var(--ui-profile-upload-accent);
    transform: translateY(-2px);
}

.ui-profile-upload__upload-icon {
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 1.35rem;
    border-radius: .9rem;
    background: linear-gradient(
        135deg,
        var(--ui-profile-upload-accent),
        var(--ui-profile-upload-accent-2)
    );
    box-shadow: 0 8px 16px -6px #6366f1;
}

.ui-profile-upload__drop-content {
    display: flex;
    flex-direction: column;
    gap: .3rem;
}

.ui-profile-upload__drop-content strong {
    font-size: .9rem;
    color: var(--ui-profile-upload-text);
}

.ui-profile-upload__drop-content span {
    font-size: .8rem;
    color: var(--ui-profile-upload-muted);
}

.ui-profile-upload__drop-content u {
    color: var(--ui-profile-upload-accent);
    font-weight: 650;
}

.ui-profile-upload__formats {
    display: flex;
    gap: .4rem;
}

.ui-profile-upload__formats span {
    padding: .2rem .55rem;
    font-size: .65rem;
    font-weight: 750;
    color: #4f46e5;
    background: rgba(99, 102, 241, .1);
    border-radius: .4rem;
}

/* File Information */

.ui-profile-upload__file {
    width: 100%;
    display: flex;
    align-items: center;
    gap: .8rem;
    padding: .8rem;
    background: rgba(100, 116, 139, .07);
    border: 1px solid var(--ui-profile-upload-border);
    border-radius: .85rem;
}

.ui-profile-upload__file[hidden] {
    display: none;
}

.ui-profile-upload__file-icon {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    color: #6366f1;
    background: rgba(99, 102, 241, .12);
    border-radius: .7rem;
    font-size: 1.2rem;
}

.ui-profile-upload__file-details {
    min-width: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: .2rem;
}

.ui-profile-upload__file-details strong {
    overflow: hidden;
    font-size: .82rem;
    color: var(--ui-profile-upload-text);
    text-overflow: ellipsis;
    white-space: nowrap;
}

.ui-profile-upload__file-details span {
    font-size: .73rem;
    color: var(--ui-profile-upload-muted);
}

.ui-profile-upload__remove {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    color: #ef4444;
    background: rgba(239, 68, 68, .08);
    border: 0;
    border-radius: .55rem;
    cursor: pointer;
    transition: all .2s ease;
}

.ui-profile-upload__remove:hover {
    color: #fff;
    background: #ef4444;
}

/* Notice */

.ui-profile-upload__notice {
    width: 100%;
    display: flex;
    align-items: flex-start;
    gap: .55rem;
    padding: .8rem .9rem;
    color: #64748b;
    background: rgba(100, 116, 139, .07);
    border-radius: .75rem;
    font-size: .76rem;
    line-height: 1.5;
}

.ui-profile-upload__notice i {
    flex-shrink: 0;
    margin-top: .1rem;
    color: #6366f1;
}

/* Error */

.ui-profile-upload__error {
    width: 100%;
    padding: .7rem .85rem;
    color: #dc2626;
    background: rgba(239, 68, 68, .08);
    border: 1px solid rgba(239, 68, 68, .15);
    border-radius: .7rem;
    font-size: .8rem;
    font-weight: 600;
}

.ui-profile-upload__error[hidden] {
    display: none;
}

/* Dark Mode */

[data-bs-theme="dark"] .ui-profile-upload {
    --ui-profile-upload-bg: #181d2b;
    --ui-profile-upload-text: #f1f5f9;
    --ui-profile-upload-muted: #94a3b8;
    --ui-profile-upload-border: rgba(255, 255, 255, .08);
}

[data-bs-theme="dark"] .ui-profile-upload__preview {
    border-color: rgba(129, 140, 248, .25);
}

[data-bs-theme="dark"] .ui-profile-upload__dropzone {
    border-color: rgba(129, 140, 248, .3);
}

[data-bs-theme="dark"] .ui-profile-upload__dropzone:hover,
[data-bs-theme="dark"] .ui-profile-upload__dropzone.is-dragover {
    background: rgba(129, 140, 248, .1);
    border-color: #818cf8;
}

[data-bs-theme="dark"] .ui-profile-upload__drop-content u,
[data-bs-theme="dark"] .ui-profile-upload__formats span {
    color: #a5b4fc;
}

[data-bs-theme="dark"] .ui-profile-upload__formats span {
    background: rgba(129, 140, 248, .15);
}

[data-bs-theme="dark"] .ui-profile-upload__file {
    background: rgba(255, 255, 255, .04);
}

[data-bs-theme="dark"] .ui-profile-upload__file-icon {
    color: #a5b4fc;
    background: rgba(129, 140, 248, .15);
}

[data-bs-theme="dark"] .ui-profile-upload__notice {
    background: rgba(255, 255, 255, .05);
}

[data-bs-theme="dark"] .ui-profile-upload__notice i {
    color: #a5b4fc;
}


/* ==========================================
   DIALOG LAYOUT
========================================== */

.dialog.ui-dialog.ui-dialog--lg {
    height: min(720px, calc(100dvh - 2rem));
    max-height: calc(100dvh - 2rem);
    overflow: hidden;
}

.ui-dialog--lg > form {
    display: flex;
    flex-direction: column;
    flex: 1 1 0;
    min-height: 0;
    overflow: hidden;
}

.ui-dialog--lg .ui-dialog__header {
    flex: 0 0 auto;
}

.ui-dialog--lg .ui-dialog__body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
}

.ui-dialog--lg .ui-dialog__footer {
    flex: 0 0 auto;
    position: relative;
    z-index: 2;
}


/* ==========================================
   DELETE PROFILE PICTURE
========================================== */

.ui-profile-delete-message {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.1rem;
    border: 1px solid rgba(239, 68, 68, .15);
    border-radius: 1rem;
    background: rgba(239, 68, 68, .045);
}

.ui-profile-delete-message__icon {
    display: grid;
    place-items: center;
    flex: 0 0 2.5rem;
    width: 2.5rem;
    height: 2.5rem;
    color: #ef4444;
    background: rgba(239, 68, 68, .1);
    border-radius: .75rem;
}

.ui-profile-delete-message strong {
    display: block;
    margin-bottom: .3rem;
    color: #1e293b;
    font-size: .95rem;
}

.ui-profile-delete-message p {
    margin: 0;
    color: #64748b;
    font-size: .85rem;
    line-height: 1.6;
}

.ui-dialog__btn--danger {
    background: linear-gradient(135deg, #ef4444, #f43f5e);
    box-shadow: 0 8px 18px -8px rgba(239, 68, 68, .5);
}

.ui-dialog__btn--danger:hover {
    filter: brightness(1.06);
}

[data-bs-theme="dark"] .ui-profile-delete-message {
    background: rgba(244, 63, 94, .07);
    border-color: rgba(244, 63, 94, .18);
}

[data-bs-theme="dark"] .ui-profile-delete-message strong {
    color: #f1f5f9;
}

[data-bs-theme="dark"] .ui-profile-delete-message p {
    color: #94a3b8;
}


/* ==========================================
   PROFILE FORM CONTROLS
========================================== */

.ui-profile-control {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    min-height: 3rem;
    border: 1px solid rgba(100, 116, 139, .22);
    border-radius: .85rem;
    background: #fff;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.ui-profile-control:focus-within {
    border-color: #6366f1;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, .12);
}

.ui-profile-control__icon,
.ui-profile-control__prefix {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 3rem;
    align-self: stretch;
    color: #818cf8;
    font-size: 1rem;
}

.ui-profile-control__prefix {
    font-size: .95rem;
    font-weight: 700;
}

.ui-profile-control__input {
    display: block;
    width: 100%;
    min-width: 0;
    min-height: 3rem;
    padding: .7rem .9rem .7rem 0;
    color: #1e293b;
    font-family: inherit;
    font-size: .9rem;
    font-weight: 500;
    background: transparent;
    border: 0;
    outline: none;
    box-shadow: none;
}

.ui-profile-control__input::placeholder {
    color: #94a3b8;
    font-weight: 400;
}

.ui-profile-control__input:focus {
    outline: none;
    border: 0;
    box-shadow: none;
}

/* Dark Mode */

[data-bs-theme="dark"] .ui-profile-control {
    background: #151a27;
    border-color: rgba(255, 255, 255, .1);
}

[data-bs-theme="dark"] .ui-profile-control__input {
    color: #f1f5f9;
}

[data-bs-theme="dark"] .ui-profile-control__input::placeholder {
    color: #64748b;
}

[data-bs-theme="dark"] .ui-profile-control__icon,
[data-bs-theme="dark"] .ui-profile-control__prefix {
    color: #a5b4fc;
}


/* ==========================================
   ACCESSIBILITY - REDUCED MOTION
========================================== */

@media (prefers-reduced-motion: reduce) {
    .ui-profile__avatar-edit,
    .ui-profile-upload__dropzone,
    .ui-profile-upload__remove,
    .ui-profile-control,
    .ui-dialog__btn--danger {
        transition: none;
    }

    .ui-profile-upload__dropzone:hover,
    .ui-profile-upload__dropzone.is-dragover {
        transform: none;
    }

    .ui-dialog__btn--danger {
        transition: none;
    }
}

/* ==========================================
   CHANGE PASSWORD DIALOG
========================================== */

.ui-password-dialog__icon {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
}

/* Form Layout */

.ui-password-form {
    display: flex;
    flex-direction: column;
    gap: 1.35rem;
}

/* Security Notice */

.ui-password-notice {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
    padding: 1rem;
    background: rgba(99, 102, 241, .07);
    border: 1px solid rgba(99, 102, 241, .14);
    border-radius: .9rem;
}

.ui-password-notice__icon {
    display: grid;
    place-items: center;
    flex: 0 0 2.4rem;
    width: 2.4rem;
    height: 2.4rem;
    color: #6366f1;
    background: rgba(99, 102, 241, .12);
    border-radius: .7rem;
}

.ui-password-notice strong {
    display: block;
    margin-bottom: .25rem;
    color: #334155;
    font-size: .85rem;
}

.ui-password-notice p {
    margin: 0;
    color: #64748b;
    font-size: .78rem;
    line-height: 1.55;
}

/* Password Input */

.ui-password-input {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    min-height: 3.1rem;
    background: #fff;
    border: 1px solid rgba(100, 116, 139, .22);
    border-radius: .85rem;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.ui-password-input:focus-within {
    border-color: #6366f1;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, .12);
}

.ui-password-input__icon {
    display: grid;
    place-items: center;
    flex: 0 0 3rem;
    color: #818cf8;
    font-size: 1rem;
}

.ui-password-input__field {
    width: 100%;
    min-width: 0;
    min-height: 3rem;
    padding: .7rem 0;
    color: #1e293b;
    font-family: inherit;
    font-size: .9rem;
    font-weight: 500;
    background: transparent;
    border: 0;
    outline: none;
    box-shadow: none;
}

.ui-password-input__field::placeholder {
    color: #94a3b8;
    font-weight: 400;
}

.ui-password-input__field:focus {
    outline: none;
    border: 0;
    box-shadow: none;
}

/* Eye Toggle */

.ui-password-input__toggle {
    display: grid;
    place-items: center;
    flex: 0 0 3rem;
    align-self: stretch;
    color: #94a3b8;
    background: transparent;
    border: 0;
    border-radius: 0 .85rem .85rem 0;
    cursor: pointer;
    transition: color .2s ease;
}

.ui-password-input__toggle:hover {
    color: #6366f1;
}

.ui-password-input__toggle:focus-visible {
    outline: 2px solid #6366f1;
    outline-offset: -4px;
}

/* Password Strength */

.ui-password-strength {
    display: flex;
    align-items: center;
    gap: .7rem;
    margin-top: .65rem;
}

.ui-password-strength__bars {
    display: flex;
    flex: 1;
    gap: 4px;
}

.ui-password-strength__bars span {
    height: 4px;
    flex: 1;
    background: #e2e8f0;
    border-radius: 10px;
    transition: background .2s ease;
}

.ui-password-strength[data-strength="weak"]
.ui-password-strength__bars span:nth-child(-n+1) {
    background: #ef4444;
}

.ui-password-strength[data-strength="fair"]
.ui-password-strength__bars span:nth-child(-n+2) {
    background: #f59e0b;
}

.ui-password-strength[data-strength="good"]
.ui-password-strength__bars span:nth-child(-n+3) {
    background: #3b82f6;
}

.ui-password-strength[data-strength="strong"]
.ui-password-strength__bars span {
    background: #10b981;
}

.ui-password-strength > span {
    color: #64748b;
    font-size: .7rem;
    font-weight: 600;
    white-space: nowrap;
}

/* Password Match */

.ui-password-match {
    display: block;
    margin-top: .5rem;
    font-size: .75rem;
    font-weight: 600;
}

.ui-password-match.is-match {
    color: #10b981;
}

.ui-password-match.is-mismatch {
    color: #ef4444;
}

/* Dark Mode */

[data-bs-theme="dark"] .ui-password-notice {
    background: rgba(129, 140, 248, .08);
    border-color: rgba(129, 140, 248, .15);
}

[data-bs-theme="dark"] .ui-password-notice strong {
    color: #e2e8f0;
}

[data-bs-theme="dark"] .ui-password-notice p {
    color: #94a3b8;
}

[data-bs-theme="dark"] .ui-password-notice__icon {
    color: #a5b4fc;
    background: rgba(129, 140, 248, .15);
}

[data-bs-theme="dark"] .ui-password-input {
    background: #151a27;
    border-color: rgba(255, 255, 255, .1);
}

[data-bs-theme="dark"] .ui-password-input__field {
    color: #f1f5f9;
}

[data-bs-theme="dark"] .ui-password-input__field::placeholder {
    color: #64748b;
}

[data-bs-theme="dark"] .ui-password-input__icon {
    color: #a5b4fc;
}

[data-bs-theme="dark"] .ui-password-input__toggle {
    color: #94a3b8;
}

[data-bs-theme="dark"] .ui-password-input__toggle:hover {
    color: #a5b4fc;
}

[data-bs-theme="dark"] .ui-password-strength__bars span {
    background: #334155;
}

/* Reduced Motion */

@media (prefers-reduced-motion: reduce) {
    .ui-password-input,
    .ui-password-input__toggle,
    .ui-password-strength__bars span {
        transition: none;
    }
}
</style>

@endsection

@section('contents')
{{-- HERO --}}
<div>
    <!-- Page Header -->
    <div class="content-hero mb-4">
        <div class="content-hero-content">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="hero-eyebrow">
                    <i class="bi bi-person"></i> PROFILE
                </span>
                <span class="hero-dot"></span>
                <span class="hero-status">Manage your account</span>
            </div>

           <!-- Profile Header -->
            <div class="ui-profile">
                <div class="ui-profile__avatar-wrapper">
                    @if($data->photo == null)
                        <div class="ui-profile__avatar ui-profile__avatar--placeholder">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    @else
                        <img src="{{ asset('storage/profile-pic/'.auth()->user()->photo) }}"
                            class="ui-profile__avatar"
                            alt="Profile Picture">
                    @endif

                    <button type="button"
                            class="ui-profile__avatar-edit"
                            data-bs-toggle="dialog"
                            data-bs-target="#dialogPicture"
                            data-bs-backdrop="static"
                            title="Change Picture">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                </div>

                <div class="ui-profile__identity">
                    <h3>{{ $data->name }}</h3>

                    <p>
                        &commat;{{ $data->username }} <span class="hero-dot"></span> {{ $data->email }}
                    </p>

                    <div class="ui-profile__actions">

                        {{-- Edit Information --}}
                        <button type="button"
                                class="btn-hero-primary"
                                data-bs-toggle="dialog"
                                data-bs-target="#dialogPersonalInfo"
                                data-bs-backdrop="static">

                            <i class="bi bi-pencil-fill me-1"></i>
                            Edit Information

                        </button>

                        {{-- Change Password --}}
                        <button type="button"
                                class="ui-profile__password"
                                data-bs-toggle="dialog"
                                data-bs-target="#dialogChangePassword"
                                data-bs-backdrop="static">

                            <i class="bi bi-shield-lock"></i>
                            Change Password

                        </button>

                        {{-- Delete Picture --}}
                        @if($data->photo != null)

                            <button type="button"
                                    class="ui-profile__delete"
                                    data-bs-toggle="dialog"
                                    data-bs-target="#modalDelPic"
                                    title="Delete Profile Picture"
                                    aria-label="Delete Profile Picture">

                                <i class="bi bi-trash3"></i>

                            </button>

                        @endif

                    </div>

                </div>
            </div>

        </div>

        <div class="content-hero-decoration">
            <div class="hero-circle hero-circle-one"></div>
            <div class="hero-circle hero-circle-two"></div>
            <div class="hero-folder">
                <i class="bi bi-person-badge"></i>
            </div>
            <span class="hero-floating-icon hero-icon-one">
                <i class="bi bi-person-circle"></i>
            </span>
            <span class="hero-floating-icon hero-icon-two">
                <i class="bi bi-person-bounding-box"></i>
            </span>
        </div>
    </div>

</div>
{{-- END HERO --}}

<div class="container-fluid px-0">
    <div class="row g-4">
        <div class="sm:col-12 md:col-4 xl:col-4">
            <div class="ui-stat ui-stat--cyan">
                <div class="ui-stat__icon"><i class="bi bi-feather"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Articles Written</span>
                    <h3 class="ui-stat__value">{{ $articlesWritten ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="sm:col-12 md:col-4 xl:col-4">
            <div class="ui-stat ui-stat--sky">
                <div class="ui-stat__icon"><i class="bi bi-send-check"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Articles Published</span>
                    <h3 class="ui-stat__value">{{ $articlesPublished  ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="sm:col-12 md:col-4 xl:col-4">
            <div class="ui-stat ui-stat--indigo">
                <div class="ui-stat__icon"><i class="bi bi-stack"></i></div>
                <div class="ui-stat__content">
                    <span class="ui-stat__label">Total Articles</span>
                    <h3 class="ui-stat__value">{{ $totalArticles ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Change Profile Picture Dialog -->
<dialog id="dialogPicture" class="dialog dialog-slide-up ui-dialog ui-dialog--lg">

    <form action="{{ route('admin.profile.photo.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Header -->
        <div class="ui-dialog__header">
            <div class="ui-dialog__icon">
                <i class="bi bi-camera-fill"></i>
            </div>

            <div class="ui-dialog__heading">
                <h1 class="dialog-title ui-dialog__title">Change Profile Picture</h1>
                <span class="ui-dialog__sub">Personalize your account with a new photo.</span>
            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="dialog"
                    aria-label="Close"></button>
        </div>

        <!-- Body -->
        <div class="ui-dialog__body">

            <div class="ui-profile-upload">

                <!-- Preview -->
                <div class="ui-profile-upload__preview-wrapper">
                    <div class="ui-profile-upload__preview" id="profilePreview">
                        <i class="bi bi-person-fill"></i>
                    </div>

                    <span class="ui-profile-upload__preview-label">
                        PHOTO PREVIEW
                    </span>
                </div>

                <!-- Upload Zone -->
                <label for="profilePhotoInput"
                       class="ui-profile-upload__dropzone"
                       id="profileDropzone">

                    <div class="ui-profile-upload__upload-icon">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>

                    <div class="ui-profile-upload__drop-content">
                        <strong>Drop your image here</strong>
                        <span>or <u>browse files</u> from your device</span>
                    </div>

                    <div class="ui-profile-upload__formats">
                        <span>PNG</span>
                        <span>JPG</span>
                        <span>JPEG</span>
                    </div>

                    <input type="file"
                           id="profilePhotoInput"
                           name="photo"
                           accept=".png,.jpg,.jpeg,image/png,image/jpeg"
                           hidden>
                </label>

                <!-- Selected File -->
                <div class="ui-profile-upload__file" id="profileFileInfo" hidden>
                    <div class="ui-profile-upload__file-icon">
                        <i class="bi bi-file-earmark-image"></i>
                    </div>

                    <div class="ui-profile-upload__file-details">
                        <strong id="profileFileName">image.jpg</strong>
                        <span id="profileFileSize">0 KB</span>
                    </div>

                    <button type="button"
                            class="ui-profile-upload__remove"
                            id="removeProfilePhoto"
                            title="Remove selected image">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Notice -->
                <div class="ui-profile-upload__notice">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>
                        Supported formats: PNG, JPG, JPEG.
                        Maximum file size: 2 MB.
                    </span>
                </div>

                <!-- Error -->
                <div class="ui-profile-upload__error"
                     id="profileUploadError"
                     role="alert"
                     hidden>
                </div>

            </div>

        </div>

        <!-- Footer -->
        <div class="ui-dialog__footer">

            <button type="button"
                    class="ui-dialog__btn ui-dialog__btn--ghost"
                    data-bs-dismiss="dialog">
                Cancel
            </button>

            <button type="submit"
                    class="ui-dialog__btn ui-dialog__btn--primary"
                    id="saveProfilePhoto"
                    disabled>
                <i class="bi bi-check2-circle"></i>
                Save Picture
            </button>

        </div>

    </form>
</dialog>

<dialog id="modalDelPic" class="dialog dialog-slide-up ui-dialog">

    <form action="{{ route('admin.profile.photo.delete') }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="ui-dialog__header">
            <div class="ui-dialog__icon"
                 style="background: linear-gradient(135deg, #ef4444, #f43f5e);">
                <i class="bi bi-trash3"></i>
            </div>

            <div class="flex-grow-1">
                <h5 class="mb-1 fw-bold">Delete Profile Picture</h5>
                <p class="mb-0 small text-secondary">
                    Remove your current profile picture.
                </p>
            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="dialog"
                    aria-label="Close">
            </button>
        </div>

        <div class="ui-dialog__body">
            <div class="ui-profile-delete-message">
                <div class="ui-profile-delete-message__icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>

                <div>
                    <strong>Are you sure?</strong>
                    <p>
                        Your current profile picture will be permanently deleted
                        and replaced with the default profile avatar.
                    </p>
                </div>
            </div>
        </div>

        <div class="ui-dialog__footer">
            <button type="button"
                    class="ui-dialog__btn ui-dialog__btn--ghost"
                    data-bs-dismiss="dialog">
                Cancel
            </button>

            <button type="submit"
                    class="ui-dialog__btn ui-dialog__btn--primary ui-dialog__btn--danger">
                <i class="bi bi-trash3"></i>
                Delete Picture
            </button>
        </div>
    </form>

</dialog>

<dialog id="dialogPersonalInfo" class="dialog dialog-slide-up ui-dialog ui-dialog--lg">
    <form action="{{ route('admin.profile.information.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="ui-dialog__header">
            <div class="ui-dialog__icon">
                <i class="bi bi-person-vcard"></i>
            </div>

            <div class="flex-grow-1">
                <h5 class="mb-1 fw-bold">Edit Personal Information</h5>
                <p class="mb-0 small text-secondary">
                    Update your name, username, and email address.
                </p>
            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="dialog"
                    aria-label="Close">
            </button>
        </div>

        <div class="ui-dialog__body">

            <div class="ui-field">
                <label for="profileName" class="ui-field__label">
                    Full Name <span class="text-danger">*</span>
                </label>

                <div class="ui-profile-control">
                    <span class="ui-profile-control__icon">
                        <i class="bi bi-person"></i>
                    </span>

                    <input type="text"
                        class="ui-profile-control__input"
                        id="profileName"
                        name="name"
                        value="{{ old('name', $data->name) }}"
                        placeholder="Enter your full name"
                        maxlength="255"
                        required>
                </div>

                @error('name')
                    <span class="ui-field__error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ui-field">
                <label for="profileUsername" class="ui-field__label">
                    Username <span class="text-danger">*</span>
                </label>

                <div class="ui-profile-control">
                    <span class="ui-profile-control__prefix">@</span>

                    <input type="text"
                        class="ui-profile-control__input"
                        id="profileUsername"
                        name="username"
                        value="{{ old('username', $data->username) }}"
                        placeholder="Enter your username"
                        maxlength="255"
                        required>
                </div>

                <span class="ui-field__hint">
                    Your username must be unique.
                </span>

                @error('username')
                    <span class="ui-field__error">{{ $message }}</span>
                @enderror
            </div>

            <div class="ui-field">
                <label for="profileEmail" class="ui-field__label">
                    Email Address <span class="text-danger">*</span>
                </label>

                <div class="ui-profile-control">
                    <span class="ui-profile-control__icon">
                        <i class="bi bi-envelope"></i>
                    </span>

                    <input type="email"
                        class="ui-profile-control__input"
                        id="profileEmail"
                        name="email"
                        value="{{ old('email', $data->email) }}"
                        placeholder="Enter your email address"
                        maxlength="255"
                        required>
                </div>

                <span class="ui-field__hint">
                    Changing your email address will require verification again.
                </span>

                @error('email')
                    <span class="ui-field__error">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="ui-dialog__footer">
            <button type="button"
                    class="ui-dialog__btn ui-dialog__btn--ghost"
                    data-bs-dismiss="dialog">
                Cancel
            </button>

            <button type="submit"
                    class="ui-dialog__btn ui-dialog__btn--primary">
                <i class="bi bi-check2-circle"></i>
                Save Changes
            </button>
        </div>

    </form>
</dialog>

<!-- ==========================================
     CHANGE PASSWORD DIALOG
========================================== -->

<dialog id="dialogChangePassword"
        class="dialog dialog-slide-up ui-dialog ui-dialog--lg">

    <form action="{{ route('admin.profile.password.update') }}"
          method="POST"
          id="changePasswordForm">

        @csrf
        @method('PUT')

        <!-- Header -->
        <div class="ui-dialog__header">

            <div class="ui-dialog__icon ui-password-dialog__icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <div class="flex-grow-1">
                <h5 class="mb-1 fw-bold">Change Password</h5>

                <p class="mb-0 small text-secondary">
                    Keep your account secure with a strong password.
                </p>
            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="dialog"
                    aria-label="Close">
            </button>

        </div>

        <!-- Body -->
        <div class="ui-dialog__body">

            <div class="ui-password-form">

                <!-- Security Notice -->
                <div class="ui-password-notice">

                    <div class="ui-password-notice__icon">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>

                    <div>
                        <strong>Security reminder</strong>

                        <p>
                            Choose a password that is difficult to guess.
                            Avoid using personal information or passwords
                            from other accounts.
                        </p>
                    </div>

                </div>


                <!-- Current Password -->
                <div class="ui-field">

                    <label for="currentPassword"
                           class="ui-field__label">

                        Current Password
                        <span class="text-danger">*</span>

                    </label>

                    <div class="ui-password-input">

                        <i class="bi bi-lock ui-password-input__icon"></i>

                        <input type="password"
                               class="ui-password-input__field"
                               id="currentPassword"
                               name="current_password"
                               placeholder="Enter your current password"
                               autocomplete="current-password"
                               required>

                        <button type="button"
                                class="ui-password-input__toggle"
                                data-password-toggle="currentPassword"
                                aria-label="Show current password"
                                aria-pressed="false">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    @error('current_password')
                        <span class="ui-field__error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                <!-- New Password -->
                <div class="ui-field">

                    <label for="newPassword"
                           class="ui-field__label">

                        New Password
                        <span class="text-danger">*</span>

                    </label>

                    <div class="ui-password-input">

                        <i class="bi bi-key ui-password-input__icon"></i>

                        <input type="password"
                               class="ui-password-input__field"
                               id="newPassword"
                               name="password"
                               placeholder="Enter your new password"
                               autocomplete="new-password"
                               minlength="8"
                               required>

                        <button type="button"
                                class="ui-password-input__toggle"
                                data-password-toggle="newPassword"
                                aria-label="Show new password"
                                aria-pressed="false">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    <!-- Password Strength -->
                    <div class="ui-password-strength"
                         id="passwordStrength"
                         hidden>

                        <div class="ui-password-strength__bars">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>

                        <span id="passwordStrengthText">
                            Password strength
                        </span>

                    </div>

                    <span class="ui-field__hint">
                        Use at least 8 characters with a combination
                        of letters, numbers, and symbols.
                    </span>

                    @error('password')
                        <span class="ui-field__error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                <!-- Confirm Password -->
                <div class="ui-field">

                    <label for="confirmPassword"
                           class="ui-field__label">

                        Confirm New Password
                        <span class="text-danger">*</span>

                    </label>

                    <div class="ui-password-input">

                        <i class="bi bi-shield-check ui-password-input__icon"></i>

                        <input type="password"
                               class="ui-password-input__field"
                               id="confirmPassword"
                               name="password_confirmation"
                               placeholder="Re-enter your new password"
                               autocomplete="new-password"
                               minlength="8"
                               required>

                        <button type="button"
                                class="ui-password-input__toggle"
                                data-password-toggle="confirmPassword"
                                aria-label="Show confirmation password"
                                aria-pressed="false">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    <span class="ui-password-match"
                          id="passwordMatch"
                          aria-live="polite"
                          hidden>
                    </span>

                </div>

            </div>

        </div>


        <!-- Footer -->
        <div class="ui-dialog__footer">

            <button type="button"
                    class="ui-dialog__btn ui-dialog__btn--ghost"
                    data-bs-dismiss="dialog">

                Cancel

            </button>

            <button type="submit"
                    class="ui-dialog__btn ui-dialog__btn--primary"
                    id="submitChangePassword">

                <i class="bi bi-shield-check"></i>
                Update Password

            </button>

        </div>

    </form>

</dialog>

@endsection

@section('javascript')

<script>

// ==========================================
// PROFILE PICTURE UPLOAD
// ==========================================

const profileInput = document.getElementById('profilePhotoInput');
const profilePreview = document.getElementById('profilePreview');
const profileDropzone = document.getElementById('profileDropzone');
const profileFileInfo = document.getElementById('profileFileInfo');
const profileFileName = document.getElementById('profileFileName');
const profileFileSize = document.getElementById('profileFileSize');
const profileError = document.getElementById('profileUploadError');
const profileSave = document.getElementById('saveProfilePhoto');
const profileRemove = document.getElementById('removeProfilePhoto');

if (profileInput) {

    const MAX_SIZE = 2 * 1024 * 1024;

    const allowedTypes = [
        'image/png',
        'image/jpeg'
    ];

    let previewUrl = null;

    function showError(message = '') {
        profileError.textContent = message;
        profileError.hidden = !message;
    }

    function clearPreview() {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }

        profilePreview.innerHTML = '<i class="bi bi-person-fill"></i>';
        profileFileInfo.hidden = true;
        profileSave.disabled = true;
    }

    function handleProfileFile(file) {

        showError();

        if (!file) return;

        const extension = file.name.split('.').pop().toLowerCase();

        const allowedExtensions = ['png', 'jpg', 'jpeg'];

        if (
            !allowedExtensions.includes(extension) ||
            !allowedTypes.includes(file.type)
        ) {
            profileInput.value = '';
            clearPreview();
            showError('Invalid file format. Please select PNG, JPG, or JPEG.');
            return;
        }

        if (file.size > MAX_SIZE) {
            profileInput.value = '';
            clearPreview();
            showError('File size exceeds 2 MB. Please select a smaller image.');
            return;
        }

        if (file.size === 0) {
            profileInput.value = '';
            clearPreview();
            showError('The selected file is empty.');
            return;
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        previewUrl = URL.createObjectURL(file);

        const image = new Image();

        image.onload = function () {

            profilePreview.innerHTML = '';
            profilePreview.appendChild(image);

            profileFileName.textContent = file.name;
            profileFileSize.textContent =
                (file.size / 1024).toFixed(1) + ' KB';

            profileFileInfo.hidden = false;
            profileSave.disabled = false;
        };

        image.onerror = function () {
            profileInput.value = '';
            clearPreview();
            showError('Unable to read this image. Please select another file.');
        };

        image.src = previewUrl;
    }

    // File selection
    profileInput.addEventListener('change', function () {
        handleProfileFile(this.files[0]);
    });

    // Remove selected image
    profileRemove.addEventListener('click', function () {
        profileInput.value = '';
        clearPreview();
        showError();
    });

    // Drag over
    profileDropzone.addEventListener('dragover', function (e) {
        e.preventDefault();
        this.classList.add('is-dragover');
    });

    // Drag leave
    profileDropzone.addEventListener('dragleave', function (e) {
        if (!this.contains(e.relatedTarget)) {
            this.classList.remove('is-dragover');
        }
    });

    // Drop file
    profileDropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        this.classList.remove('is-dragover');

        const file = e.dataTransfer.files[0];

        if (!file) return;

        const transfer = new DataTransfer();
        transfer.items.add(file);

        profileInput.files = transfer.files;

        handleProfileFile(file);
    });

    // Reset when dialog closes
    const profileDialog = document.getElementById('dialogPicture');

    profileDialog?.addEventListener('close', function () {
        profileInput.value = '';
        clearPreview();
        showError();
    });

}

</script>

<script>
    // ==========================================
// CHANGE PASSWORD
// ==========================================

(function () {

    const passwordForm = document.getElementById('changePasswordForm');

    if (!passwordForm) return;

    const currentPassword = document.getElementById('currentPassword');
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');

    const passwordStrength = document.getElementById('passwordStrength');
    const passwordStrengthText = document.getElementById('passwordStrengthText');

    const passwordMatch = document.getElementById('passwordMatch');

    // --------------------------------------
    // Toggle Password Visibility
    // --------------------------------------

    passwordForm.querySelectorAll('[data-password-toggle]').forEach(button => {

        button.addEventListener('click', function () {

            const input = document.getElementById(
                this.dataset.passwordToggle
            );

            const icon = this.querySelector('i');

            const isVisible = input.type === 'text';

            input.type = isVisible ? 'password' : 'text';

            icon.classList.toggle('bi-eye', isVisible);
            icon.classList.toggle('bi-eye-slash', !isVisible);

            this.setAttribute(
                'aria-label',
                isVisible ? 'Show password' : 'Hide password'
            );

            this.setAttribute(
                'aria-pressed',
                String(!isVisible)
            );

        });

    });


    // --------------------------------------
    // Password Strength
    // --------------------------------------

    function checkPasswordStrength(password) {

        let score = 0;

        if (password.length >= 8) score++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;

        const levels = [
            '',
            'weak',
            'fair',
            'good',
            'strong'
        ];

        const labels = [
            '',
            'Weak password',
            'Fair password',
            'Good password',
            'Strong password'
        ];

        passwordStrength.dataset.strength = levels[score];
        passwordStrengthText.textContent = labels[score];

    }


    // --------------------------------------
    // Password Confirmation
    // --------------------------------------

    function checkPasswordMatch() {

        const password = newPassword.value;
        const confirmation = confirmPassword.value;

        passwordMatch.hidden = !confirmation;

        passwordMatch.classList.remove(
            'is-match',
            'is-mismatch'
        );

        if (!confirmation) return;

        if (password === confirmation) {

            passwordMatch.textContent = '✓ Passwords match';

            passwordMatch.classList.add('is-match');

        } else {

            passwordMatch.textContent = '✕ Passwords do not match';

            passwordMatch.classList.add('is-mismatch');

        }

    }


    newPassword.addEventListener('input', function () {

        passwordStrength.hidden = !this.value;

        if (this.value) {
            checkPasswordStrength(this.value);
        }

        checkPasswordMatch();

    });


    confirmPassword.addEventListener('input', checkPasswordMatch);


    // --------------------------------------
    // Form Validation
    // --------------------------------------

    passwordForm.addEventListener('submit', function (e) {

        if (newPassword.value !== confirmPassword.value) {

            e.preventDefault();

            checkPasswordMatch();

            confirmPassword.focus();

            return;
        }

        if (currentPassword.value === newPassword.value) {

            e.preventDefault();

            alert('Your new password must be different from your current password.');

            newPassword.focus();

            return;
        }

    });


    // --------------------------------------
    // Reset Dialog
    // --------------------------------------

    const passwordDialog = document.getElementById(
        'dialogChangePassword'
    );

    passwordDialog?.addEventListener('close', function () {

        passwordForm.reset();

        passwordStrength.hidden = true;
        passwordMatch.hidden = true;

        passwordStrength.dataset.strength = '';

        passwordForm.querySelectorAll('[data-password-toggle]').forEach(button => {

            const input = document.getElementById(
                button.dataset.passwordToggle
            );

            input.type = 'password';

            button.querySelector('i').className = 'bi bi-eye';

            button.setAttribute('aria-pressed', 'false');
            button.setAttribute('aria-label', 'Show password');

        });

    });

})();
</script>

@endsection
