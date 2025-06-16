@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Test Payment Form (No Auth)</h2>

    @if (session('success'))
        <div class="bg-green-100 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 p-2 rounded mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/tokenize-card" class="mb-6">
        @csrf
        <h3 class="font-semibold mb-2">Add Card</h3>
        <input type="text" name="cardholder_name" placeholder="Cardholder Name" class="input" required>
        <input type="text" name="card_number" placeholder="Card Number" class="input" required>
        <input type="text" name="expiry_month" placeholder="MM" class="input" required>
        <input type="text" name="expiry_year" placeholder="YY" class="input" required>
        <input type="text" name="cvv" placeholder="CVV" class="input" required>
        <button class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Save Card</button>
    </form>

    @if(count($cards))
    <form method="POST" action="/process-payment">
        @csrf
        <h3 class="font-semibold mb-2">Use Saved Card</h3>
        <select name="alias" class="input" required>
            @foreach($cards as $card)
                <option value="{{ $card['alias'] }}">{{ $card['brand'] }} ending in {{ $card['last4'] }}</option>
            @endforeach
        </select>
        <input type="number" name="amount" placeholder="Amount (e.g. 100)" class="input mt-2" required>
        <button class="bg-green-500 text-white px-4 py-2 rounded mt-2">Pay</button>
    </form>
    @endif
    <form method="POST" action="/clear-cards" class="mb-4">
        @csrf
        <button class="bg-red-500 text-white px-4 py-2 rounded">Clear Saved Cards</button>
    </form>
    <hr>
    <br>
    <form method="POST" action="/init-payment" class="mb-4">
        @csrf
        <button class="bg-green-500 text-white px-4 py-2 rounded">Start Payment</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action="/tokenize-card"]');
    form.addEventListener('submit', function (e) {
        const number = form.card_number.value;
        const cvv = form.cvv.value;
        const expiryMonth = form.expiry_month.value;
        const expiryYear = form.expiry_year.value;

        if (!/^\d{16}$/.test(number)) {
            alert('Card number must be 16 digits.');
            e.preventDefault();
        } else if (!/^\d{3,4}$/.test(cvv)) {
            alert('Invalid CVV.');
            e.preventDefault();
        } else if (!/^\d{2}$/.test(expiryMonth) || !/^\d{2}$/.test(expiryYear)) {
            alert('Invalid expiry date.');
            e.preventDefault();
        }
    });
});
</script>

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
