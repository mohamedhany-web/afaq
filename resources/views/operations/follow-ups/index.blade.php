@extends('layouts.app')
@section('page-title', 'متابعات العمليات')

@section('content')
@include('crm.follow-ups.partials.workspace', ['workspace' => 'operations'])
@endsection
