@extends('errors.layout')

@section('tone', 'amber')
@section('icon', 'bi-shield-lock')
@section('code', '403')
@section('heading', 'Access denied')
@section('message', 'You do not have permission to open this page. If you think this is a mistake, try signing in with a different account.')

@section('actions')
    <button type="button" class="ui-err__btn ui-err__btn--ghost" data-back>
        <i class="bi bi-arrow-left"></i> Go back
    </button>
    <a href="{{ config('site.login_url') }}" class="ui-err__btn ui-err__btn--ghost">
        <i class="bi bi-box-arrow-in-right"></i> Sign in
    </a>
@endsection
