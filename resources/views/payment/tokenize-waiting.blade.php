@extends('layouts.app')

@section('content')
    <div class="text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 mb-4 text-blue-600 animate-spin rounded-full border-4 border-blue-200 border-t-transparent"></div>
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">Processing Your Payment</h1>
        <p class="text-gray-600 mb-4">
            Current status: <span class="font-semibold text-blue-700">{{ ucfirst($state) }}</span><br>
            Please wait, this may take a few moments.
        </p>
        <a href="{{ route('token.charge.form') }}" class="text-sm text-blue-600 hover:underline">← Return to payment form</a>
    </div>
@endsection
