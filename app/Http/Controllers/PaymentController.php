<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        $cards = session()->get('saved_cards', []);
        return view('payment.form', compact('cards'));
    }

    public function tokenizeCard(Request $request)
    {
        // if (app()->environment('local') && $request->card_number === '4242424242424242') {
        //     // $alias = 'visa-' . rand(1000, 9999);
        //     $alias = Str::upper(Str::random(20));;

        //     $cards = session()->get('saved_cards', []);
        //     $cards[] = [
        //         'alias' => $alias,
        //         'last4' => substr($request->card_number, -4),
        //         'brand' => 'VISA',
        //     ];
        //     session()->put('saved_cards', $cards);

        //     return back()->with('success', 'Card MOCK-tokenized for testing.');
        // }

        $response = Http::withBasicAuth(env('DATATRANS_USERNAME'), env('DATATRANS_PASSWORD'))
            ->post(env('DATATRANS_BASE_URL') . '/v1/transactions/secureFields/tokenize', [
                'card' => [
                    'number' => $request->card_number,
                    'expiryMonth' => $request->expiry_month,
                    'expiryYear' => $request->expiry_year,
                    'cvv' => $request->cvv,
                ],
                'cardholder' => [
                    'name' => $request->cardholder_name,
                ],
            ]);
    

        info([$response->status(), $response->body()]);
        if ($response->successful()) {
            $data = $response->json();

            $cards = session()->get('saved_cards', []);
            $cards[] = [
                'alias' => $data['alias'],
                'last4' => substr($request->card_number, -4),
                'brand' => 'VISA', // dummy, customize as needed
            ];
            session()->put('saved_cards', $cards);

            return back()->with('success', 'Card tokenized successfully.');
        }

        return back()->withErrors(['message' => 'Tokenization failed.']);
    }


    public function processPayment(Request $request)
    {
        dd($request->all());
         if (app()->environment('local') && str_starts_with($request->alias, 'mock-alias-')) {
            return back()->with('success', 'MOCK Payment successful (no charge).');
         }
        $idempotencyKey = (string) Str::uuid();
        //   dd($idempotencyKey);
        $payload = [
            'currency' => 'CHF',
            'refno' => 'Test-' . uniqid(),
            'amount' => 1000,
            'card' => [
                'alias' => $request->alias,
                'expiryMonth' => 12,
                'expiryYear' => 29,
            ],
            'autoSettle' => true,
        ];

        $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Idempotency-Key' => $idempotencyKey,
            ])
    ->withBasicAuth(env('DATATRANS_USERNAME'), env('DATATRANS_PASSWORD'))
    ->post(env('DATATRANS_BASE_URL') . '/v1/transactions/authorize', $payload);
        if ($response->successful()) {
            return back()->with('success', 'Payment successful via alias.');
        }

        // Debug error
        dd($response->status(), $response->body());

        return back()->withErrors(['message' => 'Payment failed.']);
    }

    public function clearAliases()
    {
        session()->forget('saved_cards');
        return back()->with('success', 'Saved cards cleared.');
    }

    public function initPayment(){

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode(env('DATATRANS_USERNAME') . ':' . env('DATATRANS_PASSWORD')),
            'Content-Type' => 'application/json',
        ])->post(env('DATATRANS_BASE_URL') . '/v1/transactions', [
            'currency' => 'CHF',
            'refno' => 'Test-1234',
            'amount' => 1000,
            'paymentMethods' => ['ECA'],
        ]);

        info([$response->status(), $response->body()]);
        return redirect()->back();
    }

}
