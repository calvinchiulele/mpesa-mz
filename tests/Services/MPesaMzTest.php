<?php

namespace CalvinChiulele\MPesaMz\Tests\Services;

use abdulmueid\mpesa\interfaces\TransactionResponseInterface;
use abdulmueid\mpesa\TransactionResponse;
use CalvinChiulele\MPesaMz\Services\MPesaMz;
use PHPUnit\Framework\TestCase;

/**
 * @author Calvin Chiulele <cchiulele@protonmail.com>
 * @since 0.1.0
 */
class MPesaMzTest extends TestCase
{
    /**
     * Transaction class
     *
     * @var \abdulmueid\mpesa\Transaction
     */
    private $transaction;

    /**
     * The amount of the transaction
     *
     * @var float
     */
    private $amount;

    /**
     * The MSISDN of the customer
     *
     * @var string
     */
    private $msisdn;

    /**
     * Setup the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->transaction = new MPesaMz(__DIR__.'/../config/mpesa-config-test.php');
        $this->amount = 1;
        $this->msisdn = '258840383908'; // Full MSISDN i.e. 258840000000
    }

    /**
     * Verifies if the payment was performed successfully
     *
     * @covers \CalvinChiulele\MPesaMz\Services\MpesaMz::payment
     * @return TransactionResponseInterface
     * @throws \Exception
     */
    public function testPayment(): TransactionResponseInterface
    {
        $payment = $this->transaction->payment(
            $this->msisdn, $this->amount,
            bin2hex(random_bytes(6)),
            bin2hex(random_bytes(6))
        );

        $this->assertInstanceOf(TransactionResponse::class, $payment);

        $this->assertNotEmpty($payment->getResponse());
        $this->assertNotEmpty($payment->getCode());
        $this->assertNotEmpty($payment->getDescription());
        $this->assertNotEmpty($payment->getTransactionID());
        $this->assertNotEmpty($payment->getConversationID());
        $this->assertEmpty($payment->getTransactionStatus());
        $this->assertStringStartsWith('INS-', $payment->getCode());

        return $payment;
    }

    /**
     * Verifies if the refund was performed successfully
     *
     * @covers \CalvinChiulele\MPesaMz\Services\MpesaMz::refund
     * @depends testPayment
     * @param  TransactionResponseInterface  $payment
     * @return TransactionResponseInterface
     */
    public function testRefund(TransactionResponseInterface $payment): TransactionResponseInterface
    {
        $refund = $this->transaction->refund(
            $payment->getTransactionID(), 
            $this->amount
        );

        $this->assertInstanceOf(TransactionResponse::class, $refund);

        $this->assertNotEmpty($refund->getResponse());
        $this->assertNotEmpty($refund->getCode());
        $this->assertNotEmpty($refund->getDescription());
        $this->assertNotEmpty($refund->getTransactionID());
        $this->assertNotEmpty($refund->getConversationID());
        $this->assertEmpty($refund->getTransactionStatus());
        $this->assertStringStartsWith('INS-', $refund->getCode());

        return $refund;
    }

    /**
     * Verifies if the query was performed successfully
     *
     * @covers \CalvinChiulele\MPesaMz\Services\MpesaMz::query
     * @depends testRefund
     * @param  TransactionResponseInterface  $refund
     */
    public function testQuery(TransactionResponseInterface $refund)
    {
        $query = $this->transaction->query($refund->getTransactionID());

        $this->assertInstanceOf(TransactionResponse::class, $query);

        $this->assertNotEmpty($query->getResponse());
        $this->assertNotEmpty($query->getCode());
        $this->assertNotEmpty($query->getDescription());
        $this->assertEmpty($query->getTransactionID());
        $this->assertEmpty($query->getConversationID());
        $this->assertNotEmpty($query->getTransactionStatus());
        $this->assertStringStartsWith('INS-', $query->getCode());
    }
}
