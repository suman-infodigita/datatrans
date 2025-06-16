@extends('layouts.app')

@section('content')
    <div class="bg-gray-100 flex items-center justify-center h-screen">
        <form method="POST" action="{{ route('wallee.start') }}" class="bg-white p-6 rounded shadow-md w-96">
            @csrf
            <h2 class="text-lg font-bold mb-4">Start Payment</h2>
            <input type="number" name="amount" class="w-full mb-4 p-2 border rounded" placeholder="Enter Amount" required>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Pay Now</button>
        </form>
    </div>
<style>
.input {
    display: block;
    width: 100%;
    padding: 0.5rem;
    margin-top: 0.25rem;
    margin-bottom: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.375rem;
}
</style>
@endsection
