<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WalleeService;
use Wallee\Sdk\Service\TransactionService;
use Wallee\Sdk\Model\TransactionState;

class TokenPaymentController extends Controller
{
    protected $wallee;

    public function __construct(WalleeService $wallee)
    {
        $this->wallee = $wallee;
    }

    public function registerCard()
    {
        $amount = 0.01; // Minimal charge
        $reference = 'TOKENIZE-' . time();

        $result = $this->wallee->createTransactionWithToken(
            $amount,
            session()->getId(),
            $reference
        );
        info($result);
        return redirect()->away($result['redirectUrl']);
    }

    public function chargeForm()
    {
        $tokens = $this->wallee->getUserTokens();
        return view('payment.tokenize-charge', compact('tokens'));
    }

    public function charge(Request $request)
    {
        $tokenId = $request->input('token_id');
        $amount = $request->input('amount');
        $reference = 'CHARGE-' . time();

        $txnId = $this->wallee->chargeSavedCard($tokenId, $amount, $reference);

        // Delay or redirect to status check
        return redirect()->route('token.status', ['transaction_id' => $txnId]);
    }

    public function success()
    {
        return view('payment.success');
    }
    public function fail()
    {
        return view('payment.fail');
    }

    public function transactionStatus(Request $request){
    $txnId = $request->get('transaction_id');

    $txnSvc = new TransactionService($this->wallee->apiClient);
    $txn = $txnSvc->read($this->wallee->spaceId, $txnId);

    if ($txn->getState() === TransactionState::FAILED) {
        return view('payment.fail', ['message' => $txn->getFailureReason()?->getDescription()]);
    }

    if ($txn->getState() === TransactionState::FULFILL || $txn->getState() === TransactionState::COMPLETED) {
        return redirect()->route('token.success');
    }

    // Still pending
    return view('payment.tokenize-waiting', ['state' => $txn->getState()]);
}
}
