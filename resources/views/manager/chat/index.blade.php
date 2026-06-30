@extends('layouts.app')

@section('title', 'Staff Chat')
@section('page-title', 'Staff Chat')
@section('page-subtitle', 'Message your team in real time')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
    @include('shared.chat-panel', ['routePrefix' => 'manager'])
@endsection
