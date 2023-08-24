<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\PaymentsService;

class PaymentController extends Controller
{

    private $service;

    public function __construct(PaymentsService $PaymentsService)
    {
        $this->service = $PaymentsService;
    }

    public function processPayment(Request $request)
    {
        try {
            return $this->service->creditCard($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error genera el pago'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // $client = new Payeezy_Client;
        // $client->setApiKey("6zAIMDwSbAraHGzEmnIDSjqNHD3IbDCj");
        // $client->setApiSecret("d161d79cb1d1388aa73c6eac636da34d1cc709d7be25a35f847af2552734687f");
        // $client->setMerchantToken("fdoa-04aef7a7d0858973efc1349176021fab98459e40b376c1ad");
        // $client->setTokenUrl("https://api-cert.payeezy.com/v1/transactions/tokens");
        // $client->setUrl("https://api-cert.payeezy.com/v1/transactions");

        // $transaction = new Payeezy_Transaction($client);

        // $response = $transaction->doPrimaryTransaction([
        //     "merchant_ref" => "Astonishing-Sale",
        //     "transaction_type" => "purchase",
        //     "method" => "credit_card",
        //     "amount" => "1299",
        //     "currency_code" => "USD",
        //     "credit_card" => array(
        //         "type" => "visa",
        //         "cardholder_name" => "John Smith",
        //         "card_number" => "4788250000028291",
        //         "exp_date" => "1020",
        //         "cvv" => "123"
        //     )
        // ]);

        // dd($response);


        // $amount = 100;
        // $currency = "USD";
        // $cardNumber = "4111111111111111";
        // $expirationDate = "2024-08";
        // $cvv = "123";

        // $payeezy = new Payeezy(
        //     config("3176752955"),
        //     config("fdoa-04aef7a7d0858973efc1349176021fab98459e40b376c1ad")
        // );

        // $response = $payeezy->charge($amount, $currency, $cardNumber, $expirationDate, $cvv);

        // try {
        //     if ($response->isSuccessful()) {
        //         return response()->json(['data' => $response], Response::HTTP_OK);
        //     } else {
        //         return response()->json(['data' => $response], Response::HTTP_NOT_FOUND);
        //     }
        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Ocurrió un error genera el pago'], Response::HTTP_INTERNAL_SERVER_ERROR);
        // }



        // return response()->json($response);
    }
}
