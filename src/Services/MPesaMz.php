<?php

namespace CalvinChiulele\MPesaMz\Services;

use abdulmueid\mpesa\Config;
use abdulmueid\mpesa\interfaces\TransactionResponseInterface;
use abdulmueid\mpesa\Transaction;

/**
 * This class is responsible to forward requests to Transaction
 *
 * @author Calvin Chiulele <cchiulele@protonmail.com>
 * @since 0.1.0
 * @see Transaction
 */
class MPesaMz
{
    /**
     * Third-party class that defines the M-Pesa public API
     *
     * @var Transaction
     */
    protected $transaction;

    /**
     * Path of the config file of M-Pesa API
     *
     * @var string
     */
    protected $configPath;

    /**
     * Create a new instance
     *
     * @param  string  $configPath
     * @return void
     */
    public function __construct(string $configPath = null)
    {
        $this->configPath = $configPath ?? config_path('mpesa-config.php');
        $this->transaction = new Transaction(Config::loadFromFile($this->configPath));
    }

    /**
     * Forwards the calls to transaction class
     *
     * @param  string  $name
     * @param  array  $arguments
     *
     * @return TransactionResponseInterface
     */
    public function __call(string $name, array $arguments): TransactionResponseInterface
    {
        return call_user_func_array([$this->transaction, $name], $arguments);
    }
}
