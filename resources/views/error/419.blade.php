@extends('errors.layout')
@section('tone', 'amber')
@section('icon', 'bi-hourglass-split')
@section('code', '419')
@section('heading', 'Page expired')
@section('message', 'Your session timed out before the form was sent. Go back, refresh the page and submit again.')
@section('actions')
    <button type="button" class="ui-err__btn ui-err__btn--ghost" data-back><i class="bi bi-arrow-left"></i> Go back</button>
@endsection
