<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WalleeService;

class WalleePaymentController extends Controller
{
    protected $wallee;

    public function __construct(WalleeService $wallee)
    {
        $this->wallee = $wallee;
    }

    public function showForm()
    {
        return view('wallee.form');
    }

    public function startPayment(Request $request)
    {
        $orderId = 'ORDER-' . now()->timestamp;
        $amount = $request->input('amount');

        $transaction = $this->wallee->createTransaction($orderId, $amount);

        return redirect()->away($this->wallee->getPaymentPageUrl($transaction->getId()));
    }

    public function paymentSuccess()
    {
        return view('payment.success');
    }

    public function paymentFail()
    {
        return view('payment.fail');
    }
}
