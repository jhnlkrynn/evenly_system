@extends('layouts.base')

@section('title', 'Browse event services · Eventlynk')

@section('body')
    @include('services.partials.browser', ['embedded' => false])
@endsection
