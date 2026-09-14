@extends('layouts.shelter.app')
@section('title','Adoption Requests')
@section('content')
<div class="sh-breadcrumb"><a href="{{ route('shelter.dashboard') }}">Dashboard</a><span>›</span><a href="{{ route('shelter.applications.index') }}">Adoption Requests</a><span>›</span><span>Request #{{ $application->id }}</span></div>
<div class="sh-heading"><div><h1>Adoption Request #{{ $application->id }}</h1><p>Review the applicant and animal details.</p></div><a class="sh-button" href="{{ route('shelter.applications.index',['selected'=>$application->id]) }}">Back to Requests</a></div>
<div style="max-width:850px;margin:auto">@include('shelter.applications.detail',['full'=>true])</div>
@endsection
