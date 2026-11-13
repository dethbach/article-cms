@extends('errors.layout')

@section('tone', 'rose')
@section('icon', 'bi-tools')
@section('code', '500')
@section('heading', 'Something went wrong')
@section('message', 'An unexpected error happened on our side. It is not your fault. Please try again in a moment.')

@section('actions')
    <button type="button" class="ui-err__btn ui-err__btn--ghost" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise"></i> Try again
    </button>
@endsection
