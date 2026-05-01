<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Recibo;
use App\Models\Transaccion;
use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PagoDirectoWebService
{
    public function create(Request $request)
    {
        $request->validate([
            'token'  => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $usuario  = Auth::user();
        $cliente  = Cliente::where('usuario', $usuario->id)->first();
        $clienteId = $cliente ? $cliente->id : null;

        $amountCentavos = number_format($request->amount * 100, 0, '', '');
        $nombreCliente  = $cliente ? $cliente->nombre : '';

        $payload = [
            'amount'      => $amountCentavos,
            'currency'    => 'usd',
            'source'      => $request->token,
            'description' => $nombreCliente,
        ];

        $pagoResponse = $this->creditCard($amountCentavos, $request->token, $nombreCliente);
        $rawResponse  = $pagoResponse->getContent();
        $pago         = json_decode($rawResponse);

        $this->saveTransaction($clienteId, $rawResponse, $pago, $payload);

        if (isset($pago->paid) && $pago->paid) {
            $recibo = Recibo::create([
                'cliente'       => $clienteId,
                'fecha'         => now(),
                'formaDePago'   => 2,
                'total'         => $pago->amount / 100,
                'anulado'       => 0,
                'observaciones' => 'Pago Clover web directo',
                'pedido'        => null,
                'garantia'      => 0,
            ]);

            return response()->json([
                'status'    => 200,
                'recibo_id' => $recibo->id,
                'monto'     => $pago->amount / 100,
                'fecha'     => now()->format('d/m/Y H:i'),
                'cliente'   => $nombreCliente,
            ], Response::HTTP_OK);
        }

        return response()->json(['error' => $pago], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function creditCard($amountCentavos, $token, $nombreCliente)
    {
        try {
            $ch = curl_init();

            $data = [
                'amount'      => $amountCentavos,
                'currency'    => 'usd',
                'source'      => $token,
                'description' => $nombreCliente,
            ];

            curl_setopt($ch, CURLOPT_URL, 'https://scl.clover.com/v1/charges');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $headers   = [];
            $headers[] = 'Accept: application/json';
            $headers[] = 'Authorization: Bearer 9f0919d8-6bc3-d88b-2bee-fcd1102b4b6a';
            $headers[] = 'idempotency-key ' . $this->gen_uuid();
            $headers[] = 'Content-Type: application/json';

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response      = curl_exec($ch);
            $cloverResponse = json_decode($response);

            if (curl_errno($ch)) {
                LogHelper::get(curl_error($ch));
            }
            curl_close($ch);

            return response()->json($cloverResponse, Response::HTTP_OK);
        } catch (\Exception $e) {
            LogHelper::get($e->getMessage());
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function saveTransaction($clienteId, $rawResponse, $pago, $payload = null)
    {
        $transaccion            = new Transaccion;
        $transaccion->fecha     = now();
        $transaccion->cliente   = $clienteId;
        $transaccion->pedido    = 0;
        $transaccion->resultado = json_encode($pago);
        $transaccion->ctr       = $rawResponse;
        $transaccion->payload   = $payload ? json_encode($payload) : null;
        $transaccion->save();
    }

    private function gen_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
