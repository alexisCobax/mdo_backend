<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CloverService
{
    public function __construct()
    {
    }

    public function creditCard(Request $request)
    {
        try {

            $this->validateJsonRequest($request);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://scl-sandbox.dev.clover.com/v1/charges');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{"amount":' . $request->amount . ',"currency":"usd","source":"' . $request->token . '"}');

            $headers = [];
            $headers[] = 'Accept: application/json';
            $headers[] = 'Authorization: Bearer 859c0171-ee8b-7c4b-7a07-3a02288fbc03';
            $headers[] = 'idempotency-key ' . $this->gen_uuid();
            $headers[] = 'Content-Type: application/json';

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);

            $response = json_decode($response);

            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            return response()->json($response, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function gen_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    private function validateJsonRequest(Request $request)
    {
        return $request->validate([
            'amount' => 'required',
            'token' => 'required',
        ]);
    }
}
