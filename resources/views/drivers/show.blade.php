@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4" style="font-weight: bold;">Chauffeur Details</h2>

    @if(isset($driver))
        <p><strong>Name:</strong> {{ $driver->name }}</p>
        <p><strong>Email:</strong> {{ $driver->email }}</p>
        <p><strong>Phone:</strong> {{ $driver->phone ?? 'Not provided' }}</p>
    @else
        <div class="alert alert-danger text-center">
            🚨 Chauffeur not found.
        </div>
    @endif

    <a href="{{ route('drivers.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
@endsection
