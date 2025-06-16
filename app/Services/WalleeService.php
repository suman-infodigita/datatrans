<?php
namespace App\Services;

use Wallee\Sdk\ApiClient;
use Wallee\Sdk\Service\TransactionService;
use Wallee\Sdk\Service\TransactionPaymentPageService;
use Wallee\Sdk\Model\TransactionCreate;

class WalleeService
{
    protected $apiClient;
    protected $spaceId;

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
        $transaction->setAmount($amount);
        $transaction->setMerchantReference($orderId);

        $lineItem = new \Wallee\Sdk\Model\LineItemCreate();
        $lineItem->setName('Order ' . $orderId);
        $lineItem->setQuantity(1);
        $lineItem->setAmountIncludingTax($amount);
        $lineItem->setUniqueId("item-1");
        $lineItem->setType(\Wallee\Sdk\Model\LineItemType::PRODUCT);

        $transaction->setLineItems([$lineItem]);

        return $transactionService->create($this->spaceId, $transaction);
    }

    public function getPaymentPageUrl($transactionId)
    {
        $paymentPageService = new TransactionPaymentPageService($this->apiClient);
        return $paymentPageService->paymentPageUrl($this->spaceId, $transactionId);
    }
}
