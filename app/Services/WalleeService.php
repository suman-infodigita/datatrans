<?php
namespace App\Services;
use Wallee\Sdk\ApiClient;
use Wallee\Sdk\Model\TransactionCreate;
use Wallee\Sdk\Service\TransactionService;
use Wallee\Sdk\Service\TransactionPaymentPageService;
use Wallee\Sdk\Service\TokenService;
use Wallee\Sdk\Model\LineItemCreate;
use Wallee\Sdk\Model\LineItemType;
use App\Models\WalleeToken;

class WalleeService
{
    public $spaceId;
    public $apiClient;

    public function __construct()
    {
        $this->spaceId = env('WALLEE_SPACE_ID');

        $this->apiClient = new ApiClient(env('WALLEE_USER_ID'), env('WALLEE_AUTHENTICATION_KEY'));
    }

    public function createTransaction($orderId, $amount)
    {
        $transactionService = new TransactionService($this->apiClient);

        $transaction = new TransactionCreate();
        $transaction->setCurrency('CHF'); 
        // $transaction->setAmount($amount);
        $transaction->setMerchantReference($orderId);

        $lineItem = new LineItemCreate();
        $lineItem->setName('Order ' . $orderId);
        $lineItem->setQuantity(1);
        $lineItem->setAmountIncludingTax($amount);
        $lineItem->setUniqueId("item-1".uniqid());
        $lineItem->setType(LineItemType::PRODUCT);

        $transaction->setLineItems([$lineItem]);

        return $transactionService->create($this->spaceId, $transaction);
    }

    public function getPaymentPageUrl($transactionId)
    {
        $paymentPageService = new TransactionPaymentPageService($this->apiClient);
        return $paymentPageService->paymentPageUrl($this->spaceId, $transactionId);
    }
    
     public function createTransactionWithToken($amount, $customerId, $reference)
    {
        $txnSvc = new TransactionService($this->apiClient);
        $pageSvc = new TransactionPaymentPageService($this->apiClient);

        $transaction = new TransactionCreate();
        $transaction->setCurrency('CHF');
        // $transaction->setAmount($amount);
        $transaction->setMerchantReference($reference);
        $transaction->setCustomerId($customerId);
        // $transaction->setEnabledForOneClickPayment(true);
        $transaction->setSuccessUrl(route('token.success')); // ✅ add this
        $transaction->setFailedUrl(route('token.failed'));

        $lineItem = new LineItemCreate();
        $lineItem->setName("Order $reference")
                 ->setQuantity(1)
                 ->setAmountIncludingTax($amount)
                 ->setType(LineItemType::PRODUCT)
                 ->setUniqueId('item-1');
        $transaction->setLineItems([$lineItem]);

        $txn = $txnSvc->create($this->spaceId, $transaction);
        $redirectUrl = $pageSvc->paymentPageUrl($this->spaceId, $txn->getId());

        return ['transaction' => $txn, 'redirectUrl' => $redirectUrl];
    }

    public function chargeSavedCard($tokenId, $amount, $reference)
    {
        $txnSvc = new TransactionService($this->apiClient);

        $transaction = new TransactionCreate();
        $transaction->setToken($tokenId);
        // $transaction->setAmount($amount);
        $transaction->setCurrency('CHF');
        $transaction->setMerchantReference($reference);

        $lineItem = new LineItemCreate();
        $lineItem->setName("Charge for $reference");
        $lineItem->setUniqueId('item-1');
        $lineItem->setQuantity(1);
        $lineItem->setAmountIncludingTax($amount);
        $lineItem->setType("PRODUCT");

        $transaction->setLineItems([$lineItem]);

        return $txnSvc->create($this->spaceId, $transaction)->getId();
    }

    public function storeToken($tokenId, $state)
    {
        WalleeToken::updateOrCreate(
            ['token_id' => $tokenId],
            [
                'session_id' => session()->getId(),
                'state' => $state
            ]
        );
    }

    public function getUserTokens()
    {
        return WalleeToken::where('session_id', session()->getId())->get();
    }
}
