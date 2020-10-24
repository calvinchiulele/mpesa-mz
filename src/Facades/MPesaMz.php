<?php

namespace CalvinChiulele\MPesaMz\Facades;

use CalvinChiulele\MPesaMz\Services\MPesaMz as MPesaMzService;
use Illuminate\Support\Facades\Facade;

/**
 * @author Calvin Chiulele <cchiulele@protonmail.com>
 *
 * @method abdulmueid\mpesa\interfaces\TransactionResponseInterface payment(string $msisdn, float $amount, string $reference, string $third_party_reference)
 * @method abdulmueid\mpesa\interfaces\TransactionResponseInterface refund(string $transaction_id, float $amount)
 * @method abdulmueid\mpesa\interfaces\TransactionResponseInterface query(string $query_reference)
 *
 * @since 0.1.0
 * @see \CalvinChiulele\MPesaMz\Services\MPesaMz
 */
class MPesaMz extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MPesaMzService::class;
    }
}
