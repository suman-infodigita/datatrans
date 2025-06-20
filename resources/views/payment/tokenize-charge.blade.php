@extends('layouts.app')

@section('content')
     <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Charge a Saved Card</h2>

        @if ($tokens->isEmpty())
            <div class="text-center text-gray-600">
                <p>No saved cards available.</p>
                <a href="{{ route('token.register') }}" class="text-blue-600 hover:underline mt-2 block">
                    Register a new card
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('token.charge') }}">
                @csrf

                <div class="mb-4">
                    <label for="token_id" class="block text-sm font-medium text-gray-700 mb-1">Select a Saved Card</label>
                    <select name="token_id" id="token_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @foreach ($tokens as $token)
                            <option value="{{ $token->token_id }}">Token ID: {{ $token->token_id }} — State: {{ $token->state }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (CHF)</label>
                    <input type="number" name="amount" id="amount" min="0.01" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    Pay Now
                </button>
            </form>
        @endif
    </div>
@endsection
