# Laravel Package for Vodacom Mozambique M-Pesa

This package is a wrapper for abdulmueid/mpesa-php-api to integrate M-Pesa API easier in Laravel applications

For more information of what M-Pesa is, please refer to M-Pesa official website: https://www.vm.co.mz/en/M-Pesa2

For more information of what abdulmueid/mpesa-php-api is, refer to https://github.com/abdulmueid/mpesa-php-api

# 1. Installation

All you have to do is the following:
```
composer require calvinchiulele/mpesa-mz
```

```
php artisan vendor:publish
```

After hitting the above command in terminal, you will be provided a list of all service providers registered 
in your application. Choose the `CalvinChiulele\MPesaMz\Providers\MPesaMzServiceProvider` and hit enter.
Now if you check your config folder, you'll find your mpesa-config.php file in there.

# 2. Configuration

After you have published the config files necessary for the package, you've to add all the keys 
necessary for the config file in your .env in order to the config file work properly.

The .env should be like that:

```env
MPESA_PUBLIC_KEY=xxx
MPESA_API_HOST="api.sandbox.vm.co.mz"
MPESA_API_KEY=xxx
MPESA_ORIGIN=xxx
MPESA_SERVICE_PROVIDER_CODE=xxx
MPESA_INITIATOR_IDENTIFIER=xxx
MPESA_SECURITY_CREDENTIAL=xxx
```

Where xxx is your data.
**Note: You've to either use MPESA_API_HOST to reflect to "api.sandbox.vm.co.mz" or production URL from M-Pesa API**

And the newly file created `config/mpesa-config.php` will be like that:

**Note : After adding the entries in the .env file, you've to run ``` php artisan config:clear ``` in your terminal in order to remove the configuration cached file and have the just added values in `.env` available to be used in `config/mpesa-config.php`**


```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public key for use in M-Pesa API
    |--------------------------------------------------------------------------
    |
    | Here you may specify the public key provided by Vodacom to you
    |
    */
    'public_key' => env('MPESA_PUBLIC_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | API host of M-Pesa API
    |--------------------------------------------------------------------------
    |
    | Here you may specify the API host provided by Vodacom for API operations
    |
    */
    'api_host' => env('MPESA_API_HOST', 'api.sandbox.vm.co.mz'),

    /*
    |--------------------------------------------------------------------------
    | API Key of M-Pesa API
    |--------------------------------------------------------------------------
    |
    | Here you may specify the API key provided by Vodacom to you
    |
    */
    'api_key' => env('MPESA_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Origin of M-Pesa API
    |--------------------------------------------------------------------------
    |
    | Here you may specify the API key provided by Vodacom to you
    |
    */
    'origin' => env('MPESA_ORIGIN', ''),

    /*
    |--------------------------------------------------------------------------
    | Service Provider Code of M-Pesa API
    |--------------------------------------------------------------------------
    |
    | Here you may specify the service provider code of M-Pesa provided by Vodacom to you
    |
    */
    'service_provider_code' => env('MPESA_SERVICE_PROVIDER_CODE', ''),

    /*
    |--------------------------------------------------------------------------
    | Initiator Identifier of M-Pesa API
    |--------------------------------------------------------------------------
    |
    | Here you may the initiator identifier provided by Vodacom to you
    |
    */
    'initiator_identifier' => env('MPESA_INITIATOR_IDENTIFIER', ''),

    /*
    |--------------------------------------------------------------------------
    | Security credential of M-Pesa API
    |--------------------------------------------------------------------------
    |
    | Here you may specify the security credential provided by Vodacom to you
    |
    */
    'security_credential' => env('MPESA_SECURITY_CREDENTIAL', '')
];
```

# 3. Usage

In order to use the package in your Laravel application, you can use either the facade or get an instance of the
M-Pesa service on the Laravel service container.

# 3.1 - Using the M-Pesa facade
 
```php
<?php

namespace App\Http\Controllers;

use CalvinChiulele\MPesaMz\Facades\MPesaMz;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    // more code

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // This call is a C2B transaction, which is used as a standard customer-to-business transaction. Funds from the customer’s mobile money wallet will be deducted and be transferred to the mobile money wallet of the business. To authenticate and authorize this transaction, M-Pesa Payments Gateway will initiate a USSD Push message to the customer's phone to gather and verify the mobile money PIN number. This number is not stored and is used only to authorize the transaction.

        // $request->msisdn corresponds to the customer's phone number (e.g: 258840000000)

        // 100 corresponds to the amount of the transaction. Please, do best pratices and not use magic numbers.

        // $reference corresponds to the reference of the transaction for the customer or business making the transaction. This can be a smartcard number for a TV subscription or a reference number of a utility bill.

        // $thirdPartyReference corresponds to the unique reference of the third party system. When there are queries about transactions, this will usually be used to track a transaction.
        $payment = MPesaMz::payment($request->msisdn, 100, $reference, $thirdPartyReference);

        // This call is a refund or reversal, which is used to reverse a successful transaction. // Using the Transaction ID of a previously successful transaction, M-Pesa Payments Gateway will withdraw the funds from the recipient party’s mobile money wallet and revert the funds to the mobile money wallet of the initiating party of the original transaction. 

        // $payment->getTransactionID() corresponds to the transaction ID of the just made sucessfully transaction. It's used to query transactions on the mobile money platform.

        // 100 corresponds to the amount to be reversed.
        $refund = MPesaMz::refund($payment->getTransactionID(), 100);

        // This call is to query a transaction status, which determines the current status of a particular transaction. Using either the Transaction ID or the Conversation ID of a transaction just made, the M-Pesa Payments Gateway will return information about the transaction’s status.

        $query = MPesaMz::query($refund->getTransactionID());

        // more code
    }
}

```

# 3.2 - Using the M-Pesa service from Laravel service container
 
``` php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * M-Pesa service class
     *
     * @var \CalvinChiulele\MPesaMz\Services\MPesaMz
     */
    protected $mpesaService;

    /**
     * Create a new instance
     *
     * @return void
     */
    public function __construct()
    {
        $this->mpesaService = app('CalvinChiulele\MPesaMz\Services\MPesaMz');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // The documentation on the example above serves for this example.

        $payment = $this->mpesaService->payment($request->msisdn, 100, $reference, $thirdPartyReference);

        $refund = $this->mpesaService->refund($payment->getTransactionID(), 100);

        $query = $this->mpesaService->query($refund->getTransactionID());

        // more code
    }
}

```

# 4. Testing
1. Update tests/config/mpesa-config-test.php with required parameters
2. Enter the test amount and MSISDN in tests/Services/MPesaMzTest.php, respectively on lines 47 and 48
3. Run **PHPUnit 9** binary in vendor/bin/
4. Check the phone for M-Pesa payment requests

The test case currently creates a new transaction, refunds the transaction and queries the transaction status.
**Tests may be billable when running on production.**

# 5. License

This library is release under the MIT License. See LICENSE file for details.

# 6. Authors

Calvin Carlos da Conceição Chiulele <cchiulele@protonmail.com> and contributors

This package is still in development phase, so any suggestions, improvements and recommendations are welcomed.
