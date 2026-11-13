@extends('errors.layout')
@section('tone', 'sky')
@section('icon', 'bi-wrench-adjustable')
@section('code', '503')
@section('heading', 'Be right back')
@section('message', 'We are doing some quick maintenance. Please check again in a few minutes.')
@section('actions')
    <button type="button" class="ui-err__btn ui-err__btn--ghost" onclick="location.reload()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
@endsection
