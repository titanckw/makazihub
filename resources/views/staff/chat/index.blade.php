@extends('layouts.app')

@section('title', 'Messages')
@section('page-title', 'Messages')
@section('page-subtitle', 'Chat with your manager')

@section('sidebar-nav')
    @include('staff.partials.sidebar')
@endsection

@section('content')
    @include('shared.chat-panel', ['routePrefix' => 'staff'])
@endsection
