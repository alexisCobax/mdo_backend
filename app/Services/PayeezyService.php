<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Payeezy_Client;
use Payeezy_Transaction;

class PayeezyService
{
    private $client;

    public function __construct()
    {
        $this->client = new Payeezy_Client;
        $this->initializePayeezyClient();
    }

    private function initializePayeezyClient()
    {
        $payeezyConfig = Config::get('paymentsgateways.payeezy');
        $this->client->setApiKey($payeezyConfig['apiKey']);
        $this->client->setApiSecret($payeezyConfig['apiSecret']);
        $this->client->setMerchantToken($payeezyConfig['merchantToken']);
        $this->client->setTokenUrl($payeezyConfig['tokenUrl']);
        $this->client->setUrl($payeezyConfig['url']);
    }

    public function creditCard(Request $request)
    {
        try {
            $data = $this->validateJsonRequest($request);

            $transaction = new Payeezy_Transaction($this->client);
            $response = $transaction->doPrimaryTransaction($data);

            return response()->json($response, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateJsonRequest(Request $request)
    {
        return $request->validate([
            'merchant_ref' => 'required',
            'transaction_type' => 'required',
            'method' => 'required',
            'amount' => 'required|numeric',
            'currency_code' => 'required',
            'credit_card.type' => 'required',
            'credit_card.cardholder_name' => 'required',
            'credit_card.card_number' => 'required',
            'credit_card.exp_date' => 'required',
            'credit_card.cvv' => 'required',
        ]);
    }
}
